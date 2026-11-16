@extends('layouts.app')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Edit Kategori</h3>
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
    <form method="POST" action="{{ route('kategoris.update', $kategori) }}">
        @csrf
        @method('PUT')
        <div class="form-group">
            <label for="nama_kategori">Nama Kategori</label>
            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="{{ old('nama_kategori', $kategori->nama_kategori ?? '') }}">
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
        <a href="{{ route('kategoris.index') }}" class="btn btn-default">Cancel</a>
    </form>
</div>
</div>
@endsection