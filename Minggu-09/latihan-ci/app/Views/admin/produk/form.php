<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $edit = $produk !== null; ?>
<div class="card p-4" style="max-width: 820px">
    <form action="<?= site_url($edit ? 'admin/produk/' . $produk['id'] : 'admin/produk') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-8">
                <div class="mb-3"><label class="form-label">Nama Produk</label><input class="form-control" name="nama" value="<?= esc(old('nama', $produk['nama'] ?? '')) ?>" required></div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-select" name="kategori_id" required>
                        <option value="">— pilih —</option>
                        <?php foreach ($kategori as $k): ?>
                            <option value="<?= $k['id'] ?>" <?= (string) old('kategori_id', $produk['kategori_id'] ?? '') === (string) $k['id'] ? 'selected' : '' ?>><?= esc($k['nama']) ?></option>
                        <?php endforeach ?>
                    </select>
                </div>
                <div class="row">
                    <div class="col mb-3"><label class="form-label">Harga (Rp)</label><input type="number" min="1" class="form-control" name="harga" value="<?= esc(old('harga', $produk['harga'] ?? '')) ?>" required></div>
                    <div class="col mb-3"><label class="form-label">Stok</label><input type="number" min="0" class="form-control" name="stok" value="<?= esc(old('stok', $produk['stok'] ?? 0)) ?>" required></div>
                </div>
                <div class="mb-3"><label class="form-label">Deskripsi</label><textarea class="form-control" name="deskripsi" rows="4"><?= esc(old('deskripsi', $produk['deskripsi'] ?? '')) ?></textarea></div>
            </div>
            <div class="col-md-4 text-center">
                <label class="form-label d-block">Gambar</label>
                <img id="preview" src="<?= $edit ? produk_img($produk) : placeholder_img('Gambar', '#94a3b8') ?>" class="rounded w-100 mb-2" style="aspect-ratio: 4/3; object-fit: cover" alt="">
                <input type="file" class="form-control form-control-sm" name="gambar" accept="image/*" onchange="if (this.files[0]) preview.src = URL.createObjectURL(this.files[0])">
                <div class="form-text">JPG/PNG/WEBP, maks 2 MB.</div>
            </div>
        </div>
        <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
        <a href="<?= site_url('admin/produk') ?>" class="btn btn-light">Batal</a>
    </form>
</div>
<?= $this->endSection() ?>
