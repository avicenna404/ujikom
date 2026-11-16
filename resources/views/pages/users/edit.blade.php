@extends('layouts.app')

@section('title', 'Edit Pengguna')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Edit Pengguna</h3></div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('users.update', $user) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="nama_lengkap">Nama Lengkap</label>
                <input id="nama_lengkap" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $user->nama_lengkap) }}" required>
            </div>
            <div class="form-group">
                <label for="username">Username</label>
                <input id="username" name="username" class="form-control" value="{{ old('username', $user->username) }}" required>
            </div>
            <div class="form-group">
                <label for="password">Password baru</label>
                <input id="password" name="password" type="password" class="form-control" minlength="8" placeholder="Biarkan kosong jika tidak diganti">
            </div>
            <div class="form-group">
                <label for="role">Role</label>
                <select id="role" name="role" class="form-control" required>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                    <option value="petugas" @selected(old('role', $user->role) === 'petugas')>Petugas</option>
                    <option value="peminjam" @selected(old('role', $user->role) === 'peminjam')>Peminjam</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
            <a href="{{ route('users.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection