@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar Pengembalian</h3>
        <a href="{{ route('pengembalians.create') }}" class="btn btn-primary btn-sm"><i class="glyphicon glyphicon-plus"></i> Tambah Pengembalian</a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th>Peminjaman Id</th>
                <th>User Id</th>
                <th>Tanggal Kembali</th>
                <th>Kondisi Kembali</th>
                <th>Denda</th>
                <th>Keterangan</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengembalians as $pengembalian)
                <tr>
                    <td>{{ $pengembalian->peminjaman->id ?? '-' }}</td>
                    <td>{{ $pengembalian->user->id ?? '-' }}</td>
                    <td>{{ $pengembalian->tanggal_kembali_aktual }}</td>
                    <td>{{ $pengembalian->kondisi_kembali }}</td>
                    <td>{{ $pengembalian->denda }}</td>
                    <td>{{ $pengembalian->keterangan }}</td>
                    <td>
                        <a href="{{ route('pengembalians.edit', $pengembalian) }}" class="btn btn-primary">Edit</a>
                        <form method="POST" action="{{ route('pengembalians.destroy', $pengembalian) }}" onsubmit="return confirm('Are you sure?')" class="d-inline">
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
        {{ $pengembalians->links() }}
    </div>
</div>
@endsection