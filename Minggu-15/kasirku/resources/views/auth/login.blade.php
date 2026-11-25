<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk · KasirKu</title>
    <link rel="stylesheet" href="{{ asset('assets/bootstrap/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/bootstrap-icons/bootstrap-icons.min.css') }}">
    <style>body { min-height: 100vh; background: radial-gradient(circle at 20% 20%, #6366f1, transparent 40%), linear-gradient(135deg, #1e1b4b, #312e81); } .btn-primary { background: #4f46e5; border-color: #4f46e5; }</style>
</head>
<body class="d-flex align-items-center">
<div class="container" style="max-width: 420px">
    <div class="text-center text-white mb-4"><i class="bi bi-shop-window" style="font-size: 3rem"></i><h1 class="h3 fw-bold">KasirKu</h1><p class="opacity-75 mb-0">Aplikasi kasir (Point of Sale) berbasis Laravel</p></div>
    <div class="card border-0 shadow p-4" style="border-radius: 1rem">
        @if (session('success'))<div class="alert alert-success py-2 small">{{ session('success') }}</div>@endif
        <form action="{{ url('/login') }}" method="POST">
            @csrf
            <div class="mb-3">
                <label class="form-label" for="email">Email</label>
                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" autofocus>
                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="password">Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="form-check mb-3"><input class="form-check-input" type="checkbox" name="remember" id="remember"><label class="form-check-label small" for="remember">Ingat saya</label></div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>
        <div class="small text-muted border-top mt-3 pt-3">Admin: <code>admin@kasirku.test</code> / <code>Admin1234</code><br>Kasir: <code>kasir@kasirku.test</code> / <code>Kasir1234</code></div>
    </div>
</div>
</body>
</html>
