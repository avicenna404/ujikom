<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\Kategori;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalKategori = Kategori::count();
        $totalAlat = Alat::sum('stok');
        $peminjamanAktif = Peminjaman::where('status', 'dipinjam')->count();
        $totalUser = User::count();

        $transaksiTerbaru = Peminjaman::select('id', 'user_id', 'status', 'tanggal_pinjam', 'tanggal_kembali_rencana')->with(['user:id,nama_lengkap', 'detailPeminjamans:id,peminjaman_id,alat_id,jumlah', 'detailPeminjamans.alat:id,nama_alat'])
            ->latest()
            ->take(5)
            ->get();

        $stokMenipis = Alat::select('id', 'nama_alat', 'stok')->where('stok', '<=', 3)->get();

        return view('dashboard', compact(
            'totalKategori',
            'totalAlat',
            'peminjamanAktif',
            'totalUser',
            'transaksiTerbaru',
            'stokMenipis',
        ));
    }
}
