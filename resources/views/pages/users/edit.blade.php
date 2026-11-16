@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit User</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $user->nama_lengkap ?? '') }}">
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username', $user->username ?? '') }}">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="text" name="password" id="password" class="form-control" value="{{ old('password', $user->password ?? '') }}">
        </div>
        <!-- TODO: replace with select when enum metadata available -->
        <div class="form-group">
            <label for="role">Role</label>
            <input type="text" name="role" id="role" class="form-control" value="{{ old('role', $user->role ?? '') }}">
        </div>
        <div class="form-group">
            <label for="remember_token">Remember Token</label>
            <input type="text" name="remember_token" id="remember_token" class="form-control" value="{{ old('remember_token', $user->remember_token ?? '') }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('users.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
