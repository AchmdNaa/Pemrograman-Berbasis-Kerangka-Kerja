<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login · Panel Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body style="background: linear-gradient(180deg, #4e73df 10%, #224abe 100%); min-height: 100vh;">
<div class="container">
    <div class="row justify-content-center align-items-center" style="min-height: 100vh;">
        <div class="col-md-5 col-lg-4">
            <div class="card p-4">
                <div class="text-center mb-3">
                    <i class="bi bi-mortarboard-fill text-primary" style="font-size: 2.5rem"></i>
                    <h1 class="h4 fw-bold mt-2 mb-0">Panel Admin KampusKu</h1>
                    <p class="text-muted small">Masuk untuk mengelola SIAKAD dan Toko</p>
                </div>
                <?= view('partials/flash') ?>
                <form action="<?= site_url('login') ?>" method="post">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label" for="username">Username</label>
                        <input type="text" class="form-control" id="username" name="username" value="<?= esc(old('username')) ?>" autofocus>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="password">Password</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
                </form>
                <div class="small text-muted mt-3 border-top pt-3">
                    Akun demo: <code>admin / admin123</code> (akses penuh) · <code>staff / staff123</code> (tanpa hapus data)
                </div>
                <a href="<?= site_url('/') ?>" class="small text-center d-block mt-2">&larr; Kembali ke beranda</a>
            </div>
        </div>
    </div>
</div>
</body>
</html>
