@extends('layouts.app')

@section('title', 'Laporan Transaksi')

@push('styles')
<style>
    @media print {
        .sidebar, .navbar, footer, .no-print { display: none !important; }
        #wrapper .main { width: 100% !important; margin: 0 !important; padding: 0 !important; }
        .main-content { padding: 0 !important; }
        .panel { box-shadow: none !important; border: 0 !important; }
        .panel-heading { padding-left: 0 !important; }
    }
</style>
@endpush

@section('content')
<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="panel-title">Laporan Peminjaman dan Pengembalian</h3>
        <button type="button" class="btn btn-primary btn-sm no-print" onclick="window.print()">
            <i class="glyphicon glyphicon-print"></i> Cetak Laporan
        </button>
    </div>
    <div class="panel-body">
        <h4>Transaksi Peminjaman</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead><tr><th>ID</th><th>Peminjam</th><th>Alat</th><th>Tanggal Pinjam</th><th>Rencana Kembali</th><th>Status</th></tr></thead>
                <tbody>
                    @forelse($peminjamans as $peminjaman)
                        <tr>
                            <td>#{{ $peminjaman->id }}</td>
                            <td>{{ $peminjaman->user?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $peminjaman->detailPeminjamans->map(fn ($detail) => ($detail->alat?->nama_alat ?? 'Alat dihapus') . ' × ' . $detail->jumlah)->join(', ') ?: '-' }}</td>
                            <td>{{ $peminjaman->tanggal_pinjam?->format('d M Y') }}</td>
                            <td>{{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') }}</td>
                            <td>{{ ucfirst($peminjaman->status) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center">Belum ada transaksi peminjaman.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <h4>Transaksi Pengembalian</h4>
        <div class="table-responsive">
            <table class="table table-bordered table-striped">
                <thead><tr><th>ID</th><th>Peminjam</th><th>Tanggal Kembali</th><th>Kondisi</th><th>Denda</th><th>Status Verifikasi</th><th>Petugas</th></tr></thead>
                <tbody>
                    @forelse($pengembalians as $pengembalian)
                        <tr>
                            <td>#{{ $pengembalian->peminjaman_id }}</td>
                            <td>{{ $pengembalian->peminjaman?->user?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $pengembalian->tanggal_kembali_aktual?->format('d M Y') }}</td>
                            <td>{{ ucfirst($pengembalian->kondisi_kembali) }}</td>
                            <td>Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($pengembalian->status) }}</td>
                            <td>{{ $pengembalian->user?->nama_lengkap ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Belum ada transaksi pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection