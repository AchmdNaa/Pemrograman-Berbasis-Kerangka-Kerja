<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="mx-auto" style="max-width: 480px">
    <div class="card p-4">
        <h1 class="h4 fw-bold mb-1">Daftar Akun Pelanggan</h1>
        <p class="text-muted small">Sudah punya akun? <a href="<?= url_to('login') ?>">Masuk</a></p>
        <form action="<?= url_to('register') ?>" method="post" novalidate>
            <?= csrf_field() ?>
            <?php foreach (['name' => ['Nama lengkap', 'text'], 'email' => ['Email', 'email'], 'phone' => ['Nomor HP', 'tel'], 'password' => ['Password', 'password'], 'password_confirm' => ['Ulangi password', 'password']] as $f => [$l, $t]): ?>
                <div class="mb-3"><label class="form-label" for="<?= $f ?>"><?= $l ?></label><input type="<?= $t ?>" class="form-control" id="<?= $f ?>" name="<?= $f ?>" value="<?= $t === 'password' ? '' : esc(old($f)) ?>"></div>
            <?php endforeach ?>
            <button class="btn btn-primary w-100">Daftar</button>
        </form>
    </div>
</div>
<?= $this->endSection() ?>
