<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="text-center py-5">
    <div class="display-1 fw-bold text-orange">404</div>
    <h1 class="h3 fw-bold">Halaman tidak ditemukan</h1>
    <p class="text-muted"><?= esc($message ?: 'Halaman yang Anda cari mungkin sudah dipindahkan atau tidak pernah ada.') ?></p>
    <a href="<?= url_to('home') ?>" class="btn btn-primary me-2">Kembali ke Beranda</a>
    <a href="<?= url_to('products') ?>" class="btn btn-outline-primary">Lihat Produk</a>
</div>
<?= $this->endSection() ?>
