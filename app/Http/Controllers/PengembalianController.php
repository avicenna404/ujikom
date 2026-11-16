<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PengembalianController extends Controller
{
    public function index(): View
    {
        $pengembalians = Pengembalian::with(['peminjaman.user', 'user'])
            ->latest()
            ->paginate(15);

        return view('pages.pengembalians.index', compact('pengembalians'));
    }

    public function create(): View
    {
        $peminjamans = Peminjaman::with('user')
            ->where('status', 'dipinjam')
            ->orderByDesc('id')
            ->get();

        return view('pages.pengembalians.create', compact('peminjamans'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjamans,id'],
            'tanggal_kembali_aktual' => ['required', 'date'],
            'kondisi_kembali' => ['required', 'in:baik,rusak ringan,rusak berat,hilang'],
            'denda' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $loanIsActive = Peminjaman::whereKey($validated['peminjaman_id'])
            ->where('status', 'dipinjam')
            ->exists();

        if (! $loanIsActive) {
            throw ValidationException::withMessages([
                'peminjaman_id' => 'Pengembalian hanya dapat dibuat untuk peminjaman yang sedang aktif.',
            ]);
        }
        $pendingExists = Pengembalian::where('peminjaman_id', $validated['peminjaman_id'])
            ->where('status', 'pending')
            ->exists();

        if ($pendingExists) {
            throw ValidationException::withMessages([
                'peminjaman_id' => 'Sudah ada pengajuan pengembalian yang menunggu verifikasi.',
            ]);
        }
        Pengembalian::create($validated + [
            'status' => 'pending',
            'user_id' => null,
        ]);

        return redirect()->route('pengembalians.index')
            ->with('success', 'Data pengembalian dibuat dan menunggu verifikasi petugas.');
    }

    public function edit(Pengembalian $pengembalian): View
    {
        $peminjamans = Peminjaman::with('user')->orderByDesc('id')->get();

        return view('pages.pengembalians.edit', compact('pengembalian', 'peminjamans'));
    }

    public function update(Request $request, Pengembalian $pengembalian): RedirectResponse
    {
        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjamans,id'],
            'tanggal_kembali_aktual' => ['required', 'date'],
            'kondisi_kembali' => ['required', 'in:baik,rusak ringan,rusak berat,hilang'],
            'denda' => ['required', 'integer', 'min:0'],
            'keterangan' => ['nullable', 'string'],
        ]);

        if ($pengembalian->status === 'diterima' && (int) $validated['peminjaman_id'] !== $pengembalian->peminjaman_id) {
            throw ValidationException::withMessages([
                'peminjaman_id' => 'Transaksi pengembalian yang sudah diverifikasi tidak dapat dipindahkan.',
            ]);
        }

        $pengembalian->update($validated);

        return redirect()->route('pengembalians.index')->with('success', 'Data pengembalian diperbarui.');
    }

    public function destroy(Pengembalian $pengembalian): RedirectResponse
    {
        DB::transaction(function () use ($pengembalian): void {
            $pengembalian = Pengembalian::whereKey($pengembalian->id)->lockForUpdate()->firstOrFail();

            if ($pengembalian->status === 'diterima') {
                $peminjaman = Peminjaman::whereKey($pengembalian->peminjaman_id)
                    ->lockForUpdate()
                    ->firstOrFail();

                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();

                    if ($alat->stok < $detail->jumlah) {
                        throw ValidationException::withMessages([
                            'pengembalian' => 'Data ini tidak dapat dihapus karena stok alat sudah tidak mencukupi untuk membatalkan pengembalian.',
                        ]);
                    }

                    $alat->decrement('stok', $detail->jumlah);
                }

                $peminjaman->update(['status' => 'dipinjam']);
            }

            $pengembalian->delete();
        });

        return redirect()->route('pengembalians.index')->with('success', 'Data pengembalian berhasil dihapus.');
    }

    public function verify(Request $request, Pengembalian $pengembalian): RedirectResponse
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:diterima,ditolak'],
        ]);

        DB::transaction(function () use ($pengembalian, $validated): void {
            $pengembalian = Pengembalian::whereKey($pengembalian->id)->lockForUpdate()->firstOrFail();

            if ($pengembalian->status !== 'pending') {
                throw ValidationException::withMessages(['keputusan' => 'Pengembalian ini sudah diverifikasi.']);
            }

            $peminjaman = Peminjaman::whereKey($pengembalian->peminjaman_id)
                ->lockForUpdate()
                ->firstOrFail();

            if ($validated['keputusan'] === 'diterima') {
                if ($peminjaman->status !== 'dipinjam') {
                    throw ValidationException::withMessages(['keputusan' => 'Peminjaman ini tidak sedang aktif.']);
                }

                foreach ($peminjaman->detailPeminjamans as $detail) {
                    $alat = Alat::whereKey($detail->alat_id)->lockForUpdate()->firstOrFail();
                    $alat->increment('stok', $detail->jumlah);
                }

                $peminjaman->update(['status' => 'dikembalikan']);
            }

            $pengembalian->update([
                'status' => $validated['keputusan'],
                'user_id' => auth()->id(),
            ]);
        });

        LogAktivitas::catat(
            'Pengembalian',
            ($validated['keputusan'] === 'diterima' ? 'Memverifikasi' : 'Menolak') . ' pengembalian: #' . $pengembalian->id,
        );

        return back()->with('success', 'Verifikasi pengembalian berhasil disimpan.');
    }

    public function myIndex(): View
    {
        $peminjamans = Peminjaman::with(['detailPeminjamans.alat', 'pengembalians'])
            ->where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->latest()
            ->get();

        return view('pages.pengembalians.mine', compact('peminjamans'));
    }

    public function storeByPeminjam(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'peminjaman_id' => ['required', 'exists:peminjamans,id'],
            'tanggal_kembali_aktual' => ['required', 'date'],
            'kondisi_kembali' => ['required', 'in:baik,rusak ringan,rusak berat,hilang'],
            'keterangan' => ['nullable', 'string'],
        ]);

        $peminjaman = Peminjaman::whereKey($validated['peminjaman_id'])
            ->where('user_id', auth()->id())
            ->where('status', 'dipinjam')
            ->firstOrFail();

        $hasPendingReturn = Pengembalian::where('peminjaman_id', $peminjaman->id)
            ->where('status', 'pending')
            ->exists();

        if ($hasPendingReturn) {
            throw ValidationException::withMessages([
                'peminjaman_id' => 'Pengajuan pengembalian Anda sedang menunggu verifikasi.',
            ]);
        }

        Pengembalian::create([
            'peminjaman_id' => $peminjaman->id,
            'user_id' => null,
            'tanggal_kembali_aktual' => $validated['tanggal_kembali_aktual'],
            'kondisi_kembali' => $validated['kondisi_kembali'],
            'denda' => 0,
            'keterangan' => $validated['keterangan'] ?? null,
            'status' => 'pending',
        ]);

        LogAktivitas::catat('Pengembalian', 'Mengajukan pengembalian untuk peminjaman: #' . $peminjaman->id);

        return redirect()->route('pengembalian-saya')->with('success', 'Pengajuan pengembalian dikirim kepada petugas.');
    }
}