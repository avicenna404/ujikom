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
        $alats = Alat::query()->paginate(15);
        return view('pages.alats.index', ['alats' => $alats]);
    }

    public function create(): View
    {
        $kategoris = Kategori::query()->orderBy('id')->get();
        return view('pages.alats.create', compact('kategoris'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['nama_alat' => ['required', 'string'], 'kategori_id' => ['required', 'integer', 'exists:kategoris,id'], 'stok' => ['required', 'integer'], 'kondisi' => ['required', 'string']]);
        $alat = Alat::create($validated);
        LogAktivitas::catat('Alat', 'Menambah alat baru: ' . $alat->nama_alat);

        return redirect()->route('alats.index')->with('success', 'Alat created successfully.');
    }

    public function edit(Alat $alat): View
    {
        $kategoris = Kategori::query()->orderBy('id')->get();
        return view('pages.alats.edit', compact('alat', 'kategoris'));
    }

    public function update(Request $request, Alat $alat): RedirectResponse
    {
        $validated = $request->validate(['nama_alat' => ['required', 'string'], 'kategori_id' => ['required', 'integer', 'exists:kategoris,id'], 'stok' => ['required', 'integer'], 'kondisi' => ['required', 'string']]);
        $alat->update($validated);
        return redirect()->route('alats.index')->with('success', 'Alat updated successfully.');
    }

    public function destroy(Alat $alat): RedirectResponse
    {
        $alat->delete();
        return redirect()->route('alats.index')->with('success', 'Alat deleted successfully.');
    }
}