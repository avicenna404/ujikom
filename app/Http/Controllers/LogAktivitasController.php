<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LogAktivitas;
use Illuminate\View\View;

class LogAktivitasController extends Controller
{
    public function index(): View
    {
        $logs = LogAktivitas::with('user')
            ->latest()
            ->paginate(15);

        return view('pages.log-aktivitas.index', compact('logs'));
    }
}