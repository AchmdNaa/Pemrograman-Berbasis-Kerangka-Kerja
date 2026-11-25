<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<!-- Hero -->
<section class="hero p-4 p-lg-5 mb-5">
    <div class="row align-items-center g-4">
        <div class="col-lg-7">
            <span class="badge rounded-pill px-3 py-2 mb-3"><i class="bi bi-lightning-charge-fill"></i> Promo Akhir Tahun · Diskon hingga 30%</span>
            <h1 class="display-5 fw-bold lh-sm">Belanja kebutuhan kuliah &amp; harian jadi <span class="text-orange">lebih mudah</span>.</h1>
            <p class="lead opacity-75 my-4">Ribuan produk pilihan dengan harga ramah, pembayaran aman, dan pengiriman cepat ke seluruh Surabaya.</p>
            <div class="d-flex flex-wrap gap-2">
                <a href="<?= url_to('products') ?>" class="btn btn-primary btn-lg px-4"><i class="bi bi-bag"></i> Mulai Belanja</a>
                <a href="<?= url_to('products') ?>?urut=terlaris" class="btn btn-outline-light btn-lg px-4">Lihat Terlaris</a>
            </div>
        </div>
        <div class="col-lg-5">
            <?php if ($featured): $hero = $featured[0]; ?>
                <a href="<?= url_to('product', $hero['slug']) ?>" class="hero-card d-flex gap-3 p-3 text-white">
                    <img src="<?= product_image($hero) ?>" class="rounded-3" width="130" height="130" alt="">
                    <div>
                        <div class="small text-uppercase opacity-75">Produk unggulan</div>
                        <div class="fw-bold fs-5 lh-sm my-1"><?= esc($hero['name']) ?></div>
                        <div class="fs-4 fw-bold text-orange"><?= rupiah($hero['price']) ?></div>
                        <?php if ($d = discount_percent($hero)): ?><span class="badge bg-danger">Hemat <?= $d ?>%</span><?php endif ?>
                    </div>
                </a>
            <?php endif ?>
            <div class="row g-2 mt-1 small">
                <div class="col-4"><div class="hero-card p-2 text-center"><div class="fw-bold fs-5">16+</div>Produk</div></div>
                <div class="col-4"><div class="hero-card p-2 text-center"><div class="fw-bold fs-5">4.7</div>Rating</div></div>
                <div class="col-4"><div class="hero-card p-2 text-center"><div class="fw-bold fs-5">24 jam</div>Kirim</div></div>
            </div>
        </div>
    </div>
</section>

<!-- Kategori -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h4 section-title mb-0">Belanja per Kategori</h2>
    </div>
    <div class="row g-3">
        <?php foreach ($categories as $c): ?>
            <div class="col-6 col-md-4 col-lg">
                <a href="<?= url_to('category', $c['slug']) ?>" class="category-tile d-flex align-items-center gap-3 p-3 h-100 text-dark">
                    <span class="icon"><i class="bi <?= esc($c['icon']) ?>"></i></span>
                    <span><span class="fw-semibold d-block"><?= esc($c['name']) ?></span><span class="small text-muted"><?= $c['total'] ?> produk</span></span>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- Unggulan -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h4 section-title mb-0"><i class="bi bi-stars text-orange"></i> Produk Unggulan</h2>
        <a href="<?= url_to('products') ?>" class="small fw-semibold">Lihat semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($featured as $p): ?><div class="col-6 col-md-3"><?= view('partials/product_card', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
</section>

<!-- Keunggulan -->
<section class="mb-5">
    <div class="row g-3">
        <?php foreach ([
            ['bi-shield-check', 'Pembayaran Aman', 'Transaksi terlindungi dan diverifikasi admin.'],
            ['bi-truck', 'Pengiriman Cepat', 'Pesanan diproses di hari yang sama.'],
            ['bi-arrow-repeat', 'Garansi Retur 7 Hari', 'Tukar barang jika tidak sesuai.'],
            ['bi-chat-dots', 'Layanan Pelanggan', 'Tim kami siap membantu setiap hari.'],
        ] as [$icon, $t, $d]): ?>
            <div class="col-md-6 col-lg-3">
                <div class="card p-3 h-100 d-flex flex-row gap-3 align-items-start">
                    <span class="feature-icon flex-shrink-0"><i class="bi <?= $icon ?>"></i></span>
                    <span><span class="fw-semibold d-block"><?= $t ?></span><span class="small text-muted"><?= $d ?></span></span>
                </div>
            </div>
        <?php endforeach ?>
    </div>
</section>

<!-- Terbaru -->
<section class="mb-5">
    <div class="d-flex justify-content-between align-items-end mb-3">
        <h2 class="h4 section-title mb-0">Produk Terbaru</h2>
        <a href="<?= url_to('products') ?>?urut=terbaru" class="small fw-semibold">Lihat semua <i class="bi bi-arrow-right"></i></a>
    </div>
    <div class="row g-3">
        <?php foreach ($latest as $p): ?><div class="col-6 col-md-3"><?= view('partials/product_card', ['p' => $p]) ?></div><?php endforeach ?>
    </div>
</section>

<!-- Terlaris -->
<section>
    <h2 class="h4 section-title mb-3"><i class="bi bi-fire text-danger"></i> Paling Laris</h2>
    <div class="row g-3">
        <?php foreach ($bestSellers as $i => $p): ?>
            <div class="col-md-6 col-lg-3">
                <a href="<?= url_to('product', $p['slug']) ?>" class="card p-2 d-flex flex-row align-items-center gap-2 text-dark h-100">
                    <span class="fs-3 fw-bold text-orange px-2">#<?= $i + 1 ?></span>
                    <img src="<?= product_image($p) ?>" width="64" height="64" class="rounded" alt="">
                    <span class="small"><span class="fw-semibold d-block"><?= esc($p['name']) ?></span><?= number_format((int) $p['sold'], 0, ',', '.') ?> terjual</span>
                </a>
            </div>
        <?php endforeach ?>
    </div>
</section>
<?= $this->endSection() ?>
