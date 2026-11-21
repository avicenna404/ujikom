<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LaporanController extends Controller
{
    public function index(Request $request): View
    {
        $request->merge([
            'tanggal_awal' => $request->input('tanggal_awal', now()->startOfMonth()->toDateString()),
            'tanggal_akhir' => $request->input('tanggal_akhir', now()->toDateString()),
        ]);
        $validated = $request->validate([
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
        ]);
        $tanggalAwal = $validated['tanggal_awal'];
        $tanggalAkhir = $validated['tanggal_akhir'];
        $peminjamans = Peminjaman::with(['user:id,nama_lengkap', 'detailPeminjamans.alat:id,nama_alat'])
            ->whereBetween('tanggal_pinjam', [$tanggalAwal, $tanggalAkhir])
            ->latest()
            ->get();

        $pengembalians = Pengembalian::with(['peminjaman:id,user_id', 'peminjaman.user:id,nama_lengkap', 'user:id,nama_lengkap'])
            ->whereBetween('tanggal_kembali_aktual', [$tanggalAwal, $tanggalAkhir])
            ->latest()
            ->get();

        return view('pages.laporan.index', compact('peminjamans', 'pengembalians', 'tanggalAwal', 'tanggalAkhir'));
    }
}
