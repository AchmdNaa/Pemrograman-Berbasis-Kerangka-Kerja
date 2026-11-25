<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="card p-4 mb-4" style="background: linear-gradient(120deg, #fff7ed, #ffffff)">
    <h1 class="h4 fw-bold mb-1">Halo, <?= esc($user['name']) ?> 👋</h1>
    <p class="text-muted mb-0">Selamat datang di area akun ShopEase. Dari sini Anda dapat mengelola profil dan keamanan akun.</p>
</div>
<div class="row g-3 mb-4">
    <div class="col-md-4"><div class="card stat p-3"><div class="small text-muted">Pesanan aktif</div><div class="num">0</div><div class="small text-muted">Fitur pesanan hadir di pertemuan berikutnya</div></div></div>
    <div class="col-md-4"><div class="card stat p-3" style="--accent: #0ea5e9"><div class="small text-muted">Bergabung sejak</div><div class="num fs-5 mt-1"><?= date('d M Y', strtotime($user['created_at'])) ?></div></div></div>
    <div class="col-md-4"><div class="card stat p-3" style="--accent: #10b981"><div class="small text-muted">Login terakhir</div><div class="num fs-5 mt-1"><?= $user['last_login_at'] ? date('d M Y H:i', strtotime($user['last_login_at'])) : 'Baru saja daftar' ?></div></div></div>
</div>
<div class="card p-4">
    <h2 class="h6 fw-bold">Data Akun</h2>
    <table class="table mb-3">
        <tr><th class="text-muted fw-normal w-25">Nama</th><td><?= esc($user['name']) ?></td></tr>
        <tr><th class="text-muted fw-normal">Email</th><td><?= esc($user['email']) ?></td></tr>
        <tr><th class="text-muted fw-normal">Nomor HP</th><td><?= esc($user['phone'] ?? '-') ?></td></tr>
        <tr><th class="text-muted fw-normal">Peran</th><td><?= role_badge($user['role']) ?></td></tr>
    </table>
    <div class="d-flex gap-2">
        <a href="<?= url_to('account.profile') ?>" class="btn btn-outline-primary"><i class="bi bi-pencil"></i> Ubah Profil</a>
        <a href="<?= url_to('products') ?>" class="btn btn-primary"><i class="bi bi-bag"></i> Lanjut Belanja</a>
    </div>
</div>
<?= $this->endSection() ?>
