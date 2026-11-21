@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="panel-title">Daftar Peminjaman</h3>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('peminjamans.create') }}" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Peminjaman</a>
        @endif
    </div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="GET" action="{{ route('peminjamans.index') }}" class="form-inline" style="margin-bottom:15px;">
            <div class="form-group"><label for="tanggal_awal">Dari</label> <input id="tanggal_awal" type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal }}"></div>
            <div class="form-group"><label for="tanggal_akhir">Sampai</label> <input id="tanggal_akhir" type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}"></div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('peminjamans.index') }}" class="btn btn-default">Reset</a>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead><tr><th>Peminjam</th><th>Alat</th><th>Jumlah</th><th>Tanggal Pinjam</th><th>Rencana Kembali</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($peminjamans as $peminjaman)
                        @php
                            $statusClass = match ($peminjaman->status) {
                                'pending' => 'label-warning',
                                'dipinjam' => 'label-info',
                                'dikembalikan' => 'label-success',
                                'ditolak' => 'label-danger',
                                default => 'label-default',
                            };
                        @endphp
                        <tr>
                            <td>{{ $peminjaman->user?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $peminjaman->detailPeminjamans->map(fn ($detail) => $detail->alat?->nama_alat ?? 'Alat dihapus')->join(', ') ?: '-' }}</td>
                            <td>{{ $peminjaman->detailPeminjamans->sum('jumlah') }}</td>
                            <td>{{ $peminjaman->tanggal_pinjam?->format('d M Y') }}</td>
                            <td>{{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') }}</td>
                            <td><span class="label {{ $statusClass }}">{{ ucfirst($peminjaman->status) }}</span></td>
                            <td>
                                @if($peminjaman->status === 'pending' && in_array(auth()->user()->role, ['admin', 'petugas'], true))
                                    <form method="POST" action="{{ route('peminjamans.decide', $peminjaman) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="dipinjam">
                                        <button class="btn btn-success btn-sm" type="submit">Setujui</button>
                                    </form>
                                    <form method="POST" action="{{ route('peminjamans.decide', $peminjaman) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="ditolak">
                                        <button class="btn btn-danger btn-sm" type="submit">Tolak</button>
                                    </form>
                                @endif
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('peminjamans.edit', $peminjaman) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('peminjamans.destroy', $peminjaman) }}" class="d-inline" onsubmit="return confirm('Hapus pengajuan ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-default btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Belum ada data peminjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
