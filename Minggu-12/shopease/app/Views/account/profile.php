<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php $err = session('errors') ?? []; ?>
<div class="row g-4">
    <div class="col-xl-6">
        <div class="card p-4 h-100">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-person"></i> Profil</h2>
            <form action="<?= url_to('account.profile') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <?php foreach (['name' => ['Nama lengkap', 'text'], 'email' => ['Email', 'email'], 'phone' => ['Nomor HP', 'tel']] as $f => [$label, $type]): ?>
                    <div class="mb-3">
                        <label class="form-label" for="<?= $f ?>"><?= $label ?></label>
                        <input type="<?= $type ?>" class="form-control <?= isset($err[$f]) ? 'is-invalid' : '' ?>" id="<?= $f ?>" name="<?= $f ?>" value="<?= esc(old($f, $user[$f] ?? '')) ?>">
                        <div class="invalid-feedback"><?= esc($err[$f] ?? '') ?></div>
                    </div>
                <?php endforeach ?>
                <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan Profil</button>
            </form>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card p-4 h-100">
            <h2 class="h6 fw-bold mb-3"><i class="bi bi-shield-lock"></i> Ganti Password</h2>
            <form action="<?= url_to('account.password') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <?php foreach (['current_password' => 'Password lama', 'password' => 'Password baru', 'password_confirm' => 'Ulangi password baru'] as $f => $label): ?>
                    <div class="mb-3">
                        <label class="form-label" for="<?= $f ?>"><?= $label ?></label>
                        <input type="password" class="form-control <?= isset($err[$f]) ? 'is-invalid' : '' ?>" id="<?= $f ?>" name="<?= $f ?>" autocomplete="<?= $f === 'current_password' ? 'current-password' : 'new-password' ?>">
                        <div class="invalid-feedback"><?= esc($err[$f] ?? '') ?></div>
                    </div>
                <?php endforeach ?>
                <div class="form-text mb-3">Minimal 8 karakter, berisi huruf dan angka, dan berbeda dari password lama.</div>
                <button class="btn btn-dark"><i class="bi bi-key"></i> Ganti Password</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
