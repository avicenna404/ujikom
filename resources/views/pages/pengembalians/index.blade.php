@extends('layouts.app')

@section('title', 'Pengembalian')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="panel-title">Pemantauan Pengembalian</h3>
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('pengembalians.create') }}" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Data</a>
        @endif
    </div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead><tr><th>Peminjam</th><th>Transaksi</th><th>Tanggal Kembali</th><th>Kondisi</th><th>Denda</th><th>Status</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($pengembalians as $pengembalian)
                        @php
                            $statusClass = match ($pengembalian->status) {
                                'pending' => 'label-warning',
                                'diterima' => 'label-success',
                                'ditolak' => 'label-danger',
                                default => 'label-default',
                            };
                        @endphp
                        <tr>
                            <td>{{ $pengembalian->peminjaman?->user?->nama_lengkap ?? '-' }}</td>
                            <td>#{{ $pengembalian->peminjaman_id }}</td>
                            <td>{{ $pengembalian->tanggal_kembali_aktual?->format('d M Y') }}</td>
                            <td>{{ ucfirst($pengembalian->kondisi_kembali) }}</td>
                            <td>Rp {{ number_format($pengembalian->denda, 0, ',', '.') }}</td>
                            <td><span class="label {{ $statusClass }}">{{ ucfirst($pengembalian->status) }}</span></td>
                            <td>
                                @if($pengembalian->status === 'pending')
                                    <form method="POST" action="{{ route('pengembalians.verify', $pengembalian) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="diterima">
                                        <button class="btn btn-success btn-sm" type="submit">Verifikasi</button>
                                    </form>
                                    <form method="POST" action="{{ route('pengembalians.verify', $pengembalian) }}" class="d-inline">
                                        @csrf
                                        <input type="hidden" name="keputusan" value="ditolak">
                                        <button class="btn btn-danger btn-sm" type="submit">Tolak</button>
                                    </form>
                                @endif
                                @if(auth()->user()->role === 'admin')
                                    <a href="{{ route('pengembalians.edit', $pengembalian) }}" class="btn btn-primary btn-sm">Edit</a>
                                    <form method="POST" action="{{ route('pengembalians.destroy', $pengembalian) }}" class="d-inline" onsubmit="return confirm('Hapus data pengembalian ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-default btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center">Belum ada data pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $pengembalians->links() }}
    </div>
</div>
@endsection