@extends('layouts.app')

@section('title', 'Data Kategori')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar Kategori Alat</h3>
        <a href="{{ url('/kategoris/create') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-plus"></i> Tambah Kategori
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Nama Kategori</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                     @foreach($kategoris as $kategori)
                <tr>
                    <td>{{ $kategori->id }}</td>
                    <td>{{ $kategori->nama_kategori }}</td>
                    <td>
                        <a href="{{ route('kategoris.edit', $kategori) }}" class="btn btn-primary">Edit</a>
                        <form method="POST" action="{{ route('kategoris.destroy', $kategori) }}" onsubmit="return confirm('Are you sure?')" class="d-inline">
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
