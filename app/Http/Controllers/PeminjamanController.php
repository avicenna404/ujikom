<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::query()->paginate(15);
        return view('pages.peminjamans.index', ['peminjamans' => $peminjamans]);
    }

    public function create(): View
    {
        // TODO: order dropdown by display column when metadata available
        $users = User::query()->orderBy('id')->get();
        return view('pages.peminjamans.create', compact('users'));
    }

    // TODO: extract to FormRequest if business logic grows
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id'], 'tanggal_pinjam' => ['required', 'date'], 'rencana_kembali' => ['required', 'date'], 'status' => ['string']]);
        $peminjaman = Peminjaman::create($validated);
        LogAktivitas::catat('Peminjaman', 'Menambah peminjaman: #' . $peminjaman->id);

        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman created successfully.');
    }

    public function edit(Peminjaman $peminjaman): View
    {
        // TODO: order dropdown by display column when metadata available
        $users = User::query()->orderBy('id')->get();
        return view('pages.peminjamans.edit', compact('peminjaman', 'users'));
    }

    // TODO: extract to FormRequest if business logic grows
    public function update(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $validated = $request->validate(['user_id' => ['required', 'integer', 'exists:users,id'], 'tanggal_pinjam' => ['required', 'date'], 'rencana_kembali' => ['required', 'date'], 'status' => ['string']]);
        $peminjaman->update($validated);
        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman updated successfully.');
    }

    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        $peminjaman->delete();
        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman deleted successfully.');
    }
}