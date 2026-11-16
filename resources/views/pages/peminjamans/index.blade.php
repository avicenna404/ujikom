@extends('layouts.app')

@section('title', 'Data Peminjaman')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar Peminjaman</h3>
        <a href="{{ url('/peminjamans/create') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-plus"></i> Tambah Peminjaman
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                            <tr>
                                <th>User Id</th>
                                <th>Tanggal Pinjam</th>
                                <th>Rencana Kembali</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peminjamans as $peminjaman)
                                <tr>
                                    <td>{{ $peminjaman->user->id ?? '-' }}</td>
                                    <td>{{ $peminjaman->tanggal_pinjam }}</td>
                                    <td>{{ $peminjaman->rencana_kembali }}</td>
                                    <td>{{ $peminjaman->status }}</td>
                                    <td>
                                        <a href="{{ route('peminjamans.edit', $peminjaman) }}" class="btn btn-primary">Edit</a>
                                        <form method="POST" action="{{ route('peminjamans.destroy', $peminjaman) }}" onsubmit="return confirm('Are you sure?')" class="d-inline">
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
