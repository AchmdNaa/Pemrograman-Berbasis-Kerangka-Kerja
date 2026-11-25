<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Profil</h1>
<div class="row g-4">
    <div class="col-lg-6">
        <div class="card p-4">
            <h2 class="h6 fw-bold">Data Diri</h2>
            <form action="<?= url_to('profile') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-2"><label class="form-label small">Email</label><input class="form-control" value="<?= esc($user['email']) ?>" disabled></div>
                <div class="mb-2"><label class="form-label small" for="name">Nama</label><input class="form-control" id="name" name="name" value="<?= esc(old('name', $user['name'])) ?>"></div>
                <div class="mb-3"><label class="form-label small" for="phone">Nomor HP</label><input class="form-control" id="phone" name="phone" value="<?= esc(old('phone', $user['phone'])) ?>"></div>
                <button class="btn btn-primary">Simpan</button>
            </form>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card p-4">
            <h2 class="h6 fw-bold">Ganti Password</h2>
            <form action="<?= url_to('profile.password') ?>" method="post">
                <?= csrf_field() ?>
                <?php foreach (['current_password' => 'Password lama', 'password' => 'Password baru', 'password_confirm' => 'Ulangi password baru'] as $f => $l): ?>
                    <div class="mb-2"><label class="form-label small" for="<?= $f ?>"><?= $l ?></label><input type="password" class="form-control" id="<?= $f ?>" name="<?= $f ?>"></div>
                <?php endforeach ?>
                <button class="btn btn-dark mt-2">Ganti Password</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
