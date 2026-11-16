<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(): View
    {
        $peminjamans = Peminjaman::with(['user', 'detailPeminjamans.alat'])
            ->latest()
            ->paginate(15);

        return view('pages.peminjamans.index', compact('peminjamans'));
    }

    public function create(): View
    {
        $users = User::orderBy('nama_lengkap')->get();
        $alats = Alat::orderBy('nama_alat')->get();

        return view('pages.peminjamans.create', compact('users', 'alats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['nullable', 'string', 'max:1000'],
            'alat_id' => ['required', 'exists:alats,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $peminjaman = DB::transaction(function () use ($validated): Peminjaman {
            $peminjaman = Peminjaman::create([
                'user_id' => $validated['user_id'],
                'tanggal_pinjam' => $validated['tanggal_pinjam'],
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan' => $validated['keperluan'] ?? null,
                'status' => 'pending',
            ]);

            $peminjaman->detailPeminjamans()->create([
                'alat_id' => $validated['alat_id'],
                'jumlah' => $validated['jumlah'],
                'kondisi_awal' => 'baik',
            ]);

            return $peminjaman;
        });

        LogAktivitas::catat('Peminjaman', 'Membuat pengajuan peminjaman: #' . $peminjaman->id);

        return redirect()->route('peminjamans.index')->with('success', 'Pengajuan peminjaman berhasil dibuat.');
    }

    public function edit(Peminjaman $peminjaman): View
    {

        $users = User::orderBy('nama_lengkap')->get();

        return view('pages.peminjamans.edit', compact('peminjaman', 'users'));
    }

    public function update(Request $request, Peminjaman $peminjaman): RedirectResponse
    {

        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['nullable', 'string', 'max:1000'],
        ]);

        $peminjaman->update($validated);

        return redirect()->route('peminjamans.index')->with('success', 'Pengajuan peminjaman berhasil diperbarui.');
    }

    public function destroy(Peminjaman $peminjaman): RedirectResponse
    {
        DB::transaction(function () use ($peminjaman): void {
            if ($peminjaman->status === 'dipinjam') {
                $peminjaman->load('detailPeminjamans');

                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->first();
                    if ($alat) {
                        $alat->increment('stok', $detail->jumlah);
                    }
                }
            }

            $peminjaman->delete();
        });

        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman berhasil dihapus.');
    }

    public function decide(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:dipinjam,ditolak'],
        ]);

        DB::transaction(function () use ($peminjaman, $validated): void {
            $peminjaman = Peminjaman::whereKey($peminjaman->id)->lockForUpdate()->firstOrFail();

            if ($peminjaman->status !== 'pending') {
                throw ValidationException::withMessages(['keputusan' => 'Pengajuan ini sudah diproses.']);
            }

            if ($validated['keputusan'] === 'dipinjam') {
                $details = $peminjaman->detailPeminjamans()->get();

                if ($details->isEmpty()) {
                    throw ValidationException::withMessages(['keputusan' => 'Pengajuan tidak memiliki rincian alat.']);
                }

                foreach ($details as $detail) {
                    $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();

                    if ($alat->stok < $detail->jumlah) {
                        throw ValidationException::withMessages([
                            'keputusan' => 'Stok ' . $alat->nama_alat . ' tidak mencukupi untuk menyetujui pengajuan.',
                        ]);
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }
            }

            $peminjaman->update(['status' => $validated['keputusan']]);
        });

        LogAktivitas::catat(
            'Peminjaman',
            ($validated['keputusan'] === 'dipinjam' ? 'Menyetujui' : 'Menolak') . ' peminjaman: #' . $peminjaman->id,
        );

        return back()->with('success', 'Keputusan peminjaman berhasil disimpan.');
    }

    public function myIndex(): View
    {
        $peminjamans = Peminjaman::with('detailPeminjamans.alat')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(15);

        return view('pages.peminjamans.mine', compact('peminjamans'));
    }

    public function requestCreate(): View
    {
        $alats = Alat::where('stok', '>', 0)->orderBy('nama_alat')->get();

        return view('pages.peminjamans.request', compact('alats'));
    }

    public function requestStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:today'],
            'keperluan' => ['nullable', 'string', 'max:1000'],
            'alat_id' => ['required', 'exists:alats,id'],
            'jumlah' => ['required', 'integer', 'min:1'],
        ]);

        $peminjaman = DB::transaction(function () use ($validated): Peminjaman {
            $peminjaman = Peminjaman::create([
                'user_id' => auth()->id(),
                'tanggal_pinjam' => today(),
                'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
                'keperluan' => $validated['keperluan'] ?? null,
                'status' => 'pending',
            ]);

            $peminjaman->detailPeminjamans()->create([
                'alat_id' => $validated['alat_id'],
                'jumlah' => $validated['jumlah'],
                'kondisi_awal' => 'baik',
            ]);

            return $peminjaman;
        });

        LogAktivitas::catat('Peminjaman', 'Mengajukan peminjaman: #' . $peminjaman->id);

        return redirect()->route('peminjaman-saya')->with('success', 'Pengajuan Anda dikirim untuk ditinjau petugas.');
    }
}
