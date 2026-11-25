<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · KasirKu</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>
        :root { --k: #4f46e5; --bs-primary: #4f46e5; --bs-primary-rgb: 79, 70, 229; --bs-link-color-rgb: 79, 70, 229; }
        body { background: #f1f5f9; }
        a { text-decoration: none; }
        .btn-primary { --bs-btn-bg: #4f46e5; --bs-btn-border-color: #4f46e5; --bs-btn-hover-bg: #4338ca; --bs-btn-hover-border-color: #4338ca; --bs-btn-active-bg: #3730a3; --bs-btn-disabled-bg: #a5b4fc; --bs-btn-disabled-border-color: #a5b4fc; }
        .btn-outline-primary { --bs-btn-color: #4f46e5; --bs-btn-border-color: #4f46e5; --bs-btn-hover-bg: #4f46e5; --bs-btn-hover-border-color: #4f46e5; --bs-btn-active-bg: #4f46e5; --bs-btn-active-border-color: #4f46e5; }
        .pagination { --bs-pagination-active-bg: #4f46e5; --bs-pagination-active-border-color: #4f46e5; --bs-pagination-color: #4f46e5; }
        .form-check-input:checked { background-color: #4f46e5; border-color: #4f46e5; }
        .wrap { display: flex; min-height: 100vh; }
        .side { width: 230px; flex-shrink: 0; background: #1e1b4b; color: #c7d2fe; }
        .side .brand { color: #fff; font-weight: 800; font-size: 1.25rem; padding: 1.1rem 1.25rem; display: block; }
        .side .heading { font-size: .68rem; text-transform: uppercase; letter-spacing: .08em; color: #818cf8; padding: .9rem 1.25rem .3rem; }
        .side .nav-link { color: #c7d2fe; padding: .55rem 1.25rem; display: flex; gap: .6rem; }
        .side .nav-link:hover { background: rgba(255,255,255,.06); color: #fff; }
        .side .nav-link.active { background: #4f46e5; color: #fff; font-weight: 600; }
        .card { border: 0; border-radius: .9rem; box-shadow: 0 .1rem .8rem rgba(15,23,42,.06); }
        .stat { border-left: 4px solid var(--c, #4f46e5); }
        .stat .num { font-size: 1.55rem; font-weight: 800; }
    </style>
    @stack('styles')
</head>
<body>
<div class="wrap">
    <aside class="side d-print-none">
        <a class="brand" href="{{ route('dashboard') }}"><i class="bi bi-shop-window"></i> KasirKu</a>
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a class="nav-link {{ request()->routeIs('pos.*') ? 'active' : '' }}" href="{{ route('pos.index') }}"><i class="bi bi-calculator"></i> Kasir</a>
            <a class="nav-link {{ request()->routeIs('transactions.*') ? 'active' : '' }}" href="{{ route('transactions.index') }}"><i class="bi bi-receipt"></i> Transaksi</a>
            @if (auth()->user()->isAdmin())
                <div class="heading">Admin</div>
                <a class="nav-link {{ request()->routeIs('products.*') ? 'active' : '' }}" href="{{ route('products.index') }}"><i class="bi bi-box-seam"></i> Produk</a>
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}"><i class="bi bi-tags"></i> Kategori</a>
                <a class="nav-link {{ request()->routeIs('reports.*') ? 'active' : '' }}" href="{{ route('reports.index') }}"><i class="bi bi-graph-up-arrow"></i> Laporan</a>
                <a class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}" href="{{ route('users.index') }}"><i class="bi bi-people"></i> Pengguna</a>
            @endif
        </nav>
    </aside>
    <div class="flex-grow-1" style="min-width: 0">
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center d-print-none">
            <h1 class="h5 fw-bold mb-0">@yield('title')</h1>
            <div class="d-flex align-items-center gap-3 small">
                <span><i class="bi bi-person-circle"></i> {{ auth()->user()->name }} <span class="badge {{ auth()->user()->isAdmin() ? 'bg-primary' : 'bg-secondary' }}">{{ auth()->user()->role }}</span></span>
                <form action="{{ route('logout') }}" method="POST" class="m-0">@csrf<button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Keluar</button></form>
            </div>
        </header>
        <main class="p-4">
            @if (session('success'))<div class="alert alert-success alert-dismissible fade show d-print-none"><i class="bi bi-check-circle"></i> {{ session('success') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @if (session('error'))<div class="alert alert-danger alert-dismissible fade show d-print-none"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}<button class="btn-close" data-bs-dismiss="alert"></button></div>@endif
            @yield('content')
        </main>
    </div>
</div>
<script src="{{ asset('assets/bootstrap/bootstrap.bundle.min.js') }}"></script>
@stack('scripts')
</body>
</html>
