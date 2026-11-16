@extends('layouts.app')

@section('title', 'Katalog Alat')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="panel-title">Katalog Alat Tersedia</h3>
        <a href="{{ route('peminjaman-ajukan.create') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-plus"></i> Ajukan Peminjaman
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead><tr><th>Nama Alat</th><th>Kategori</th><th>Kondisi</th><th>Stok Tersedia</th></tr></thead>
                <tbody>
                    @forelse($alats as $alat)
                        <tr>
                            <td>{{ $alat->nama_alat }}</td>
                            <td>{{ $alat->kategori?->nama_kategori ?? '-' }}</td>
                            <td>{{ ucfirst($alat->kondisi) }}</td>
                            <td><span class="label {{ $alat->stok > 0 ? 'label-success' : 'label-default' }}">{{ $alat->stok ?? 0 }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Belum ada alat tersedia.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $alats->links() }}
    </div>
</div>
@endsection