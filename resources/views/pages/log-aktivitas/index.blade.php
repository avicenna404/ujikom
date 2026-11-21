@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Log Aktivitas</h3>
    </div>
    <div class="panel-body">
        <form method="GET" action="{{ route('log-aktivitas.index') }}" class="form-inline" style="margin-bottom:15px;">
            <div class="form-group"><label for="tanggal_awal">Dari</label> <input id="tanggal_awal" type="date" name="tanggal_awal" class="form-control" value="{{ $tanggalAwal }}"></div>
            <div class="form-group"><label for="tanggal_akhir">Sampai</label> <input id="tanggal_akhir" type="date" name="tanggal_akhir" class="form-control" value="{{ $tanggalAkhir }}"></div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="{{ route('log-aktivitas.index') }}" class="btn btn-default">Reset</a>
        </form>
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                    <tr>
                        <th width="60">No</th>
                        <th>Waktu</th>
                        <th>Pengguna</th>
                        <th>Modul</th>
                        <th>Keterangan Aktivitas</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $log)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $log->created_at?->format('d M Y H:i') ?? '-' }}</td>
                            <td>{{ $log->user?->nama_lengkap ?? 'Sistem / Admin' }}</td>
                            <td><span class="label label-info">{{ $log->modul }}</span></td>
                            <td>{{ $log->aktivitas }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">Belum ada aktivitas tercatat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>
@endsection
