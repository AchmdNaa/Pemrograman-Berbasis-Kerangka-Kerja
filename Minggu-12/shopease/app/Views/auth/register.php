<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>
<h1 class="h3 fw-bold mb-1">Buat Akun Baru</h1>
<p class="text-muted mb-4">Sudah punya akun? <a href="<?= url_to('login') ?>" class="fw-semibold">Masuk di sini</a></p>

<form action="<?= url_to('register') ?>" method="post" novalidate>
    <?= csrf_field() ?>
    <?php $err = session('errors') ?? []; ?>
    <?php $field = static function (string $name, string $label, string $type = 'text', string $extra = '') use ($err) { ?>
        <div class="mb-3">
            <label class="form-label" for="<?= $name ?>"><?= $label ?></label>
            <input type="<?= $type ?>" class="form-control <?= isset($err[$name]) ? 'is-invalid' : '' ?>" id="<?= $name ?>" name="<?= $name ?>"
                   value="<?= $type === 'password' ? '' : esc(old($name)) ?>" <?= $extra ?>>
            <div class="invalid-feedback"><?= esc($err[$name] ?? '') ?></div>
        </div>
    <?php }; ?>

    <?php $field('name', 'Nama lengkap', 'text', 'autocomplete="name"') ?>
    <?php $field('email', 'Email', 'email', 'autocomplete="email"') ?>
    <?php $field('phone', 'Nomor HP <span class="text-muted small">(opsional)</span>', 'tel', 'placeholder="08xxxxxxxxxx"') ?>
    <div class="row">
        <div class="col-md-6"><?php $field('password', 'Password', 'password', 'autocomplete="new-password"') ?></div>
        <div class="col-md-6"><?php $field('password_confirm', 'Ulangi password', 'password', 'autocomplete="new-password"') ?></div>
    </div>
    <div class="form-text mb-3 mt-n2">Minimal 8 karakter, berisi huruf dan angka.</div>

    <div class="form-check mb-3">
        <input class="form-check-input <?= isset($err['terms']) ? 'is-invalid' : '' ?>" type="checkbox" value="1" id="terms" name="terms" <?= old('terms') ? 'checked' : '' ?>>
        <label class="form-check-label small" for="terms">Saya menyetujui syarat &amp; ketentuan serta kebijakan privasi ShopEase.</label>
        <div class="invalid-feedback"><?= esc($err['terms'] ?? '') ?></div>
    </div>
    <button class="btn btn-primary btn-lg w-100"><i class="bi bi-person-plus"></i> Daftar</button>
</form>
<?= $this->endSection() ?>
