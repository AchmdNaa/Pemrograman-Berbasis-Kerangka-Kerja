<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin') ?> · Panel Admin</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
<div class="admin-wrap">
    <aside class="sidebar">
        <a class="brand" href="<?= site_url('admin') ?>"><i class="bi bi-mortarboard-fill fs-4"></i> KAMPUSKU</a>
        <nav class="nav flex-column">
            <a class="nav-link <?= url_is('admin') ? 'active' : '' ?>" href="<?= site_url('admin') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>

            <div class="heading">SIAKAD</div>
            <a class="nav-link <?= url_is('admin/mahasiswa*') ? 'active' : '' ?>" href="<?= site_url('admin/mahasiswa') ?>"><i class="bi bi-people"></i> Mahasiswa</a>
            <a class="nav-link <?= url_is('admin/matakuliah*') ? 'active' : '' ?>" href="<?= site_url('admin/matakuliah') ?>"><i class="bi bi-journal-bookmark"></i> Mata Kuliah</a>

            <div class="heading">Toko Online</div>
            <a class="nav-link <?= url_is('admin/produk*') ? 'active' : '' ?>" href="<?= site_url('admin/produk') ?>"><i class="bi bi-box-seam"></i> Produk</a>
            <a class="nav-link <?= url_is('admin/pesanan*') ? 'active' : '' ?>" href="<?= site_url('admin/pesanan') ?>"><i class="bi bi-receipt"></i> Pesanan</a>
            <a class="nav-link" href="<?= site_url('toko') ?>" target="_blank"><i class="bi bi-shop"></i> Lihat Toko</a>

            <?php if (is_admin()): ?>
                <div class="heading">Pengaturan</div>
                <a class="nav-link <?= url_is('admin/users*') ? 'active' : '' ?>" href="<?= site_url('admin/users') ?>"><i class="bi bi-person-gear"></i> Pengguna</a>
            <?php endif ?>
        </nav>
    </aside>

    <div class="main">
        <header class="topbar d-flex justify-content-between align-items-center px-4 py-3 mb-4">
            <h1 class="h5 mb-0 fw-bold"><?= esc($title ?? '') ?></h1>
            <div class="d-flex align-items-center gap-3">
                <span class="small text-muted">
                    <i class="bi bi-person-circle"></i> <?= esc(session('name')) ?>
                    <span class="badge <?= is_admin() ? 'bg-primary' : 'bg-secondary' ?> ms-1"><?= esc(session('role')) ?></span>
                </span>
                <form action="<?= site_url('logout') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</button>
                </form>
            </div>
        </header>
        <div class="px-4 pb-4">
            <?= view('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
