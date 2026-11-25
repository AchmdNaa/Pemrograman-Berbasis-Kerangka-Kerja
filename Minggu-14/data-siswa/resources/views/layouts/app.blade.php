<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Dashboard') · Data Siswa</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>
        :root { --bs-primary: #dc2626; --bs-primary-rgb: 220, 38, 38; --bs-link-color-rgb: 185, 28, 28; }
        body { background: #f8fafc; }
        .btn-primary { --bs-btn-bg: #dc2626; --bs-btn-border-color: #dc2626; --bs-btn-hover-bg: #b91c1c; --bs-btn-hover-border-color: #b91c1c; --bs-btn-active-bg: #991b1b; }
        .btn-outline-primary { --bs-btn-color: #dc2626; --bs-btn-border-color: #dc2626; --bs-btn-hover-bg: #dc2626; --bs-btn-hover-border-color: #dc2626; }
        .pagination { --bs-pagination-active-bg: #dc2626; --bs-pagination-active-border-color: #dc2626; --bs-pagination-color: #b91c1c; }
        .navbar { background: linear-gradient(90deg, #7f1d1d, #dc2626); }
        .card { border: 0; border-radius: .9rem; box-shadow: 0 .1rem .8rem rgba(15,23,42,.06); }
        .stat { border-left: 4px solid var(--c, #dc2626); }
        .avatar { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; display: inline-grid; place-items: center; background: #fee2e2; color: #b91c1c; font-weight: 700; flex-shrink: 0; }
        .form-check-input:checked { background-color: #dc2626; border-color: #dc2626; }
        a { text-decoration: none; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}"><i class="bi bi-mortarboard-fill"></i> Data Siswa <span class="fw-normal small opacity-75">· Laravel {{ app()->version() }}</span></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('dashboard') ? 'active fw-semibold' : '' }}" href="{{ route('dashboard') }}"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('siswa.*') ? 'active fw-semibold' : '' }}" href="{{ route('siswa.index') }}"><i class="bi bi-people"></i> Siswa</a></li>
                <li class="nav-item"><a class="nav-link {{ request()->routeIs('kelas.*') ? 'active fw-semibold' : '' }}" href="{{ route('kelas.index') }}"><i class="bi bi-door-open"></i> Kelas</a></li>
            </ul>
        </div>
    </div>
</nav>
<main class="container pb-5">
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show"><i class="bi bi-check-circle"></i> {{ session('success') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show"><i class="bi bi-exclamation-triangle"></i> {{ session('error') }}<button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
    @endif
    @yield('content')
</main>
<footer class="border-top bg-white py-3 small text-muted">
    <div class="container d-flex justify-content-between"><span>CRUD Laravel — PBKK D</span><span>Achmad Najwa · 5025231265</span></div>
</footer>
<script src="{{ asset('assets/bootstrap/bootstrap.bundle.min.js') }}"></script>
</body>
</html>
