<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use Illuminate\Http\Request;
use Illuminate\View\View;

class DetailPeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        $request->merge([
            'tanggal_awal' => $request->input('tanggal_awal', now()->startOfMonth()->toDateString()),
            'tanggal_akhir' => $request->input('tanggal_akhir', now()->toDateString()),
        ]);
        $tanggal = $request->validate([
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
        ]);
        $tanggalAwal = $tanggal['tanggal_awal'];
        $tanggalAkhir = $tanggal['tanggal_akhir'];
        $details = DetailPeminjaman::select('id', 'peminjaman_id', 'alat_id', 'jumlah', 'kondisi_awal')
            ->with([
                'peminjaman:id,user_id,status,tanggal_pinjam',
                'peminjaman.user:id,nama_lengkap',
                'alat:id,nama_alat',
            ])
            ->whereHas('peminjaman', fn ($query) => $query->whereBetween('tanggal_pinjam', [$tanggalAwal, $tanggalAkhir]))
            ->latest()
            ->get();

        return view('pages.detail-peminjamans.index', compact('details', 'tanggalAwal', 'tanggalAkhir'));
    }
}
