<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card p-3">
    <div class="d-flex justify-content-between mb-3 gap-2">
        <form class="d-flex gap-2" method="get">
            <input type="search" name="q" value="<?= esc($q) ?>" class="form-control" placeholder="Cari produk...">
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
        <a href="<?= site_url('admin/produk/new') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Produk</a>
    </div>
    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light"><tr><th>Produk</th><th>Kategori</th><th>Harga</th><th>Stok</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($produk as $p): ?>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><img src="<?= produk_img($p) ?>" style="width: 56px; height: 42px; object-fit: cover" class="rounded" alt=""><span class="fw-semibold"><?= esc($p['nama']) ?></span></div></td>
                    <td><span class="badge badge-soft"><?= esc($p['kategori']) ?></span></td>
                    <td><?= rupiah($p['harga']) ?></td>
                    <td><?= $p['stok'] > 0 ? $p['stok'] : '<span class="badge bg-danger">Habis</span>' ?></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= site_url('toko/' . $p['slug']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                        <a href="<?= site_url('admin/produk/' . $p['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <?php if (is_admin()): ?>
                            <form action="<?= site_url('admin/produk/' . $p['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus produk ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links('default', 'bootstrap') ?>
</div>
<?= $this->endSection() ?>
