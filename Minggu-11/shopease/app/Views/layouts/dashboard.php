<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Dashboard') ?> · ShopEase</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shop.css') ?>">
</head>
<body>
<?= $this->include('partials/navbar') ?>
<?php $role = auth_user('role'); ?>
<main class="container py-4">
    <div class="row g-4">
        <aside class="col-lg-3">
            <div class="card p-3">
                <div class="d-flex align-items-center gap-2 mb-3 pb-3 border-bottom">
                    <span class="rounded-circle bg-warning-subtle text-orange fw-bold d-grid fs-5" style="width: 46px; height: 46px; place-items: center"><?= esc(mb_strtoupper(mb_substr(auth_user('name'), 0, 1))) ?></span>
                    <div class="small"><div class="fw-semibold"><?= esc(auth_user('name')) ?></div><?= role_badge($role) ?></div>
                </div>
                <nav class="nav flex-column dash-nav">
                    <?php if ($role === 'admin'): ?>
                        <div class="small text-uppercase text-muted fw-semibold px-2 mt-1 mb-1">Admin</div>
                        <a class="nav-link <?= url_is('admin') ? 'active' : '' ?>" href="<?= url_to('admin.dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
                        <a class="nav-link <?= url_is('admin/pengguna*') ? 'active' : '' ?>" href="<?= url_to('admin.users') ?>"><i class="bi bi-people"></i> Pengguna</a>
                    <?php endif ?>
                    <?php if (in_array($role, ['admin', 'operator'], true)): ?>
                        <div class="small text-uppercase text-muted fw-semibold px-2 mt-2 mb-1">Operasional</div>
                        <a class="nav-link <?= url_is('operator*') ? 'active' : '' ?>" href="<?= url_to('operator.dashboard') ?>"><i class="bi bi-box-seam"></i> Stok Produk</a>
                    <?php endif ?>
                    <div class="small text-uppercase text-muted fw-semibold px-2 mt-2 mb-1">Akun</div>
                    <a class="nav-link <?= url_is('akun') ? 'active' : '' ?>" href="<?= url_to('account') ?>"><i class="bi bi-house"></i> Ringkasan Akun</a>
                    <a class="nav-link <?= url_is('akun/profil') ? 'active' : '' ?>" href="<?= url_to('account.profile') ?>"><i class="bi bi-person-gear"></i> Profil &amp; Keamanan</a>
                    <form action="<?= url_to('logout') ?>" method="post" class="m-0"><?= csrf_field() ?>
                        <button class="nav-link text-danger border-0 bg-transparent w-100"><i class="bi bi-box-arrow-right"></i> Keluar</button>
                    </form>
                </nav>
            </div>
        </aside>
        <section class="col-lg-9">
            <?= $this->include('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </section>
    </div>
</main>
<?= $this->include('partials/footer') ?>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
