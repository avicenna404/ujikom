<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LogAktivitasController extends Controller
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
        $logs = LogAktivitas::with('user:id,nama_lengkap')
            ->whereDate('created_at', '>=', $tanggalAwal)
            ->whereDate('created_at', '<=', $tanggalAkhir)
            ->latest()
            ->get();

        return view('pages.log-aktivitas.index', compact('logs', 'tanggalAwal', 'tanggalAkhir'));
    }
}
