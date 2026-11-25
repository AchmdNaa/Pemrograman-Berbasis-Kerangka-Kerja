<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<nav class="small mb-3"><a href="<?= site_url('toko') ?>">Toko</a> / <a href="<?= site_url('toko?kategori=' . $produk['kategori_slug']) ?>"><?= esc($produk['kategori']) ?></a> / <?= esc($produk['nama']) ?></nav>
<div class="card p-4 mb-4">
    <div class="row g-4">
        <div class="col-md-5"><img src="<?= produk_img($produk) ?>" class="w-100 rounded" style="aspect-ratio: 4/3; object-fit: cover" alt="<?= esc($produk['nama']) ?>"></div>
        <div class="col-md-7">
            <span class="badge badge-soft mb-2"><?= esc($produk['kategori']) ?></span>
            <h1 class="h3 fw-bold"><?= esc($produk['nama']) ?></h1>
            <div class="fs-3 fw-bold text-primary mb-2"><?= rupiah($produk['harga']) ?></div>
            <p class="text-muted"><?= nl2br(esc($produk['deskripsi'])) ?></p>
            <p class="<?= $produk['stok'] > 0 ? 'text-success' : 'text-danger' ?> fw-semibold">
                <i class="bi <?= $produk['stok'] > 0 ? 'bi-check-circle' : 'bi-x-circle' ?>"></i>
                <?= $produk['stok'] > 0 ? 'Tersedia ' . $produk['stok'] . ' unit' : 'Stok habis' ?>
            </p>
            <?php if ($produk['stok'] > 0): ?>
                <form action="<?= site_url('keranjang/tambah') ?>" method="post" class="d-flex gap-2" style="max-width: 320px">
                    <?= csrf_field() ?>
                    <input type="hidden" name="produk_id" value="<?= $produk['id'] ?>">
                    <input type="number" class="form-control" name="qty" value="1" min="1" max="<?= $produk['stok'] ?>" style="width: 90px">
                    <button class="btn btn-primary flex-grow-1"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button>
                </form>
            <?php endif ?>
        </div>
    </div>
</div>
<?php if ($terkait): ?>
    <h2 class="h5 fw-bold mb-3">Produk Terkait</h2>
    <div class="row g-3">
        <?php foreach ($terkait as $p): ?><div class="col-6 col-md-3"><?= view('toko/_card', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
