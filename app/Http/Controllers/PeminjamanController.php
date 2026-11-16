<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        $peminjamans = \App\Models\Peminjaman::query()->paginate(15);
        return view('pages.peminjamans.index', ['peminjamans' => $peminjamans]);
    }

    public function create(): View
    {
        // TODO: order dropdown by display column when metadata available
        $users = \App\Models\User::query()->orderBy('id')->get();
        return view('pages.peminjamans.create', compact('users'));
    }

    // TODO: extract to FormRequest if business logic grows
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id'], 'tanggal_pinjam' => ['required', 'date'], 'rencana_kembali' => ['required', 'date'], 'status' => ['string']]);
        \App\Models\Peminjaman::create($validated);
        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman created successfully.');
    }

    public function edit(\App\Models\Peminjaman $peminjaman): View
    {
        // TODO: order dropdown by display column when metadata available
        $users = \App\Models\User::query()->orderBy('id')->get();
        return view('pages.peminjamans.edit', compact('peminjaman', 'users'));
    }

    // TODO: extract to FormRequest if business logic grows
    public function update(Request $request, \App\Models\Peminjaman $peminjaman): RedirectResponse
    {
        $validated = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id'], 'tanggal_pinjam' => ['required', 'date'], 'rencana_kembali' => ['required', 'date'], 'status' => ['string']]);
        $peminjaman->update($validated);
        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman updated successfully.');
    }

    public function destroy(\App\Models\Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->delete();
        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman deleted successfully.');
    }
}
