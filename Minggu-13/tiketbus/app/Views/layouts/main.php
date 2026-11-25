<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'TiketBus') ?> · TiketBus</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tiket.css') ?>">
</head>
<body>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
        <a class="navbar-brand fs-4" href="<?= url_to('home') ?>"><i class="bi bi-bus-front-fill text-tb"></i> Tiket<span class="text-tb">Bus</span></a>
        <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#nav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="nav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link <?= url_is('/') ? 'active fw-semibold' : '' ?>" href="<?= url_to('home') ?>">Cari Tiket</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('cek-tiket') ? 'active fw-semibold' : '' ?>" href="<?= url_to('check') ?>">Cek Tiket</a></li>
                <?php if (session('user_role') === 'pelanggan'): ?>
                    <li class="nav-item"><a class="nav-link <?= url_is('pesanan*') ? 'active fw-semibold' : '' ?>" href="<?= url_to('my.bookings') ?>">Pesanan Saya</a></li>
                <?php endif ?>
            </ul>
            <?php if (session('user_id')): ?>
                <div class="dropdown">
                    <button class="btn btn-light border dropdown-toggle" data-bs-toggle="dropdown"><i class="bi bi-person-circle text-tb"></i> <?= esc(explode(' ', auth_user('name'))[0]) ?></button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <?php if (is_admin()): ?>
                            <li><a class="dropdown-item" href="<?= url_to('admin.dashboard') ?>"><i class="bi bi-speedometer2 me-2"></i>Panel Admin</a></li>
                        <?php else: ?>
                            <li><a class="dropdown-item" href="<?= url_to('my.bookings') ?>"><i class="bi bi-ticket-perforated me-2"></i>Pesanan Saya</a></li>
                            <li><a class="dropdown-item" href="<?= url_to('profile') ?>"><i class="bi bi-person-gear me-2"></i>Profil</a></li>
                        <?php endif ?>
                        <li><hr class="dropdown-divider"></li>
                        <li><form action="<?= url_to('logout') ?>" method="post" class="m-0"><?= csrf_field() ?><button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button></form></li>
                    </ul>
                </div>
            <?php else: ?>
                <a href="<?= url_to('login') ?>" class="btn btn-outline-primary me-2">Masuk</a>
                <a href="<?= url_to('register') ?>" class="btn btn-primary">Daftar</a>
            <?php endif ?>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?= $this->include('partials/flash') ?>
    <?= $this->renderSection('content') ?>
</main>
<footer class="border-top bg-white py-3 mt-4 small text-muted">
    <div class="container d-flex justify-content-between"><span>TiketBus — Quiz CodeIgniter PBKK D</span><span>Achmad Najwa · 5025231265</span></div>
</footer>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<?= $this->renderSection('scripts') ?>
</body>
</html>
