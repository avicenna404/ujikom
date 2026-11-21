@extends('layouts.app')

@section('title', 'Ajukan Peminjaman')

@section('content')
<div class="panel"><div class="panel-heading"><h3 class="panel-title">Ajukan Peminjaman Alat</h3></div><div class="panel-body">
@if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
<form method="POST" action="{{ route('peminjaman-ajukan.store') }}">@csrf
<div class="form-group"><label for="alat_id">Pilih alat (dapat memilih beberapa)</label><select id="alat_id" name="alat_id[]" class="form-control" multiple required>
@forelse($alats as $alat)<option value="{{ $alat->id }}" @selected(in_array($alat->id, old('alat_id', [])))>{{ $alat->nama_alat }} — stok {{ $alat->stok }}</option>@empty<option value="">Tidak ada alat yang stoknya tersedia</option>@endforelse
</select></div>
<div class="form-group"><label>Jumlah setiap alat (urutannya mengikuti pilihan)</label><div id="jumlah-fields"></div><script>document.addEventListener("DOMContentLoaded", function () { const select = document.getElementById("alat_id"); const fields = document.getElementById("jumlah-fields"); const render = () => { const oldValues = Array.from(fields.querySelectorAll("input"), input => input.value); fields.replaceChildren(...Array.from(select.selectedOptions, (option, index) => { const input = document.createElement("input"); input.type = "number"; input.min = "1"; input.required = true; input.name = "jumlah[]"; input.className = "form-control"; input.placeholder = "Jumlah " + option.text; input.value = oldValues[index] || "1"; return input; })); }; select.addEventListener("change", render); render(); });</script></div>
<div class="form-group"><label for="tanggal_kembali_rencana">Rencana Tanggal Kembali</label><input id="tanggal_kembali_rencana" name="tanggal_kembali_rencana" type="date" class="form-control" min="{{ today()->format('Y-m-d') }}" value="{{ old('tanggal_kembali_rencana') }}" required></div>
<div class="form-group"><label for="keperluan">Keperluan</label><textarea id="keperluan" name="keperluan" class="form-control">{{ old('keperluan') }}</textarea></div>
<button class="btn btn-primary" type="submit" @disabled($alats->isEmpty())>Kirim Pengajuan</button> <a href="{{ route('katalog-alat.index') }}" class="btn btn-default">Batal</a></form>
</div></div>
@endsection
