<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(): View
    {
        $pengembalians = \App\Models\Pengembalian::query()->paginate(15);
        return view('pages.pengembalians.index', ['pengembalians' => $pengembalians]);
    }

    public function create(): View
    {
        // TODO: order dropdown by display column when metadata available
        $peminjamans = \App\Models\Peminjaman::query()->orderBy('id')->get();
        // TODO: order dropdown by display column when metadata available
        $users = \App\Models\User::query()->orderBy('id')->get();
        return view('pages.pengembalians.create', compact('peminjamans', 'users'));
    }

    // TODO: extract to FormRequest if business logic grows
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['peminjaman_id' => ['required', 'integer', 'exists:peminjamans,id'], 'user_id' => ['nullable', 'integer', 'exists:users,id'], 'tanggal_kembali_aktual' => ['required', 'date'], 'kondisi_kembali' => ['string'], 'denda' => [], 'keterangan' => ['nullable', 'string']]);
        \App\Models\Pengembalian::create($validated);
        return redirect()->route('pengembalians.index')->with('success', 'Pengembalian created successfully.');
    }

    public function edit(\App\Models\Pengembalian $pengembalian): View
    {
        // TODO: order dropdown by display column when metadata available
        $peminjamans = \App\Models\Peminjaman::query()->orderBy('id')->get();
        // TODO: order dropdown by display column when metadata available
        $users = \App\Models\User::query()->orderBy('id')->get();
        return view('pages.pengembalians.edit', compact('pengembalian', 'peminjamans', 'users'));
    }

    // TODO: extract to FormRequest if business logic grows
    public function update(Request $request, \App\Models\Pengembalian $pengembalian): RedirectResponse
    {
        $validated = $request->validate(['peminjaman_id' => ['required', 'integer', 'exists:peminjamans,id'], 'user_id' => ['nullable', 'integer', 'exists:users,id'], 'tanggal_kembali_aktual' => ['required', 'date'], 'kondisi_kembali' => ['string'], 'denda' => [], 'keterangan' => ['nullable', 'string']]);
        $pengembalian->update($validated);
        return redirect()->route('pengembalians.index')->with('success', 'Pengembalian updated successfully.');
    }

    public function destroy(\App\Models\Pengembalian $pengembalian): RedirectResponse
    {
        $pengembalian->delete();
        return redirect()->route('pengembalians.index')->with('success', 'Pengembalian deleted successfully.');
    }
}
