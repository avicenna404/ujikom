@extends('layouts.app')

@section('title', 'Edit Pengembalian')

@section('content')
<div class="panel">
    <div class="panel-heading"><h3 class="panel-title">Edit Data Pengembalian</h3></div>
    <div class="panel-body">
        @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
        <form method="POST" action="{{ route('pengembalians.update', $pengembalian) }}">
            @csrf
            @method('PUT')
            <div class="form-group">
                <label for="peminjaman_id">Peminjaman</label>
                <select id="peminjaman_id" name="peminjaman_id" class="form-control" required>
                    @foreach($peminjamans as $peminjaman)
                        <option value="{{ $peminjaman->id }}" @selected(old('peminjaman_id', $pengembalian->peminjaman_id) == $peminjaman->id)>
                            #{{ $peminjaman->id }} — {{ $peminjaman->user?->nama_lengkap ?? '-' }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="form-group">
                <label for="tanggal_kembali_aktual">Tanggal Kembali Aktual</label>
                <input id="tanggal_kembali_aktual" name="tanggal_kembali_aktual" type="date" class="form-control" value="{{ old('tanggal_kembali_aktual', $pengembalian->tanggal_kembali_aktual?->format('Y-m-d')) }}" required>
            </div>
            <div class="form-group">
                <label for="kondisi_kembali">Kondisi Kembali</label>
                <select id="kondisi_kembali" name="kondisi_kembali" class="form-control" required>
                    @foreach(['baik', 'rusak ringan', 'rusak berat', 'hilang'] as $kondisi)
                        <option value="{{ $kondisi }}" @selected(old('kondisi_kembali', $pengembalian->kondisi_kembali) === $kondisi)>{{ ucfirst($kondisi) }}</option>
                    @endforeach
                </select>
            </div>
            <p class="text-muted">Denda dihitung otomatis sebesar Rp5.000 per hari keterlambatan.</p>
            <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <textarea id="keterangan" name="keterangan" class="form-control">{{ old('keterangan', $pengembalian->keterangan) }}</textarea>
            </div>
            <button class="btn btn-primary" type="submit">Simpan</button>
            <a href="{{ route('pengembalians.index') }}" class="btn btn-default">Batal</a>
        </form>
    </div>
</div>
@endsection