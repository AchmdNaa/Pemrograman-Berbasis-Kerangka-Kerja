<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $disc = discount_percent($product); $stock = (int) $product['stock']; ?>
<nav class="small mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url_to('home') ?>">Beranda</a></li>
        <li class="breadcrumb-item"><a href="<?= url_to('products') ?>">Produk</a></li>
        <li class="breadcrumb-item"><a href="<?= url_to('category', $product['category_slug']) ?>"><?= esc($product['category']) ?></a></li>
        <li class="breadcrumb-item active"><?= esc($product['name']) ?></li>
    </ol>
</nav>

<div class="card p-4 mb-5">
    <div class="row g-4">
        <div class="col-md-5">
            <img src="<?= product_image($product) ?>" class="w-100 rounded-4" alt="<?= esc($product['name']) ?>">
        </div>
        <div class="col-md-7">
            <a href="<?= url_to('category', $product['category_slug']) ?>" class="badge rounded-pill text-bg-light border mb-2"><?= esc($product['category']) ?></a>
            <h1 class="h3 fw-bold"><?= esc($product['name']) ?></h1>
            <div class="d-flex align-items-center gap-2 small text-muted mb-3">
                <?= stars($product['rating']) ?> <strong class="text-dark"><?= number_format((float) $product['rating'], 1) ?></strong>
                · <?= number_format((int) $product['sold'], 0, ',', '.') ?> terjual
            </div>
            <div class="p-3 rounded-3 mb-3" style="background: var(--se-soft)">
                <span class="fs-2 fw-bold text-orange"><?= rupiah($product['price']) ?></span>
                <?php if ($disc): ?>
                    <span class="price-old ms-2 fs-6"><?= rupiah($product['old_price']) ?></span>
                    <span class="badge bg-danger ms-1">-<?= $disc ?>%</span>
                <?php endif ?>
            </div>
            <p class="text-secondary"><?= nl2br(esc($product['description'])) ?></p>
            <table class="table table-sm small w-auto">
                <tr><th class="pe-4 text-muted fw-normal">Kategori</th><td><?= esc($product['category']) ?></td></tr>
                <tr><th class="pe-4 text-muted fw-normal">Stok</th><td class="<?= $stock > 0 ? 'text-success' : 'text-danger' ?> fw-semibold"><?= $stock > 0 ? $stock . ' tersedia' : 'Habis' ?></td></tr>
                <tr><th class="pe-4 text-muted fw-normal">Kode</th><td><code>SE-<?= str_pad((string) $product['id'], 4, '0', STR_PAD_LEFT) ?></code></td></tr>
            </table>
            <div class="d-flex gap-2">
                <button class="btn btn-primary btn-lg px-4" <?= $stock > 0 ? '' : 'disabled' ?> title="Keranjang tersedia setelah fitur akun & transaksi"><i class="bi bi-cart-plus"></i> Tambah ke Keranjang</button>
                <button class="btn btn-outline-secondary btn-lg"><i class="bi bi-heart"></i></button>
            </div>
        </div>
    </div>
</div>

<?php if ($related): ?>
    <h2 class="h4 section-title mb-3">Produk Serupa</h2>
    <div class="row g-3">
        <?php foreach ($related as $p): ?><div class="col-6 col-md-3"><?= view('partials/product_card', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
