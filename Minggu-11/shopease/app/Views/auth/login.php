<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-1">Masuk ke ShopEase</h1>
<p class="text-muted mb-4">Belum punya akun? <a href="<?= url_to('register') ?>" class="fw-semibold">Daftar gratis</a></p>

<form action="<?= url_to('login') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <?php $err = session('errors') ?? []; ?>
    <div class="mb-3">
        <label class="form-label" for="email">Email</label>
        <input type="email" class="form-control form-control-lg <?= isset($err['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email')) ?>" autocomplete="email" autofocus>
        <div class="invalid-feedback"><?= esc($err['email'] ?? '') ?></div>
    </div>
    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <div class="input-group input-group-lg has-validation">
            <input type="password" class="form-control <?= isset($err['password']) ? 'is-invalid' : '' ?>" id="password" name="password" autocomplete="current-password">
            <button class="btn btn-outline-secondary" type="button" data-toggle-password="password" title="Tampilkan password"><i class="bi bi-eye"></i></button>
            <div class="invalid-feedback"><?= esc($err['password'] ?? '') ?></div>
        </div>
    </div>
    <button class="btn btn-primary btn-lg w-100 mt-2"><i class="bi bi-box-arrow-in-right"></i> Masuk</button>
</form>

<div class="card bg-light border-0 shadow-none mt-4 p-3 small">
    <div class="fw-semibold mb-1"><i class="bi bi-info-circle"></i> Akun demo</div>
    <table class="table table-sm table-borderless mb-0 small">
        <tr><td><?= role_badge('admin') ?></td><td><code>admin@shopease.test</code></td><td><code>Admin#123</code></td></tr>
        <tr><td><?= role_badge('operator') ?></td><td><code>operator@shopease.test</code></td><td><code>Operator#123</code></td></tr>
        <tr><td><?= role_badge('customer') ?></td><td><code>najwa@student.its.ac.id</code></td><td><code>Customer#123</code></td></tr>
    </table>
</div>
<?= $this->endSection() ?>
