<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Pengembalian;
use App\Models\Peminjaman;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PengembalianController extends Controller
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
        $pengembalians = Pengembalian::select('id', 'peminjaman_id', 'user_id', 'tanggal_kembali_aktual', 'kondisi_kembali', 'denda', 'status')
            ->with(['peminjaman:id,user_id', 'peminjaman.user:id,nama_lengkap', 'user:id,nama_lengkap'])
            ->whereBetween('tanggal_kembali_aktual', [$tanggalAwal, $tanggalAkhir])
            ->latest()
            ->get();

        return view('pages.pengembalians.index', compact('pengembalians', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function create(): View
    {
        $peminjamans = Peminjaman::select('id', 'user_id')->with('user:id,nama_lengkap')
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
        $peminjamans = Peminjaman::select('id', 'user_id')->with('user:id,nama_lengkap')->orderByDesc('id')->get();

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
        if ($pengembalian->status === 'diterima') {
            $peminjaman = Peminjaman::findOrFail($pengembalian->peminjaman_id);
            $details = $peminjaman->detailPeminjamans()->get();
            $alatById = Alat::whereIn('id', $details->pluck('alat_id'))->get()->keyBy('id');

            foreach ($details as $detail) {
                $alat = $alatById->get($detail->alat_id);
                if (! $alat || $alat->stok < $detail->jumlah) {
                    throw ValidationException::withMessages([
                        'pengembalian' => 'Data ini tidak dapat dihapus karena stok alat sudah tidak mencukupi untuk membatalkan pengembalian.',
                    ]);
                }
            }
            foreach ($details as $detail) {
                $alatById->get($detail->alat_id)->decrement('stok', $detail->jumlah);
            }
            $peminjaman->update(['status' => 'dipinjam']);
        }
        $pengembalian->delete();

        return redirect()->route('pengembalians.index')->with('success', 'Data pengembalian berhasil dihapus.');
    }

    public function verify(Request $request, Pengembalian $pengembalian): RedirectResponse
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:diterima,ditolak'],
        ]);

        if ($pengembalian->status !== 'pending') {
            throw ValidationException::withMessages(['keputusan' => 'Pengembalian ini sudah diverifikasi.']);
        }

        $peminjaman = Peminjaman::findOrFail($pengembalian->peminjaman_id);
        if ($validated['keputusan'] === 'diterima') {
            if ($peminjaman->status !== 'dipinjam') {
                throw ValidationException::withMessages(['keputusan' => 'Peminjaman ini tidak sedang aktif.']);
            }

            $details = $peminjaman->detailPeminjamans()->get();
            $alatById = Alat::whereIn('id', $details->pluck('alat_id'))->get()->keyBy('id');
            foreach ($details as $detail) {
                $alat = $alatById->get($detail->alat_id);
                if (! $alat) {
                    throw ValidationException::withMessages(['keputusan' => 'Alat tidak ditemukan.']);
                }
            }
            foreach ($details as $detail) {
                $alatById->get($detail->alat_id)->increment('stok', $detail->jumlah);
            }
            $peminjaman->update(['status' => 'dikembalikan']);
        }
        $pengembalian->update([
            'status' => $validated['keputusan'],
            'user_id' => auth()->id(),
        ]);

        LogAktivitas::catat(
            'Pengembalian',
            ($validated['keputusan'] === 'diterima' ? 'Memverifikasi' : 'Menolak') . ' pengembalian: #' . $pengembalian->id,
        );

        return back()->with('success', 'Verifikasi pengembalian berhasil disimpan.');
    }

    public function myIndex(Request $request): View
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
        $peminjamans = Peminjaman::with(['detailPeminjamans.alat:id,nama_alat', 'pengembalians'])
            ->milikUser((int) auth()->id())->status('dipinjam')
            ->whereBetween('tanggal_pinjam', [$tanggalAwal, $tanggalAkhir])
            ->latest()
            ->get();

        return view('pages.pengembalians.mine', compact('peminjamans', 'tanggalAwal', 'tanggalAkhir'));
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
