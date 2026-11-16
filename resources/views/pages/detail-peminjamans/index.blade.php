@extends('layouts.app')

@section('title', 'Detail Peminjaman')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Daftar Detail Peminjaman</h3>
    </div>
    <div class="panel-body">
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
                            <td>{{ $details->firstItem() + $loop->index }}</td>
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

        {{ $details->links() }}
    </div>
</div>
@endsection