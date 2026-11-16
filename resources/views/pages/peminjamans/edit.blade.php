@extends('layouts.app')

@section('title', 'Edit Pengajuan Peminjaman')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Edit Pengajuan Peminjaman #{{ $peminjaman->id }}</h3></div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('peminjamans.update', $peminjaman) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="user_id">Peminjam</label>
                <select id="user_id" name="user_id" class="form-control" required>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}" @selected(old('user_id', $peminjaman->user_id) == $user->id)>{{ $user->nama_lengkap }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_pinjam">Tanggal Pinjam</label>
                <input id="tanggal_pinjam" name="tanggal_pinjam" type="date" class="form-control" value="{{ old('tanggal_pinjam', $peminjaman->tanggal_pinjam?->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali_rencana">Rencana Kembali</label>
                <input id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" type="date" class="form-control" value="{{ old('tanggal_kembali_rencana', $peminjaman->tanggal_kembali_rencana?->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="keperluan">Keperluan</label>
                <textarea id="keperluan" name="keperluan" class="form-control">{{ old('keperluan', $peminjaman->keperluan) }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a href="{{ route('peminjamans.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection