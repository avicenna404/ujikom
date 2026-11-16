<ul class="nav">
    <li>
        <a href="{{ url('/') }}" class="{{ request()->is('/') || request()->is('dashboard') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-home"></i> <span>Dashboard</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/kategoris') }}" class="{{ request()->is('kategori*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-tags"></i> <span>Kategori</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/alats') }}" class="{{ request()->is('alat*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-wrench"></i> <span>Alat</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/peminjamans') }}" class="{{ request()->is('peminjaman*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-plus-sign"></i> <span>Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/detail-peminjamans') }}" class="{{ request()->is('detail-peminjaman*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-list-alt"></i> <span>Detail Peminjaman</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/pengembalians') }}" class="{{ request()->is('pengembalian*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-share-alt"></i> <span>Pengembalian</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/users') }}" class="{{ request()->is('user*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-user"></i> <span>User</span>
        </a>
    </li>
    <li>
        <a href="{{ url('/log-aktivitas') }}" class="{{ request()->is('log-aktivitas*') ? 'active' : '' }}">
            <i class="glyphicon glyphicon-time"></i> <span>Log Aktivitas</span>
        </a>
    </li>
</ul>
