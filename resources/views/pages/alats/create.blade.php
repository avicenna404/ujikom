@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Tambah Alat</h3>
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
    <form method="POST" action="{{ route('alats.store') }}">
        @csrf
        <div class="form-group">
            <label for="nama_alat">Nama Alat</label>
            <input type="text" name="nama_alat" id="nama_alat" class="form-control" value="{{ old('nama_alat') }}">
        </div>
        <div class="form-group">
            <label for="kategori_id">Kategori Id</label>
            <select name="kategori_id" id="kategori_id" class="form-control">
                <option value="">-- Select --</option>
                @foreach($kategoris as $option)
                    <option value="{{ $option->id }}" {{ old('kategori_id', '') == $option->id ? 'selected' : '' }}>
                        {{ $option->nama_kategori }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label for="stok">Stok</label>
            <input type="number" name="stok" id="stok" class="form-control" value="{{ old('stok') }}">
        </div>
        <div class="form-group">
            <label for="kondisi">Kondisi</label>
            <select name="kondisi" id="kondisi" class="form-control">
                <option value="">-- Select --</option>
                <option value="baik" {{ old('kondisi', '') == 'baik' ? 'selected' : '' }}>Baik</option>
                <option value="rusak ringan" {{ old('kondisi', '') == 'rusak ringan' ? 'selected' : '' }}>Rusak Ringan</option>
                <option value="rusak berat" {{ old('kondisi', '') == 'rusak berat' ? 'selected' : '' }}>Rusak Berat</option>
            </select>
        </div>
        <br>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('alats.index') }}" class="btn btn-default">Cancel</a>
    </form>
</div>
</div>
@endsection