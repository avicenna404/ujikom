@extends('layouts.app')

@section('title', 'Data Pengguna')

@section('content')
<div class="panel">
    <div class="panel-heading" style="display: flex; justify-content: space-between; align-items: center;">
        <h3 class="panel-title">Daftar Pengguna</h3>
        <a href="{{ route('users.create') }}" class="btn btn-primary btn-sm">
            <i class="glyphicon glyphicon-plus"></i> Tambah Pengguna
        </a>
    </div>
    <div class="panel-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th>Nama Lengkap</th>
                        <th>Username</th>
                        <th>Role</th>
                        <th width="160">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->nama_lengkap }}</td>
                            <td>{{ $user->username }}</td>
                            <td><span class="label label-info">{{ ucfirst($user->role) }}</span></td>
                            <td>
                                {{-- <a href="{{ route('users.edit', $user) }}" class="btn btn-primary btn-sm">Edit</a> --}}
                                @if(auth()->id() !== $user->id)
                                    <form method="POST" action="{{ route('users.destroy', $user) }}" class="d-inline" onsubmit="return confirm('Hapus pengguna ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="text-center">Belum ada pengguna.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{ $users->links() }}
    </div>
</div>
@endsection
