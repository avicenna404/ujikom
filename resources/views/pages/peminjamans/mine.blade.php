@extends('layouts.app')

@section('title', 'Peminjaman Saya')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="panel-title">Peminjaman Saya</h3>
        <a href="{{ route('peminjaman-ajukan.create') }}" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Ajukan Peminjaman</a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead><tr><th>No</th><th>Alat</th><th>Jumlah</th><th>Tanggal</th><th>Rencana Kembali</th><th>Status</th></tr></thead>
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
                            <td>#{{ $peminjaman->id }}</td>
                            <td>{{ $peminjaman->detailPeminjamans->map(fn ($detail) => $detail->alat?->nama_alat ?? 'Alat dihapus')->join(', ') }}</td>
                            <td>{{ $peminjaman->detailPeminjamans->sum('jumlah') }}</td>
                            <td>{{ $peminjaman->tanggal_pinjam?->format('d M Y') }}</td>
                            <td>{{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') }}</td>
                            <td><span class="label {{ $statusClass }}">{{ ucfirst($peminjaman->status) }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Anda belum memiliki pengajuan peminjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $peminjamans->links() }}
    </div>
</div>
@endsection