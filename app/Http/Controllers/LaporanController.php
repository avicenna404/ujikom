<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->latest()
            ->get();

        $pengembalians = Pengembalian::with(['peminjaman.user', 'user'])
            ->latest()
            ->get();

        return view('pages.laporan.index', compact('peminjamans', 'pengembalians'));
    }
}