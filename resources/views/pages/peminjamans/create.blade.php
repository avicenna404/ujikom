@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Create Peminjaman</h1>
    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form method="POST" action="{{ route('peminjamans.store') }}">
        @csrf
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
            <label for="tanggal_pinjam">Tanggal Pinjam</label>
            <input type="datetime-local" name="tanggal_pinjam" id="tanggal_pinjam" class="form-control" value="{{ old('tanggal_pinjam') }}">
        </div>
        <div class="form-group">
            <label for="rencana_kembali">Rencana Kembali</label>
            <input type="date" name="rencana_kembali" id="rencana_kembali" class="form-control" value="{{ old('rencana_kembali') }}">
        </div>
        <!-- TODO: replace with select when enum metadata available -->
        <div class="form-group">
            <label for="status">Status</label>
            <input type="text" name="status" id="status" class="form-control" value="{{ old('status') }}">
        </div>
        <button type="submit" class="btn btn-primary">Save</button>
        <a href="{{ route('peminjamans.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
