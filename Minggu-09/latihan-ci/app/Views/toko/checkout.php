<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Checkout</h1>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4">
            <h2 class="h6 fw-bold">Data Pengiriman</h2>
            <form action="<?= site_url('checkout') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-3"><label class="form-label">Nama Penerima</label><input class="form-control" name="nama_pembeli" value="<?= esc(old('nama_pembeli')) ?>" required></div>
                <div class="mb-3"><label class="form-label">No. Telepon</label><input class="form-control" name="telepon" placeholder="08xxxxxxxxxx" value="<?= esc(old('telepon')) ?>" required></div>
                <div class="mb-3"><label class="form-label">Alamat Lengkap</label><textarea class="form-control" name="alamat" rows="3" required><?= esc(old('alamat')) ?></textarea></div>
                <button class="btn btn-primary btn-lg w-100"><i class="bi bi-bag-check"></i> Buat Pesanan</button>
            </form>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card p-4">
            <h2 class="h6 fw-bold">Pesanan Anda</h2>
            <?php foreach ($items as $i): ?>
                <div class="d-flex justify-content-between small border-bottom py-2"><span><?= esc($i['nama']) ?> × <?= $i['qty'] ?></span><span><?= rupiah($i['subtotal']) ?></span></div>
            <?php endforeach ?>
            <div class="d-flex justify-content-between fw-bold mt-2"><span>Total</span><span class="text-primary"><?= rupiah($total) ?></span></div>
            <div class="form-text">Pembayaran dilakukan saat barang diterima (COD).</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
