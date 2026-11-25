<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="hero p-5 mb-5 text-center">
    <h1 class="display-6 fw-bold">Tentang ShopEase</h1>
    <p class="lead opacity-75 mx-auto" style="max-width: 640px">Kami percaya belanja online harus sederhana, aman, dan terjangkau, terutama untuk mahasiswa dan keluarga muda.</p>
</section>
<div class="row g-4 mb-5">
    <div class="col-lg-6">
        <h2 class="h4 section-title">Cerita Kami</h2>
        <p class="text-secondary">ShopEase berawal dari proyek mata kuliah Pemrograman Berbasis Kerangka Kerja. Tujuannya membangun toko online yang rapi menggunakan CodeIgniter 4 dengan pola MVC: <em>routing</em> yang jelas, <em>controller</em> yang ringkas, <em>model</em> yang mengelola data, dan <em>view</em> yang dapat dipakai ulang.</p>
        <p class="text-secondary">Saat ini katalog ShopEase berisi produk elektronik, fashion, perlengkapan rumah, olahraga, serta buku dan alat tulis.</p>
    </div>
    <div class="col-lg-6">
        <div class="row g-3">
            <?php foreach ([['16+', 'Produk aktif'], ['5', 'Kategori'], ['7.000+', 'Barang terjual'], ['4.7', 'Rata-rata rating']] as [$v, $l]): ?>
                <div class="col-6"><div class="card p-4 text-center"><div class="fs-2 fw-bold text-orange"><?= $v ?></div><div class="text-muted small"><?= $l ?></div></div></div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<h2 class="h4 section-title mb-3">Nilai yang Kami Pegang</h2>
<div class="row g-3">
    <?php foreach ([['bi-hand-thumbs-up', 'Jujur', 'Harga dan stok ditampilkan apa adanya.'], ['bi-lightning', 'Cepat', 'Pesanan diproses di hari yang sama.'], ['bi-people', 'Peduli', 'Layanan pelanggan yang responsif.']] as [$i, $t, $d]): ?>
        <div class="col-md-4"><div class="card p-4 h-100"><span class="feature-icon mb-3"><i class="bi <?= $i ?>"></i></span><div class="fw-semibold"><?= $t ?></div><div class="small text-muted"><?= $d ?></div></div></div>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>
