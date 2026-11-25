<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="card p-5 mx-auto" style="max-width: 640px">
    <div class="text-center">
        <i class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem"></i>
        <h1 class="h4 fw-bold mt-2">Pesanan Diterima</h1>
        <p class="text-muted">Simpan kode pesanan berikut untuk konfirmasi.</p>
        <div class="fs-4 fw-bold font-monospace mb-3"><?= esc($pesanan['kode']) ?></div>
    </div>
    <?php foreach ($items as $i): ?>
        <div class="d-flex justify-content-between small border-bottom py-2"><span><?= esc($i['nama_produk']) ?> × <?= $i['qty'] ?></span><span><?= rupiah($i['subtotal']) ?></span></div>
    <?php endforeach ?>
    <div class="d-flex justify-content-between fw-bold mt-2"><span>Total</span><span><?= rupiah($pesanan['total']) ?></span></div>
    <div class="small text-muted mt-3">Dikirim ke: <?= esc($pesanan['nama_pembeli']) ?> (<?= esc($pesanan['telepon']) ?>) — <?= esc($pesanan['alamat']) ?></div>
    <a href="<?= site_url('toko') ?>" class="btn btn-primary mt-4">Kembali Belanja</a>
</div>
<?= $this->endSection() ?>
