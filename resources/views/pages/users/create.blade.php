@extends('layouts.app')

@section('title', 'Tambah Pengguna')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Tambah Pengguna</h3></div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" name="username" class="form-control" value="{{ old('username') }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" name="password" type="password" class="form-control" required minlength="8">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                    <option value="petugas" @selected(old('role') === 'petugas')>Petugas</option>
                    <option value="peminjam" @selected(old('role') === 'peminjam')>Peminjam</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection