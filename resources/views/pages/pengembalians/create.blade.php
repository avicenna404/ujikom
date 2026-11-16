@extends('layouts.app')

@section('title', 'Tambah Pengembalian')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Tambah Data Pengembalian</h3></div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('pengembalians.store') }}">
            @csrf
            <div class="form-group">
                <label for="peminjaman_id">Peminjaman</label>
                <select id="peminjaman_id" name="peminjaman_id" class="form-control" required>
                    @foreach($peminjamans as $peminjaman)
                        <option value="{{ $peminjaman->id }}" @selected(old('peminjaman_id') == $peminjaman->id)>
                            #{{ $peminjaman->id }} — {{ $peminjaman->user?->nama_lengkap ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali_aktual">Tanggal Kembali Aktual</label>
                <input id="tanggal_kembali_aktual" name="tanggal_kembali_aktual" type="date" class="form-control" value="{{ old('tanggal_kembali_aktual', today()->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="kondisi_kembali">Kondisi Kembali</label>
                <select id="kondisi_kembali" name="kondisi_kembali" class="form-control" required>
                    <option value="baik">Baik</option>
                    <option value="rusak ringan">Rusak ringan</option>
                    <option value="rusak berat">Rusak berat</option>
                    <option value="hilang">Hilang</option>
                </select>
            </div>
            <div class="form-group">
                <label for="denda">Denda (Rp)</label>
                <input id="denda" name="denda" type="number" min="0" class="form-control" value="{{ old('denda', 0) }}" required>
            </div>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control">{{ old('keterangan') }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Simpan sebagai Menunggu Verifikasi</button>
            <a href="{{ route('pengembalians.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection