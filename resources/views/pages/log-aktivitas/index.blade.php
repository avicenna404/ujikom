@extends('layouts.app')

@section('title', 'Log Aktivitas')

@section('content')
<div class="panel">
    <div class="panel-heading">
        <h3 class="panel-title">Log Aktivitas</h3>
    </div>
    <div class="panel-body">
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
                            <td>{{ $logs->firstItem() + $loop->index }}</td>
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

        {{ $logs->links() }}
    </div>
</div>
@endsection