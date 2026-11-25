<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<?php $edit = $mhs !== null; ?>
<div class="card p-4" style="max-width: 760px">
    <form action="<?= site_url($edit ? 'admin/mahasiswa/' . $mhs['id'] : 'admin/mahasiswa') ?>" method="post" enctype="multipart/form-data">
        <?= csrf_field() ?>
        <div class="row g-3">
            <div class="col-md-8">
                <div class="mb-3">
                    <label class="form-label" for="nim">NIM</label>
                    <input class="form-control" id="nim" name="nim" maxlength="10" value="<?= esc(old('nim', $mhs['nim'] ?? '')) ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="nama">Nama Lengkap</label>
                    <input class="form-control" id="nama" name="nama" value="<?= esc(old('nama', $mhs['nama'] ?? '')) ?>" required>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label class="form-label" for="prodi">Program Studi</label>
                        <select class="form-select" id="prodi" name="prodi" required>
                            <option value="">— pilih —</option>
                            <?php foreach ($daftarProdi as $p): ?>
                                <option <?= old('prodi', $mhs['prodi'] ?? '') === $p ? 'selected' : '' ?>><?= esc($p) ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label" for="angkatan">Angkatan</label>
                        <input type="number" class="form-control" id="angkatan" name="angkatan" min="2000" max="2100" value="<?= esc(old('angkatan', $mhs['angkatan'] ?? date('Y'))) ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="email">Email <span class="text-muted small">(opsional)</span></label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email', $mhs['email'] ?? '')) ?>">
                </div>
            </div>
            <div class="col-md-4 text-center">
                <label class="form-label d-block">Foto</label>
                <img id="preview" src="<?= $edit ? foto_mhs($mhs) : placeholder_img('Foto', '#94a3b8') ?>" class="rounded mb-2" style="width: 160px; height: 160px; object-fit: cover" alt="">
                <input type="file" class="form-control form-control-sm" name="foto" accept="image/*"
                       onchange="if (this.files[0]) preview.src = URL.createObjectURL(this.files[0])">
                <div class="form-text">JPG/PNG/WEBP, maks 1 MB.<?= $edit ? ' Kosongkan bila tidak diganti.' : '' ?></div>
            </div>
        </div>
        <div class="d-flex gap-2 mt-2">
            <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
            <a href="<?= site_url('admin/mahasiswa') ?>" class="btn btn-light">Batal</a>
        </div>
    </form>
</div>
<?= $this->endSection() ?>
