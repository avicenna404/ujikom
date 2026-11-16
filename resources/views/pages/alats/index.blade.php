@extends('layouts.app')

@section('title', 'Data Alat')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display:flex;justify-content:space-between;align-items:center;">
        <h3 class="panel-title">Daftar Alat</h3>
        <a href="{{ route('alats.create') }}" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Alat</a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead><tr><th>Nama Alat</th><th>Kategori</th><th>Stok</th><th>Kondisi</th><th>Aksi</th></tr></thead>
                <tbody>
                    @forelse($alats as $alat)
                        <tr>
                            <td>{{ $alat->nama_alat }}</td>
                            <td>{{ $alat->kategori?->nama_kategori ?? '-' }}</td>
                            <td>{{ $alat->stok ?? 0 }}</td>
                            <td>{{ ucfirst($alat->kondisi) }}</td>
                            <td>
                                <a href="{{ route('alats.edit', $alat) }}" class="btn btn-primary btn-sm">Edit</a>
                                @if(auth()->user()->role === 'admin')
                                    <form method="POST" action="{{ route('alats.destroy', $alat) }}" class="d-inline" onsubmit="return confirm('Hapus alat ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-danger btn-sm" type="submit">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center">Belum ada data alat.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $alats->links() }}
    </div>
</div>
@endsection