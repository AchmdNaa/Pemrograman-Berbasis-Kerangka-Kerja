<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0">Manajemen Produk</h1>
    <a href="<?= url_to('admin.products.new') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
</div>
<div class="card p-3">
    <form class="row g-2 mb-3" method="get">
        <div class="col-md-5"><input type="search" name="q" value="<?= esc($q) ?>" class="form-control form-control-sm" placeholder="Cari nama produk"></div>
        <div class="col-md-3">
            <select name="kategori" class="form-select form-select-sm">
                <option value="">Semua kategori</option>
                <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= $cat == $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <?php foreach (['' => 'Semua status', 'aktif' => 'Aktif', 'nonaktif' => 'Nonaktif', 'habis' => 'Stok habis'] as $v => $l): ?>
                    <option value="<?= $v ?>" <?= $status === $v ? 'selected' : '' ?>><?= $l ?></option>
                <?php endforeach ?>
            </select>
        </div>
        <div class="col-md-1"><button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel"></i></button></div>
    </form>
    <div class="table-responsive">
        <table class="table align-middle small">
            <thead class="table-light"><tr><th>Produk</th><th>Kategori</th><th class="text-end">Harga</th><th class="text-center">Stok</th><th class="text-center">Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php if ($products === []): ?><tr><td colspan="6" class="text-center text-muted py-4">Tidak ada produk.</td></tr><?php endif ?>
            <?php foreach ($products as $p): ?>
                <tr class="<?= $p['is_active'] ? '' : 'table-secondary' ?>">
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= product_image($p) ?>" width="44" height="44" class="rounded object-fit-cover" alt="">
                            <div><div class="fw-semibold"><?= esc($p['name']) ?> <?= $p['is_featured'] ? '<i class="bi bi-star-fill text-warning" title="Unggulan"></i>' : '' ?></div><code class="small text-muted"><?= esc($p['slug']) ?></code></div>
                        </div>
                    </td>
                    <td><?= esc($p['category']) ?></td>
                    <td class="text-end"><?= rupiah($p['price']) ?><?php if ($p['old_price']): ?><div class="price-old"><?= rupiah($p['old_price']) ?></div><?php endif ?></td>
                    <td class="text-center"><span class="badge <?= $p['stock'] == 0 ? 'text-bg-danger' : ($p['stock'] < 10 ? 'text-bg-warning' : 'text-bg-light border') ?>"><?= $p['stock'] ?></span></td>
                    <td class="text-center">
                        <form action="<?= url_to('admin.products.toggle', $p['id']) ?>" method="post" class="d-inline"><?= csrf_field() ?>
                            <button class="btn btn-sm <?= $p['is_active'] ? 'btn-success' : 'btn-outline-secondary' ?> py-0" title="Klik untuk mengubah"><?= $p['is_active'] ? 'Aktif' : 'Nonaktif' ?></button>
                        </form>
                    </td>
                    <td class="text-end text-nowrap">
                        <a href="<?= url_to('product', $p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary" title="Lihat di toko"><i class="bi bi-eye"></i></a>
                        <a href="<?= url_to('admin.products.edit', $p['id']) ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <form action="<?= url_to('admin.products.delete', $p['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus produk <?= esc($p['name'], 'js') ?>?')"><?= csrf_field() ?>
                            <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                        </form>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links('default', 'bootstrap') ?>
</div>
<?= $this->endSection() ?>
