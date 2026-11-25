<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Admin') ?> · Admin TiketBus</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tiket.css') ?>">
</head>
<body>
<div class="admin-wrap">
    <aside class="admin-side">
        <a class="brand fs-5" href="<?= url_to('admin.dashboard') ?>"><i class="bi bi-bus-front-fill text-info"></i> TiketBus Admin</a>
        <nav class="nav flex-column">
            <a class="nav-link <?= url_is('admin') ? 'active' : '' ?>" href="<?= url_to('admin.dashboard') ?>"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <div class="heading">Transaksi</div>
            <a class="nav-link <?= url_is('admin/pemesanan*') ? 'active' : '' ?>" href="<?= url_to('admin.bookings') ?>"><i class="bi bi-receipt"></i> Pemesanan</a>
            <a class="nav-link <?= url_is('admin/laporan*') ? 'active' : '' ?>" href="<?= url_to('admin.reports') ?>"><i class="bi bi-graph-up"></i> Laporan Penjualan</a>
            <div class="heading">Operasional</div>
            <a class="nav-link <?= url_is('admin/jadwal*') ? 'active' : '' ?>" href="<?= url_to('admin.schedules') ?>"><i class="bi bi-calendar-week"></i> Jadwal</a>
            <a class="nav-link <?= url_is('admin/bus*') ? 'active' : '' ?>" href="<?= url_to('admin.buses') ?>"><i class="bi bi-bus-front"></i> Armada Bus</a>
            <a class="nav-link <?= url_is('admin/rute*') ? 'active' : '' ?>" href="<?= url_to('admin.routes') ?>"><i class="bi bi-signpost-split"></i> Rute</a>
            <div class="heading">Pengaturan</div>
            <a class="nav-link <?= url_is('admin/rekening*') ? 'active' : '' ?>" href="<?= url_to('admin.banks') ?>"><i class="bi bi-bank"></i> Rekening Bank</a>
            <a class="nav-link <?= url_is('admin/pelanggan*') ? 'active' : '' ?>" href="<?= url_to('admin.customers') ?>"><i class="bi bi-people"></i> Pelanggan</a>
            <a class="nav-link" href="<?= url_to('home') ?>" target="_blank"><i class="bi bi-box-arrow-up-right"></i> Lihat Situs</a>
        </nav>
    </aside>
    <div class="flex-grow-1" style="min-width: 0">
        <header class="bg-white border-bottom px-4 py-3 d-flex justify-content-between align-items-center mb-4">
            <h1 class="h5 fw-bold mb-0"><?= esc($title ?? '') ?></h1>
            <div class="d-flex align-items-center gap-3 small">
                <span><i class="bi bi-person-circle"></i> <?= esc(auth_user('name')) ?></span>
                <form action="<?= url_to('logout') ?>" method="post" class="m-0"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Keluar</button></form>
            </div>
        </header>
        <div class="px-4 pb-4">
            <?= $this->include('partials/flash') ?>
            <?= $this->renderSection('content') ?>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
</body>
</html>
