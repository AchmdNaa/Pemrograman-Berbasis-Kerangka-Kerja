<?php $cartCount = (new \App\Libraries\Keranjang())->count(); ?>
<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Latihan CI4') ?> · Latihan CodeIgniter</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= site_url('/') ?>"><i class="bi bi-mortarboard-fill text-primary"></i> KampusKu</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= site_url('/') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('toko*') ? 'active' : '' ?>" href="<?= site_url('toko') ?>">Toko Online</a></li>
            </ul>
            <a href="<?= site_url('keranjang') ?>" class="btn btn-outline-primary me-2 position-relative">
                <i class="bi bi-cart3"></i> Keranjang
                <?php if ($cartCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"><?= $cartCount ?></span>
                <?php endif ?>
            </a>
            <a href="<?= site_url(session('isLoggedIn') ? 'admin' : 'login') ?>" class="btn btn-primary">
                <i class="bi bi-speedometer2"></i> <?= session('isLoggedIn') ? 'Panel Admin' : 'Login Admin' ?>
            </a>
        </div>
    </div>
</nav>

<main class="container py-4">
    <?= view('partials/flash') ?>
    <?= $this->renderSection('content') ?>
</main>

<footer class="border-top bg-white py-3 mt-4">
    <div class="container small text-muted d-flex justify-content-between">
        <span>Latihan CodeIgniter 4 — PBKK D</span>
        <span>Achmad Najwa · 5025231265</span>
    </div>
</footer>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
