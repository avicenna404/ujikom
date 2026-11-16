@extends('layouts.app')

@section('title', 'Data Alat')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar Alat</h3>
        <a href="{{ url('/alats/create') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-plus"></i> Tambah Alat
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama alat</th>
                        <th>Stok</th>
                        <th>Kondisi</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach($alats as $alat)
                <tr>
                    <td>{{ $alat->nama_alat }}</td>
                    <td>{{ $alat->kategori->id ?? '-' }}</td>
                    <td>{{ $alat->stok }}</td>
                    <td>{{ $alat->kondisi }}</td>
                    <td>
                        <a href="{{ route('alats.edit', $alat) }}" class="btn btn-primary">Edit</a>
                        <form method="POST" action="{{ route('alats.destroy', $alat) }}" onsubmit="return confirm('Are you sure?')" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
