<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Pesanan Saya</h1>
<?php if ($bookings === []): ?>
    <div class="card p-5 text-center text-muted"><i class="bi bi-ticket-perforated fs-1"></i><p class="mt-2">Belum ada pesanan.</p><a href="<?= url_to('home') ?>" class="btn btn-primary align-self-center">Cari Tiket</a></div>
<?php endif ?>
<div class="row g-3">
    <?php foreach ($bookings as $b): ?>
        <div class="col-lg-6">
            <a href="<?= url_to('booking.show', $b['code']) ?>" class="card p-3 text-dark h-100">
                <div class="d-flex justify-content-between mb-2"><span class="font-monospace fw-bold"><?= esc($b['code']) ?></span><?= status_badge($b['status']) ?></div>
                <div class="fw-semibold"><?= esc($b['origin']) ?> <i class="bi bi-arrow-right text-tb"></i> <?= esc($b['destination']) ?></div>
                <div class="small text-muted"><?= tgl($b['depart_at']) ?> · <?= esc($b['operator']) ?> <?= esc($b['class']) ?></div>
                <div class="d-flex justify-content-between mt-2 small">
                    <span class="text-muted">Dipesan <?= tgl($b['created_at'], true, false) ?></span><span class="fw-bold"><?= rupiah($b['total']) ?></span>
                </div>
            </a>
        </div>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>
