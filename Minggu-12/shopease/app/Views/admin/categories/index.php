<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php $err = session('errors') ?? []; ?>
<h1 class="h4 fw-bold mb-3">Kategori Produk</h1>
<div class="row g-4">
    <div class="col-xl-7">
        <div class="card p-3">
            <table class="table align-middle small mb-0">
                <thead class="table-light"><tr><th>Kategori</th><th>Slug</th><th class="text-center">Produk</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($categories as $c): ?>
                    <tr>
                        <td><i class="bi <?= esc($c['icon']) ?> text-orange me-1"></i> <span class="fw-semibold"><?= esc($c['name']) ?></span><div class="text-muted"><?= esc($c['description']) ?></div></td>
                        <td><code><?= esc($c['slug']) ?></code></td>
                        <td class="text-center"><?= $c['total'] ?></td>
                        <td class="text-end text-nowrap">
                            <a href="?edit=<?= $c['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="<?= url_to('admin.categories.delete', $c['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus kategori ini?')"><?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card p-4">
            <h2 class="h6 fw-bold"><?= $edit ? 'Edit Kategori' : 'Tambah Kategori' ?></h2>
            <form action="<?= url_to('admin.categories') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama</label>
                    <input class="form-control <?= isset($err['name']) || isset($err['slug']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name', $edit['name'] ?? '')) ?>">
                    <div class="invalid-feedback"><?= esc($err['name'] ?? $err['slug'] ?? '') ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="icon">Ikon <a href="https://icons.getbootstrap.com" target="_blank" class="small">(Bootstrap Icons)</a></label>
                    <input class="form-control <?= isset($err['icon']) ? 'is-invalid' : '' ?>" id="icon" name="icon" placeholder="bi-tag" value="<?= esc(old('icon', $edit['icon'] ?? 'bi-tag')) ?>">
                    <div class="invalid-feedback"><?= esc($err['icon'] ?? '') ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Deskripsi</label>
                    <input class="form-control" id="description" name="description" value="<?= esc(old('description', $edit['description'] ?? '')) ?>">
                </div>
                <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                <?php if ($edit): ?><a href="<?= url_to('admin.categories') ?>" class="btn btn-light">Batal</a><?php endif ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
