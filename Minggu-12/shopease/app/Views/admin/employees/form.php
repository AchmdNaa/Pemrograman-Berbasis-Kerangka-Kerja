<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php $edit = $e !== null; $err = session('errors') ?? []; $v = static fn ($k, $d = '') => old($k, $e[$k] ?? $d); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0"><?= esc($title) ?></h1>
    <a href="<?= url_to('admin.employees') ?>" class="btn btn-light">&larr; Kembali</a>
</div>
<form action="<?= $edit ? url_to('admin.employees.update', $e['id']) : url_to('admin.employees') ?>" method="post" enctype="multipart/form-data" novalidate>
    <?= csrf_field() ?>
    <div class="card p-4">
        <div class="row g-3">
            <?php
            $input = static function (string $f, string $label, string $type = 'text', string $attr = '', string $col = 'col-md-6') use ($err, $v) { ?>
                <div class="<?= $col ?>">
                    <label class="form-label" for="<?= $f ?>"><?= $label ?></label>
                    <input type="<?= $type ?>" class="form-control <?= isset($err[$f]) ? 'is-invalid' : '' ?>" id="<?= $f ?>" name="<?= $f ?>" value="<?= esc($v($f)) ?>" <?= $attr ?>>
                    <div class="invalid-feedback"><?= esc($err[$f] ?? '') ?></div>
                </div>
            <?php };
            $input('nip', 'NIP', 'text', 'placeholder="SE2026001" maxlength="9"', 'col-md-4');
            $input('name', 'Nama lengkap', 'text', '', 'col-md-8');
            $input('position', 'Jabatan');
            ?>
            <div class="col-md-6">
                <label class="form-label" for="department">Departemen</label>
                <select class="form-select <?= isset($err['department']) ? 'is-invalid' : '' ?>" id="department" name="department">
                    <option value="">— pilih —</option>
                    <?php foreach ($departments as $d): ?><option <?= $v('department') === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach ?>
                </select>
                <div class="invalid-feedback"><?= esc($err['department'] ?? '') ?></div>
            </div>
            <?php
            $input('email', 'Email', 'email');
            $input('phone', 'Nomor HP', 'tel', 'placeholder="08xxxxxxxxxx"');
            $input('hire_date', 'Tanggal masuk', 'date', '', 'col-md-4');
            $input('salary', 'Gaji pokok (Rp)', 'number', 'min="0" step="50000"', 'col-md-4');
            ?>
            <div class="col-md-4">
                <label class="form-label" for="status">Status</label>
                <select class="form-select <?= isset($err['status']) ? 'is-invalid' : '' ?>" id="status" name="status">
                    <?php foreach ($statuses as $s): ?><option value="<?= $s ?>" <?= $v('status', 'aktif') === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach ?>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="photo">Foto <span class="text-muted small">(opsional, maks 1 MB)</span></label>
                <input type="file" class="form-control <?= isset($err['photo']) ? 'is-invalid' : '' ?>" id="photo" name="photo" accept="image/png,image/jpeg,image/webp">
                <div class="invalid-feedback"><?= esc($err['photo'] ?? '') ?></div>
            </div>
        </div>
        <div class="mt-4"><button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button></div>
    </div>
</form>
<?= $this->endSection() ?>
