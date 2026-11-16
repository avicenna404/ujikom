@extends('layouts.app')

@section('title', 'Dashboard Peminjaman')

@push('styles')
<style>
    .dashboard-metric {
        display: flex;
        align-items: center;
        justify-content: space-between;
        min-height: 82px;
    }
    .dashboard-metric .metric-value {
        color: #2B333E;
        font-size: 30px;
        font-weight: 700;
        line-height: 1.1;
    }
    .dashboard-metric .metric-label {
        color: #8d9095;
        font-size: 14px;
        margin-top: 6px;
    }
    .dashboard-metric .metric-icon {
        color: #00AAFF;
        font-size: 34px;
        margin-left: 12px;
    }
    .dashboard-panel-heading {
        display: flex;
        align-items: center;
        justify-content: space-between;
    }
    .dashboard-stock-list {
        margin-bottom: 15px;
    }
    .dashboard-stock-list .list-group-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-md-3 col-sm-6">
        <div class="panel panel-headline">
            <div class="panel-body metric dashboard-metric">
                <div>
                    <div class="metric-value">{{ number_format($totalKategori) }}</div>
                    <div class="metric-label">Total Kategori</div>
                </div>
                <i class="glyphicon glyphicon-tags metric-icon" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="panel panel-headline">
            <div class="panel-body metric dashboard-metric">
                <div>
                    <div class="metric-value">{{ number_format($totalAlat) }}</div>
                    <div class="metric-label">Total Stok Alat</div>
                </div>
                <i class="glyphicon glyphicon-wrench metric-icon" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="panel panel-headline">
            <div class="panel-body metric dashboard-metric">
                <div>
                    <div class="metric-value">{{ number_format($peminjamanAktif) }}</div>
                    <div class="metric-label">Sedang Dipinjam</div>
                </div>
                <i class="glyphicon glyphicon-transfer metric-icon" aria-hidden="true"></i>
            </div>
        </div>
    </div>
    <div class="col-md-3 col-sm-6">
        <div class="panel panel-headline">
            <div class="panel-body metric dashboard-metric">
                <div>
                    <div class="metric-value">{{ number_format($totalUser) }}</div>
                    <div class="metric-label">Total Pengguna</div>
                </div>
                <i class="glyphicon glyphicon-user metric-icon" aria-hidden="true"></i>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="panel">
            <div class="panel-heading dashboard-panel-heading">
                <h3 class="panel-title">Peminjaman Terbaru</h3>
                <a href="{{ url('/peminjamans') }}" class="btn btn-primary btn-sm">Lihat Semua</a>
            </div>
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Nama Peminjam</th>
                                <th>Tanggal Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transaksiTerbaru as $peminjaman)
                                @php
                                    $statusClass = match ($peminjaman->status) {
                                        'dipinjam' => 'label-warning',
                                        'dikembalikan' => 'label-success',
                                        default => 'label-default',
                                    };
                                @endphp
                                <tr>
                                    <td>{{ $peminjaman->user?->nama_lengkap ?? '-' }}</td>
                                    <td>{{ $peminjaman->tanggal_pinjam?->format('d M Y') ?? '-' }}</td>
                                    <td>{{ $peminjaman->tanggal_kembali_rencana?->format('d M Y') ?? $peminjaman->rencana_kembali?->format('d M Y') ?? '-' }}</td>
                                    <td><span class="label {{ $statusClass }}">{{ ucfirst($peminjaman->status ?? 'Tidak diketahui') }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">Belum ada transaksi peminjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="panel">
            <div class="panel-heading dashboard-panel-heading">
                <h3 class="panel-title">Peringatan Stok Menipis</h3>
                <a href="{{ url('/alats') }}" class="btn btn-primary btn-sm">Lihat Alat</a>
            </div>
            <div class="panel-body">
                @if($stokMenipis->isNotEmpty())
                    <ul class="list-group dashboard-stock-list">
                        @foreach($stokMenipis as $alat)
                            <li class="list-group-item">
                                <span>{{ $alat->nama_alat }}</span>
                                <span class="label {{ $alat->stok <= 1 ? 'label-danger' : 'label-warning' }}">
                                    Stok {{ $alat->stok ?? 0 }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-muted">Tidak ada alat dengan stok 3 atau kurang.</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection