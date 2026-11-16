<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\DetailPeminjaman;
use Illuminate\View\View;

class DetailPeminjamanController extends Controller
{
    public function index(): View
    {
        $details = DetailPeminjaman::with(['peminjaman.user', 'alat'])
            ->latest()
            ->paginate(15);

        return view('pages.detail-peminjamans.index', compact('details'));
    }
}