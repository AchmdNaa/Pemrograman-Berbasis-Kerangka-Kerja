<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="mx-auto" style="max-width: 420px">
    <div class="card p-4">
        <h1 class="h4 fw-bold mb-1">Masuk</h1>
        <p class="text-muted small">Belum punya akun? <a href="<?= url_to('register') ?>">Daftar</a></p>
        <form action="<?= url_to('login') ?>" method="post">
            <?= csrf_field() ?>
            <div class="mb-3"><label class="form-label" for="email">Email</label><input type="email" class="form-control" id="email" name="email" value="<?= esc(old('email')) ?>" required></div>
            <div class="mb-3"><label class="form-label" for="password">Password</label><input type="password" class="form-control" id="password" name="password" required></div>
            <button class="btn btn-primary w-100">Masuk</button>
        </form>
        <div class="small text-muted mt-3 border-top pt-3">
            Demo pelanggan: <code>najwa@student.its.ac.id</code> / <code>Pelanggan#123</code><br>
            Demo admin: <code>admin@tiketbus.test</code> / <code>Admin#123</code>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
