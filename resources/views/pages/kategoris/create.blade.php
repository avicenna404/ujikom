@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Kategori</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('kategoris.store') }}">
        @csrf
        <div class="form-group">
            <label for="nama_kategori">Nama Kategori</label>
            <input type="text" name="nama_kategori" id="nama_kategori" class="form-control" value="{{ old('nama_kategori') }}">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('kategoris.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
