<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<section class="hero p-5 mb-4">
    <div class="row align-items-center">
        <div class="col-lg-7">
            <span class="badge bg-light text-primary mb-2">Latihan 1 &amp; 2 · CodeIgniter 4</span>
            <h1 class="display-6 fw-bold">Satu aplikasi, dua modul: SIAKAD dan Toko Online</h1>
            <p class="lead opacity-75 mb-4">Dibangun dengan pola MVC CodeIgniter 4: routing, controller, view berbasis Bootstrap, model &amp; CRUD, upload file, dan hak akses pengguna.</p>
            <a href="<?= site_url('toko') ?>" class="btn btn-light btn-lg me-2"><i class="bi bi-shop"></i> Belanja Sekarang</a>
            <a href="<?= site_url('admin') ?>" class="btn btn-outline-light btn-lg"><i class="bi bi-mortarboard"></i> Buka SIAKAD</a>
        </div>
        <div class="col-lg-5 d-none d-lg-block text-center">
            <i class="bi bi-buildings" style="font-size: 9rem; opacity: .35"></i>
        </div>
    </div>
</section>

<div class="row g-4 mb-4">
    <div class="col-md-6">
        <div class="card h-100 p-4">
            <div class="d-flex gap-3">
                <i class="bi bi-people-fill fs-1 text-primary"></i>
                <div>
                    <h2 class="h5 fw-bold">SIAKAD</h2>
                    <p class="text-muted mb-2">Kelola data mahasiswa beserta foto, mata kuliah, input nilai, dan perhitungan IPK otomatis.</p>
                    <span class="badge badge-soft"><?= $jumlahMhs ?> mahasiswa terdaftar</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card h-100 p-4">
            <div class="d-flex gap-3">
                <i class="bi bi-bag-heart-fill fs-1 text-danger"></i>
                <div>
                    <h2 class="h5 fw-bold">Toko Online</h2>
                    <p class="text-muted mb-2">Katalog produk, keranjang belanja berbasis session, checkout, dan pengelolaan pesanan oleh admin.</p>
                    <a href="<?= site_url('toko') ?>" class="small fw-semibold">Lihat katalog <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>
</div>

<h2 class="h5 fw-bold mb-3">Produk Terbaru</h2>
<div class="row g-3">
    <?php foreach ($produkTerbaru as $p): ?>
        <div class="col-6 col-md-3"><?= view('toko/_card', ['p' => $p]) ?></div>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>
