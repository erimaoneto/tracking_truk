<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Erickman Dashboard | Sistem Pelacakan Truk</title>
    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="" />
</head>
<body>

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="brand">
            <div class="brand-icon">E</div>
            <div>
                <h1>PT. Erickman</h1>
                <span>Sarana Abadi</span>
            </div>
        </div>

        <ul class="nav-links">
            <!-- GENERAL / UTAMA -->
            <div class="nav-section-title">Menu Utama</div>
            <li>
                <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 01.553-.894L9 2l6 3 5.447-2.724A1 1 0 0121 3.118v10.764a1 1 0 01-.553.894L15 18l-6 2zM9 2v18m6-15v13"></path></svg>
                    Dashboard
                </a>
            </li>

            <!-- DATA MASTER -->
            <div class="nav-section-title">Data Master</div>
            <li>
                <a href="{{ route('admin.items') }}" class="nav-item {{ request()->routeIs('admin.items*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Master Barang
                </a>
            </li>
            <li>
                <a href="{{ route('admin.vehicles') }}" class="nav-item {{ request()->routeIs('admin.vehicles*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0"></path></svg>
                    Master Kendaraan
                </a>
            </li>
            <li>
                <a href="{{ route('admin.drivers') }}" class="nav-item {{ request()->routeIs('admin.drivers*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    Master Supir
                </a>
            </li>
            <li>
                <a href="{{ route('admin.users') }}" class="nav-item {{ request()->routeIs('admin.users*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.121 17.804A13.937 13.937 0 0112 16c2.5 0 4.847.655 6.879 1.804M15 10a3 3 0 11-6 0 3 3 0 016 0zm6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Master Pengguna
                </a>
            </li>

            <!-- TRANSAKSI -->
            <div class="nav-section-title">Transaksi & Log</div>
            <li>
                <a href="{{ route('admin.contracts') }}" class="nav-item {{ request()->routeIs('admin.contracts*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Kontrak Sewa
                </a>
            </li>
            <li>
                <a href="{{ route('stock-opname') }}" class="nav-item {{ request()->routeIs('stock-opname*') ? 'active' : '' }}">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                    Stok Opname
                </a>
            </li>
        </ul>

        <div style="margin-top: auto;">
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="nav-item" style="width: 100%; border: none; background: rgba(239, 68, 68, 0.1); color: #ef4444; cursor: pointer; display: flex; align-items: center; justify-content: flex-start;">
                    <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="margin-right: 12px;"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </button>
            </form>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @yield('content')
    </main>

    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
    @stack('scripts')
</body>
</html>
