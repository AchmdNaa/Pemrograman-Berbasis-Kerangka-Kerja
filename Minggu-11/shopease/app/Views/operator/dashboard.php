<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Stok Produk</h1>
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card stat p-3" style="--accent: #dc2626"><div class="small text-muted">Stok habis</div><div class="num"><?= $habis ?></div></div></div>
    <div class="col-md-6"><div class="card stat p-3" style="--accent: #f59e0b"><div class="small text-muted">Stok menipis (1–9)</div><div class="num"><?= $menipis ?></div></div></div>
</div>
<div class="card p-3">
    <form class="d-flex gap-2 mb-3" method="get" style="max-width: 360px">
        <input type="search" name="q" value="<?= esc($q) ?>" class="form-control form-control-sm" placeholder="Cari produk">
        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
    </form>
    <table class="table align-middle small">
        <thead class="table-light"><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th style="width: 220px">Stok</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><div class="d-flex align-items-center gap-2"><img src="<?= product_image($p) ?>" width="40" height="40" class="rounded" alt=""><span class="fw-semibold"><?= esc($p['name']) ?></span></div></td>
                <td><?= esc($p['category']) ?></td>
                <td><?= rupiah($p['price']) ?></td>
                <td>
                    <form action="<?= url_to('operator.stock', $p['id']) ?>" method="post" class="d-flex gap-2">
                        <?= csrf_field() ?>
                        <input type="number" min="0" name="stock" value="<?= $p['stock'] ?>" class="form-control form-control-sm <?= $p['stock'] == 0 ? 'is-invalid' : ($p['stock'] < 10 ? 'border-warning' : '') ?>">
                        <button class="btn btn-sm btn-outline-primary"><i class="bi bi-check-lg"></i></button>
                    </form>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?= $pager->links('default', 'bootstrap') ?>
</div>
<?= $this->endSection() ?>
