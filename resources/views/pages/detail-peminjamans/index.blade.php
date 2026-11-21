@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Daftar Detail Peminjaman</h3>
    </div>
    <div class="panel-body">
        <form method="GET" action="{{ route('detail-peminjamans.index') }}" class="form-inline" style="margin-bottom:15px;">
            <div class="form-group"><label for="tanggal_awal">Dari</label> <input id="tanggal_awal" type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal }}"></div>
            <div class="form-group"><label for="tanggal_akhir">Sampai</label> <input id="tanggal_akhir" type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}"></div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('detail-peminjamans.index') }}" class="btn btn-default">Reset</a>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>ID Transaksi</th>
                        <th>Nama Peminjam</th>
                        <th>Nama Alat</th>
                        <th>Jumlah Pinjam</th>
                        <th>Tanggal Pinjam</th>
                        <th>Status Peminjaman</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($details as $detail)
                        @php
                            $status = $detail->peminjaman->status ?? null;
                            $statusClass = match ($status) {
                                'dipinjam' => 'label-warning',
                                'dikembalikan' => 'label-success',
                                default => 'label-default',
                            };
                        @endphp
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>#{{ $detail->peminjaman_id }}</td>
                            <td>{{ $detail->peminjaman?->user?->nama_lengkap ?? '-' }}</td>
                            <td>{{ $detail->alat?->nama_alat ?? '-' }}</td>
                            <td>{{ $detail->jumlah }}</td>
                            <td>{{ $detail->peminjaman?->tanggal_pinjam?->format('d M Y') ?? '-' }}</td>
                            <td>
                                <span class="label {{ $statusClass }}">
                                    {{ $status ? ucfirst($status) : 'Tidak diketahui' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">Belum ada detail peminjaman.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
