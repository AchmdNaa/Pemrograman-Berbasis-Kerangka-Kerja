<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-4">
            <h2 class="h6 fw-bold mb-3">Item Pesanan</h2>
            <table class="table">
                <thead class="table-light"><tr><th>Produk</th><th>Harga</th><th>Qty</th><th class="text-end">Subtotal</th></tr></thead>
                <tbody>
                <?php foreach ($items as $i): ?>
                    <tr><td><?= esc($i['nama_produk']) ?></td><td><?= rupiah($i['harga']) ?></td><td><?= $i['qty'] ?></td><td class="text-end"><?= rupiah($i['subtotal']) ?></td></tr>
                <?php endforeach ?>
                </tbody>
                <tfoot><tr class="fw-bold"><td colspan="3">Total</td><td class="text-end"><?= rupiah($pesanan['total']) ?></td></tr></tfoot>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <div class="d-flex justify-content-between"><span class="text-muted">Status</span><?= view('admin/pesanan/_badge', ['status' => $pesanan['status']]) ?></div>
            <hr>
            <div class="small text-muted">Pembeli</div><div class="fw-semibold"><?= esc($pesanan['nama_pembeli']) ?></div>
            <div class="small"><?= esc($pesanan['telepon']) ?></div>
            <div class="small text-muted mt-2">Alamat</div><div class="small"><?= nl2br(esc($pesanan['alamat'])) ?></div>
            <div class="small text-muted mt-2">Tanggal</div><div class="small"><?= date('d/m/Y H:i', strtotime($pesanan['created_at'])) ?></div>
            <?php if (! in_array($pesanan['status'], ['selesai', 'batal'], true)): ?>
                <hr>
                <form action="<?= site_url('admin/pesanan/' . $pesanan['id'] . '/status') ?>" method="post" class="d-flex gap-2">
                    <?= csrf_field() ?>
                    <select name="status" class="form-select">
                        <?php foreach (\App\Models\PesananModel::STATUS as $s): ?>
                            <option <?= $s === $pesanan['status'] ? 'selected' : '' ?>><?= $s ?></option>
                        <?php endforeach ?>
                    </select>
                    <button class="btn btn-primary">Ubah</button>
                </form>
                <div class="form-text">Status "batal" otomatis mengembalikan stok.</div>
            <?php endif ?>
        </div>
        <a href="<?= site_url('admin/pesanan') ?>" class="btn btn-light mt-3">&larr; Kembali</a>
    </div>
</div>
<?= $this->endSection() ?>
