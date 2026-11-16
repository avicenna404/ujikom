@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Tambah Pengembalian</h3>
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
    <form method="POST" action="{{ route('pengembalians.store') }}">
        @csrf
        <div class="form-group">
            <label for="peminjaman_id">Peminjaman Id</label>
            <select name="peminjaman_id" id="peminjaman_id" class="form-control">
                <option value="">-- Select --</option>
                @foreach($peminjamans as $option)
                    <option value="{{ $option->id }}" {{ old('peminjaman_id', '') == $option->id ? 'selected' : '' }}>
                        {{ $option->name ?? $option->title ?? $option->label ?? $option->id }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="user_id">User Id</label>
            <select name="user_id" id="user_id" class="form-control">
                <option value="">-- Select --</option>
                @foreach($users as $option)
                    <option value="{{ $option->id }}" {{ old('user_id', '') == $option->id ? 'selected' : '' }}>
                        {{ $option->name ?? $option->title ?? $option->label ?? $option->id }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="tanggal_kembali_aktual">Tanggal Kembali Aktual</label>
            <input type="date" name="tanggal_kembali_aktual" id="tanggal_kembali_aktual" class="form-control" value="{{ old('tanggal_kembali_aktual') }}">
        </div>
        <!-- TODO: replace with select when enum metadata available -->
        <div class="form-group">
            <label for="kondisi_kembali">Kondisi Kembali</label>
            <input type="text" name="kondisi_kembali" id="kondisi_kembali" class="form-control" value="{{ old('kondisi_kembali') }}">
        </div>
        <div class="form-group">
            <label for="denda">Denda</label>
            <input type="text" name="denda" id="denda" class="form-control" value="{{ old('denda') }}">
        </div>
        <div class="form-group">
            <label for="keterangan">Keterangan</label>
            <textarea name="keterangan" id="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('pengembalians.index') }}" class="btn btn-default">Cancel</a>
    </form>
</div>
</div>
@endsection