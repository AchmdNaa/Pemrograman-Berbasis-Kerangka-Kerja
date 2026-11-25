<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3"><i class="bi bi-cart3"></i> Keranjang Belanja</h1>
<?php foreach ($catatan as $c): ?><div class="alert alert-warning py-2"><?= esc($c) ?></div><?php endforeach ?>

<?php if ($items === []): ?>
    <div class="card p-5 text-center text-muted">
        <i class="bi bi-cart-x fs-1"></i>
        <p class="mb-3">Keranjang Anda masih kosong.</p>
        <a href="<?= site_url('toko') ?>" class="btn btn-primary align-self-center">Mulai Belanja</a>
    </div>
<?php else: ?>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card p-3">
                <form id="formUpdate" action="<?= site_url('keranjang/update') ?>" method="post"><?= csrf_field() ?></form>
                <table class="table align-middle">
                    <thead class="table-light"><tr><th>Produk</th><th>Harga</th><th style="width: 110px">Jumlah</th><th class="text-end">Subtotal</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($items as $i): ?>
                        <tr>
                            <td><div class="d-flex align-items-center gap-2"><img src="<?= produk_img($i) ?>" style="width: 56px; height: 42px; object-fit: cover" class="rounded" alt=""><a href="<?= site_url('toko/' . $i['slug']) ?>" class="text-dark fw-semibold"><?= esc($i['nama']) ?></a></div></td>
                            <td><?= rupiah($i['harga']) ?></td>
                            <td><input form="formUpdate" type="number" name="qty[<?= $i['id'] ?>]" value="<?= $i['qty'] ?>" min="0" max="<?= $i['stok'] ?>" class="form-control form-control-sm"></td>
                            <td class="text-end fw-semibold"><?= rupiah($i['subtotal']) ?></td>
                            <td class="text-end">
                                <form action="<?= site_url('keranjang/hapus/' . $i['id']) ?>" method="post"><?= csrf_field() ?><button class="btn btn-sm btn-link text-danger"><i class="bi bi-trash"></i></button></form>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
                <div class="d-flex gap-2">
                    <button form="formUpdate" class="btn btn-outline-primary btn-sm"><i class="bi bi-arrow-repeat"></i> Perbarui Jumlah</button>
                    <form action="<?= site_url('keranjang/kosongkan') ?>" method="post" onsubmit="return confirm('Kosongkan keranjang?')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm">Kosongkan</button></form>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card p-4">
                <h2 class="h6 fw-bold">Ringkasan</h2>
                <div class="d-flex justify-content-between"><span>Total (<?= array_sum(array_column($items, 'qty')) ?> barang)</span><span class="fw-bold fs-5"><?= rupiah($total) ?></span></div>
                <a href="<?= site_url('checkout') ?>" class="btn btn-primary w-100 mt-3">Lanjut ke Checkout <i class="bi bi-arrow-right"></i></a>
            </div>
        </div>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
