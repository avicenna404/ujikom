@php($role = auth()->user()?->role)

<ul class="nav">
    @if(in_array($role, ['admin', 'petugas'], true))
        <li>
            <a href="{{ route('dashboard') }}" class="{{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-home"></i> <span>Dashboard</span>
            </a>
        </li>
    @endif

    @if($role === 'admin')
        <li>
            <a href="{{ route('users.index') }}" class="{{ request()->is('users*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-user"></i> <span>Pengguna</span>
            </a>
        </li>
        <li>
            <a href="{{ route('kategoris.index') }}" class="{{ request()->is('kategoris*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-tags"></i> <span>Kategori</span>
            </a>
        </li>
    @endif

    @if(in_array($role, ['admin', 'petugas'], true))
        <li>
            <a href="{{ route('alats.index') }}" class="{{ request()->is('alats*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-wrench"></i> <span>Data Alat</span>
            </a>
        </li>
        <li>
            <a href="{{ route('peminjamans.index') }}" class="{{ request()->is('peminjamans*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-list-alt"></i> <span>Peminjaman</span>
            </a>
        </li>
        <li>
            <a href="{{ route('detail-peminjamans.index') }}" class="{{ request()->is('detail-peminjaman*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-th-list"></i> <span>Detail Peminjaman</span>
            </a>
        </li>
        <li>
            <a href="{{ route('pengembalians.index') }}" class="{{ request()->is('pengembalians*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-share-alt"></i> <span>Pengembalian</span>
            </a>
        </li>
        <li>
            <a href="{{ route('laporan.index') }}" class="{{ request()->is('laporan*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-print"></i> <span>Laporan</span>
            </a>
        </li>
    @endif

    @if($role === 'admin')
        <li>
            <a href="{{ route('log-aktivitas.index') }}" class="{{ request()->is('log-aktivitas*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-time"></i> <span>Log Aktivitas</span>
            </a>
        </li>
    @endif

    @if($role === 'peminjam')
        <li>
            <a href="{{ route('katalog-alat.index') }}" class="{{ request()->is('katalog-alat*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-wrench"></i> <span>Katalog Alat</span>
            </a>
        </li>
        <li>
            <a href="{{ route('peminjaman-saya') }}" class="{{ request()->is('peminjaman-saya*') || request()->is('ajukan-peminjaman*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-list-alt"></i> <span>Peminjaman Saya</span>
            </a>
        </li>
        <li>
            <a href="{{ route('pengembalian-saya') }}" class="{{ request()->is('pengembalian-saya*') ? 'active' : '' }}">
                <i class="glyphicon glyphicon-share-alt"></i> <span>Pengembalian</span>
            </a>
        </li>
    @endif
</ul>