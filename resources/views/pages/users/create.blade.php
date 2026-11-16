@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Tambah User</h3>
    </div>
    <div class="panel-body">
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('users.store') }}">
        @csrf
        <div class="form-group">
            <label for="nama_lengkap">Nama Lengkap</label>
            <input type="text" name="nama_lengkap" id="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}">
        </div>
        <div class="form-group">
            <label for="username">Username</label>
            <input type="text" name="username" id="username" class="form-control" value="{{ old('username') }}">
        </div>
        <div class="form-group">
            <label for="password">Password</label>
            <input type="text" name="password" id="password" class="form-control" value="{{ old('password') }}">
        </div>
        <!-- TODO: replace with select when enum metadata available -->
        <div class="form-group">
            <label for="role">Role</label>
            <input type="text" name="role" id="role" class="form-control" value="{{ old('role') }}">
        </div>
        <div class="form-group">
            <label for="remember_token">Remember Token</label>
            <input type="text" name="remember_token" id="remember_token" class="form-control" value="{{ old('remember_token') }}">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('users.index') }}" class="btn btn-default">Cancel</a>
    </form>
</div>
</div>
@endsection