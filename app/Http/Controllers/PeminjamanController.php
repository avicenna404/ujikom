<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Alat;
use App\Models\LogAktivitas;
use App\Models\Peminjaman;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PeminjamanController extends Controller
{
    public function index(Request $request): View
    {
        [$tanggalAwal, $tanggalAkhir] = $this->tanggalFilter($request);
        $peminjamans = Peminjaman::with(['user:id,nama_lengkap', 'detailPeminjamans.alat:id,nama_alat'])
            ->when(auth()->user()->role === 'peminjam', fn ($query) => $query->milikUser((int) auth()->id()))
            ->whereBetween('tanggal_pinjam', [$tanggalAwal, $tanggalAkhir])
            ->latest()
            ->get();

        return view('pages.peminjamans.index', compact('peminjamans', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function create(): View
    {
        $users = User::select('id', 'nama_lengkap', 'username')->orderBy('nama_lengkap')->get();
        $alats = Alat::select('id', 'nama_alat', 'stok')->orderBy('nama_alat')->get();

        return view('pages.peminjamans.create', compact('users', 'alats'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['nullable', 'string', 'max:1000'],
            'alat_id' => ['required', 'array', 'min:1'],
            'alat_id.*' => ['required', 'integer', 'distinct'],
            'jumlah' => ['required', 'array'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
        ], ['alat_id.*.distinct' => 'Alat yang sama tidak boleh dipilih lebih dari satu kali.']);

        $this->validateItems($validated['alat_id'], $validated['jumlah']);
        $peminjaman = Peminjaman::create([
            'user_id' => $validated['user_id'],
            'tanggal_pinjam' => $validated['tanggal_pinjam'],
            'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            'keperluan' => $validated['keperluan'] ?? null,
            'status' => 'pending',
        ]);
        $peminjaman->detailPeminjamans()->createMany($this->detailAttributes($validated['alat_id'], $validated['jumlah']));

        LogAktivitas::catat('Peminjaman', 'Membuat pengajuan peminjaman: #' . $peminjaman->id);

        return redirect()->route('peminjamans.index')->with('success', 'Pengajuan peminjaman berhasil dibuat.');
    }

    public function edit(Peminjaman $peminjaman): View
    {

        $users = User::select('id', 'nama_lengkap')->orderBy('nama_lengkap')->get();

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
        if ($peminjaman->status === 'dipinjam') {
            $details = $peminjaman->detailPeminjamans()->get();
            $alatById = Alat::whereIn('id', $details->pluck('alat_id'))->get()->keyBy('id');

            foreach ($details as $detail) {
                $alatById->get($detail->alat_id)?->increment('stok', $detail->jumlah);
            }
        }
        $peminjaman->delete();

        return redirect()->route('peminjamans.index')->with('success', 'Peminjaman berhasil dihapus.');
    }

    public function decide(Request $request, Peminjaman $peminjaman): RedirectResponse
    {
        $validated = $request->validate([
            'keputusan' => ['required', 'in:dipinjam,ditolak'],
        ]);

        if ($peminjaman->status !== 'pending') {
            throw ValidationException::withMessages(['keputusan' => 'Pengajuan ini sudah diproses.']);
        }

        if ($validated['keputusan'] === 'dipinjam') {
            $details = $peminjaman->detailPeminjamans()->get();
            if ($details->isEmpty()) {
                throw ValidationException::withMessages(['keputusan' => 'Pengajuan tidak memiliki rincian alat.']);
            }

            $alatById = Alat::whereIn('id', $details->pluck('alat_id'))->get()->keyBy('id');
            foreach ($details as $detail) {
                $alat = $alatById->get($detail->alat_id);
                if (! $alat || $alat->stok < $detail->jumlah) {
                    throw ValidationException::withMessages([
                        'keputusan' => 'Stok ' . ($alat?->nama_alat ?? 'alat') . ' tidak mencukupi untuk menyetujui pengajuan.',
                    ]);
                }
            }
            foreach ($details as $detail) {
                $alatById->get($detail->alat_id)->decrement('stok', $detail->jumlah);
            }
        }
        $peminjaman->update(['status' => $validated['keputusan']]);

        LogAktivitas::catat(
            'Peminjaman',
            ($validated['keputusan'] === 'dipinjam' ? 'Menyetujui' : 'Menolak') . ' peminjaman: #' . $peminjaman->id,
        );

        return back()->with('success', 'Keputusan peminjaman berhasil disimpan.');
    }

    public function myIndex(Request $request): View
    {
        [$tanggalAwal, $tanggalAkhir] = $this->tanggalFilter($request);
        $peminjamans = Peminjaman::with('detailPeminjamans.alat:id,nama_alat')->milikUser((int) auth()->id())
            ->whereBetween('tanggal_pinjam', [$tanggalAwal, $tanggalAkhir])
            ->latest()
            ->get();

        return view('pages.peminjamans.mine', compact('peminjamans', 'tanggalAwal', 'tanggalAkhir'));
    }

    public function requestCreate(): View
    {
        $alats = Alat::select('id', 'nama_alat', 'stok')->where('stok', '>', 0)->orderBy('nama_alat')->get();

        return view('pages.peminjamans.request', compact('alats'));
    }

    public function requestStore(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_kembali_rencana' => ['required', 'date', 'after_or_equal:today'],
            'keperluan' => ['nullable', 'string', 'max:1000'],
            'alat_id' => ['required', 'array', 'min:1'],
            'alat_id.*' => ['required', 'integer', 'distinct'],
            'jumlah' => ['required', 'array'],
            'jumlah.*' => ['required', 'integer', 'min:1'],
        ], ['alat_id.*.distinct' => 'Alat yang sama tidak boleh dipilih lebih dari satu kali.']);

        $this->validateItems($validated['alat_id'], $validated['jumlah']);
        $peminjaman = Peminjaman::create([
            'user_id' => auth()->id(),
            'tanggal_pinjam' => now()->toDateString(),
            'tanggal_kembali_rencana' => $validated['tanggal_kembali_rencana'],
            'keperluan' => $validated['keperluan'] ?? null,
            'status' => 'pending',
        ]);
        $peminjaman->detailPeminjamans()->createMany($this->detailAttributes($validated['alat_id'], $validated['jumlah']));

        LogAktivitas::catat('Peminjaman', 'Mengajukan peminjaman: #' . $peminjaman->id);

        return redirect()->route('peminjaman-saya')->with('success', 'Pengajuan Anda dikirim untuk ditinjau petugas.');
    }

    private function validateItems(array $alatIds, array $jumlahs): void
    {
        if (count($alatIds) !== count($jumlahs)) {
            throw ValidationException::withMessages(['jumlah' => 'Masukkan jumlah untuk setiap jenis alat yang dipilih.']);
        }

        $alatById = Alat::whereIn('id', $alatIds)->get()->keyBy('id');
        $this->ensureAlatExists($alatIds, $alatById);
        foreach ($alatIds as $index => $alatId) {
            $alat = $alatById->get($alatId);
            $kuota = min(3, max(1, (int) floor($alat->stok * 0.5)));
            if ((int) ($jumlahs[$index] ?? 0) > $kuota) {
                throw ValidationException::withMessages(["jumlah.$index" => "Jumlah {$alat->nama_alat} melebihi kuota {$kuota} unit (maksimal 3 unit atau 50% stok tersedia)."]);
            }
        }
    }

    private function ensureAlatExists(array $alatIds, $alatById): void
    {
        if ($alatById->count() !== count($alatIds)) {
            throw ValidationException::withMessages(['alat_id' => 'Salah satu alat yang dipilih sudah tidak tersedia. Muat ulang halaman dan coba kembali.']);
        }
    }

    private function detailAttributes(array $alatIds, array $jumlahs): array
    {
        return collect($alatIds)->values()->map(fn ($id, $index) => [
            'alat_id' => $id,
            'jumlah' => $jumlahs[$index],
            'kondisi_awal' => 'baik',
        ])->all();
    }

    private function tanggalFilter(Request $request): array
    {
        $request->merge([
            'tanggal_awal' => $request->input('tanggal_awal', now()->startOfMonth()->toDateString()),
            'tanggal_akhir' => $request->input('tanggal_akhir', now()->toDateString()),
        ]);
        $tanggal = $request->validate([
            'tanggal_awal' => ['required', 'date'],
            'tanggal_akhir' => ['required', 'date', 'after_or_equal:tanggal_awal'],
        ]);

        return [$tanggal['tanggal_awal'], $tanggal['tanggal_akhir']];
    }
}
