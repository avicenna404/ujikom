@extends('layouts.app')

@section('title', 'Data User')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar User</h3>
        {{-- <a href="{{ url('/users/create') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-plus"></i> Tambah User
        </a>  --- kode tidak digunakan --}}
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                        <tr>
                            <th>Nama Lengkap</th>
                            <th>Username</th>
                            <th>Password</th>
                            <th>Role</th>
                            {{-- <th>Remember Token</th> --}}
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($users as $user)
                            <tr>
                                <td>{{ $user->nama_lengkap }}</td>
                                <td>{{ $user->username }}</td>
                                <td>{{ $user->password }}</td>
                                <td>{{ $user->role }}</td>
                                {{-- <td>{{ $user->remember_token }}</td> --}}
                                <td>
                                    <a href="{{ route('users.edit', $user) }}" class="btn btn-primary">Edit</a>
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" onsubmit="return confirm('Are you sure?')" class="d-inline">
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
