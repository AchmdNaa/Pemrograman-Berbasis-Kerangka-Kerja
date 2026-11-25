<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php $edit = $product !== null; $err = session('errors') ?? []; $v = static fn ($k, $d = '') => old($k, $product[$k] ?? $d); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0"><?= esc($title) ?></h1>
    <a href="<?= url_to('admin.products') ?>" class="btn btn-light">&larr; Kembali</a>
</div>
<form action="<?= $edit ? url_to('admin.products.update', $product['id']) : url_to('admin.products') ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-xl-8">
            <div class="card p-4">
                <div class="mb-3">
                    <label class="form-label" for="name">Nama produk</label>
                    <input class="form-control <?= isset($err['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc($v('name')) ?>">
                    <div class="invalid-feedback"><?= esc($err['name'] ?? '') ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="slug">Slug URL <span class="text-muted small">(kosongkan agar dibuat otomatis dari nama)</span></label>
                    <div class="input-group has-validation">
                        <span class="input-group-text small">/produk/</span>
                        <input class="form-control <?= isset($err['slug']) ? 'is-invalid' : '' ?>" id="slug" name="slug" value="<?= esc($v('slug')) ?>">
                        <div class="invalid-feedback"><?= esc($err['slug'] ?? '') ?></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="category_id">Kategori</label>
                    <select class="form-select <?= isset($err['category_id']) ? 'is-invalid' : '' ?>" id="category_id" name="category_id">
                        <option value="">— pilih kategori —</option>
                        <?php foreach ($categories as $c): ?><option value="<?= $c['id'] ?>" <?= (string) $v('category_id') === (string) $c['id'] ? 'selected' : '' ?>><?= esc($c['name']) ?></option><?php endforeach ?>
                    </select>
                    <div class="invalid-feedback"><?= esc($err['category_id'] ?? '') ?></div>
                </div>
                <div class="row">
                    <?php foreach (['price' => 'Harga jual (Rp)', 'old_price' => 'Harga coret (opsional)', 'stock' => 'Stok'] as $f => $label): ?>
                        <div class="col-md-4 mb-3">
                            <label class="form-label" for="<?= $f ?>"><?= $label ?></label>
                            <input type="number" min="0" class="form-control <?= isset($err[$f]) ? 'is-invalid' : '' ?>" id="<?= $f ?>" name="<?= $f ?>" value="<?= esc($v($f, $f === 'stock' ? 0 : '')) ?>">
                            <div class="invalid-feedback"><?= esc($err[$f] ?? '') ?></div>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="description">Deskripsi</label>
                    <textarea class="form-control" id="description" name="description" rows="5"><?= esc($v('description')) ?></textarea>
                </div>
                <div class="d-flex gap-4">
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= $v('is_active', 1) ? 'checked' : '' ?>><label class="form-check-label" for="is_active">Tampilkan di katalog</label></div>
                    <div class="form-check form-switch"><input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" <?= $v('is_featured', 0) ? 'checked' : '' ?>><label class="form-check-label" for="is_featured">Produk unggulan</label></div>
                </div>
            </div>
        </div>
        <div class="col-xl-4">
            <div class="card p-4 text-center">
                <label class="form-label fw-semibold">Gambar produk</label>
                <img id="preview" src="<?= $edit ? product_image($product) : base_url('assets/img/no-image.svg') ?>" class="w-100 rounded-3 mb-3 border" style="aspect-ratio: 1/1; object-fit: cover" alt="">
                <input type="file" class="form-control <?= isset($err['image']) ? 'is-invalid' : '' ?>" name="image" accept="image/png,image/jpeg,image/webp"
                       onchange="if (this.files[0]) preview.src = URL.createObjectURL(this.files[0])">
                <div class="invalid-feedback"><?= esc($err['image'] ?? '') ?></div>
                <div class="form-text">JPG, PNG, atau WEBP · maks 2 MB</div>
                <?php if ($edit && $product['image']): ?>
                    <div class="form-check text-start mt-2"><input class="form-check-input" type="checkbox" name="remove_image" value="1" id="remove_image"><label class="form-check-label small" for="remove_image">Hapus gambar (pakai ilustrasi otomatis)</label></div>
                <?php endif ?>
            </div>
            <button class="btn btn-primary btn-lg w-100 mt-3"><i class="bi bi-save"></i> <?= $edit ? 'Simpan Perubahan' : 'Simpan Produk' ?></button>
        </div>
    </div>
</form>
<?= $this->endSection() ?>
