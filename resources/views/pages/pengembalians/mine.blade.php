@extends('layouts.app')

@section('title', 'Pengembalian Saya')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Pengembalian Alat Saya</h3></div>
    <div class="panel-body">
        <form method="GET" action="{{ route('pengembalian-saya') }}" class="form-inline" style="margin-bottom:15px;">
            <div class="form-group"><label for="tanggal_awal">Dari</label> <input id="tanggal_awal" type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal }}"></div>
            <div class="form-group"><label for="tanggal_akhir">Sampai</label> <input id="tanggal_akhir" type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}"></div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('pengembalian-saya') }}" class="btn btn-default">Reset</a>
        </form>
        @forelse($peminjamans as $peminjaman)
            @php($pendingReturn = $peminjaman->pengembalians->firstWhere('status', 'pending'))
            <div class="panel panel-default">
                <div class="panel-heading">
                    <strong>Transaksi #{{ $peminjaman->id }}</strong>
                    <span class="text-muted">Rencana kembali: {{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') }}</span>
                </div>
                <div class="panel-body">
                    <p>Alat: {{ $peminjaman->detailPeminjamans->map(fn ($detail) => ($detail->alat?->nama_alat ?? 'Alat dihapus') . ' (' . $detail->jumlah . ')')->join(', ') }}</p>
                    <p>Estimasi denda jika dikembalikan hari ini: Rp{{ number_format($peminjaman->hitungDendaTerlambat(today()->toDateString()), 0, ',', '.') }}</p>
                    @if($pendingReturn)
                        <p><span class="label label-warning">Menunggu verifikasi</span> Pengajuan tanggal {{ $pendingReturn->tanggal_kembali_aktual?->format('d M Y') }}.</p>
                    @else
                        <form method="POST" action="{{ route('pengembalian-saya.store') }}" class="form-inline">
                            @csrf
                            <input type="hidden" name="peminjaman_id" value="{{ $peminjaman->id }}">
                            <div class="form-group">
                                <label for="tanggal_{{ $peminjaman->id }}">Tanggal Kembali</label>
                                <input id="tanggal_{{ $peminjaman->id }}" name="tanggal_kembali_aktual" type="date" class="form-control" value="{{ today()->format('Y-m-d') }}" required>
                            </div>
                            <div class="form-group">
                                <label for="kondisi_{{ $peminjaman->id }}">Kondisi Alat</label>
                                <select id="kondisi_{{ $peminjaman->id }}" name="kondisi_kembali" class="form-control" required>
                                    <option value="baik">Baik</option>
                                    <option value="rusak ringan">Rusak ringan</option>
                                    <option value="rusak berat">Rusak berat</option>
                                    <option value="hilang">Hilang</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary">Ajukan Pengembalian</button>
                        </form>
                    @endif
                </div>
            </div>
        @empty
            <p>Anda tidak memiliki alat yang sedang dipinjam.</p>
        @endforelse
    </div>
</div>
@endsection
