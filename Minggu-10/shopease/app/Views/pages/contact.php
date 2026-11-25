<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-5">
        <h1 class="h3 section-title">Hubungi Kami</h1>
        <p class="text-secondary">Ada pertanyaan tentang produk, pesanan, atau kerja sama? Kirim pesan dan tim kami akan membalas dalam 1×24 jam.</p>
        <div class="card p-3 mb-3 d-flex flex-row gap-3"><span class="feature-icon"><i class="bi bi-geo-alt"></i></span><div><div class="fw-semibold">Alamat</div><div class="small text-muted">Jl. Teknik Kimia, Keputih, Sukolilo, Surabaya 60111</div></div></div>
        <div class="card p-3 mb-3 d-flex flex-row gap-3"><span class="feature-icon"><i class="bi bi-envelope"></i></span><div><div class="fw-semibold">Email</div><div class="small text-muted">halo@shopease.test</div></div></div>
        <div class="card p-3 d-flex flex-row gap-3"><span class="feature-icon"><i class="bi bi-clock"></i></span><div><div class="fw-semibold">Jam Layanan</div><div class="small text-muted">Senin–Sabtu, 08.00–20.00 WIB</div></div></div>
    </div>
    <div class="col-lg-7">
        <div class="card p-4">
            <form action="<?= url_to('contact') ?>" method="post" novalidate>
                <?= csrf_field() ?>
                <?php $err = session('errors') ?? []; ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="name">Nama</label>
                        <input class="form-control <?= isset($err['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= esc(old('name')) ?>">
                        <div class="invalid-feedback"><?= esc($err['name'] ?? '') ?></div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label" for="email">Email</label>
                        <input type="email" class="form-control <?= isset($err['email']) ? 'is-invalid' : '' ?>" id="email" name="email" value="<?= esc(old('email')) ?>">
                        <div class="invalid-feedback"><?= esc($err['email'] ?? '') ?></div>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="subject">Subjek</label>
                    <input class="form-control <?= isset($err['subject']) ? 'is-invalid' : '' ?>" id="subject" name="subject" value="<?= esc(old('subject')) ?>">
                    <div class="invalid-feedback"><?= esc($err['subject'] ?? '') ?></div>
                </div>
                <div class="mb-3">
                    <label class="form-label" for="message">Pesan</label>
                    <textarea class="form-control <?= isset($err['message']) ? 'is-invalid' : '' ?>" id="message" name="message" rows="5"><?= esc(old('message')) ?></textarea>
                    <div class="invalid-feedback"><?= esc($err['message'] ?? '') ?></div>
                </div>
                <button class="btn btn-primary px-4"><i class="bi bi-send"></i> Kirim Pesan</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
