@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Ajukan Peminjaman Alat</h3></div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('peminjaman-ajukan.store') }}">
            @csrf
            <div class="form-group">
                <label for="alat_id">Pilih Alat</label>
                <select id="alat_id" name="alat_id" class="form-control" required>
                    @forelse($alats as $alat)
                        <option value="{{ $alat->id }}" @selected(old('alat_id') == $alat->id)>{{ $alat->nama_alat }} — stok {{ $alat->stok }}</option>
                    @empty
                        <option value="">Tidak ada alat yang stoknya tersedia</option>
                    @endforelse
                </select>
            </div>
            <div class="form-group">
                <label for="jumlah">Jumlah</label>
                <input id="jumlah" name="jumlah" type="number" min="1" class="form-control" value="{{ old('jumlah', 1) }}" required>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali_rencana">Rencana Tanggal Kembali</label>
                <input id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" type="date" class="form-control" min="{{ today()->format('Y-m-d') }}" value="{{ old('tanggal_kembali_rencana') }}" required>
            </div>
            <div class="form-group">
                <label for="keperluan">Keperluan</label>
                <textarea id="keperluan" name="keperluan" class="form-control">{{ old('keperluan') }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit" @disabled($alats->isEmpty())>Kirim Pengajuan</button>
            <a href="{{ route('katalog-alat.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection