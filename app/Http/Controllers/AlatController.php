<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\LogAktivitas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AlatController extends Controller
{
    public function index(): View
    {
        $alats = Alat::select('id', 'nama_alat', 'kategori_id', 'stok', 'kondisi')
            ->with('kategori:id,nama_kategori')
            ->paginate(15);

        return view('pages.alats.index', compact('alats'));
    }

    public function catalog(): View
    {
        $alats = Alat::select('id', 'nama_alat', 'kategori_id', 'stok', 'kondisi')
            ->with('kategori:id,nama_kategori')
            ->orderBy('nama_alat')
            ->paginate(15);

        return view('pages.alats.catalog', compact('alats'));
    }

    public function create(): View
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pages.alats.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'nama_alat' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:baik,rusak ringan,rusak berat'],
        ]);

        $alat = Alat::create($validated);
        LogAktivitas::catat('Alat', 'Menambah alat baru: ' . $alat->nama_alat);

        return redirect()->route('alats.index')->with('success', 'Alat berhasil ditambahkan.');
    }

    public function edit(Alat $alat): View
    {
        $kategoris = Kategori::orderBy('nama_kategori')->get();

        return view('pages.alats.edit', compact('alat', 'kategoris'));
    }

    public function update(Request $request, Alat $alat): RedirectResponse
    {
        $validated = $request->validate([
            'nama_alat' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'integer', 'exists:kategoris,id'],
            'stok' => ['required', 'integer', 'min:0'],
            'kondisi' => ['required', 'in:baik,rusak ringan,rusak berat'],
        ]);

        $alat->update($validated);

        return redirect()->route('alats.index')->with('success', 'Alat berhasil diperbarui.');
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        $alat->delete();

        return redirect()->route('alats.index')->with('success', 'Alat berhasil dihapus.');
    }
}
