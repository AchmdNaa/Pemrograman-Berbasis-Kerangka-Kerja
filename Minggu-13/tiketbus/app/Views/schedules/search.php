<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="card p-3 mb-4"><?= view('partials/search_form', ['cities' => $cities, 'q' => $q]) ?></div>
<?php foreach ($errors as $e): ?><div class="alert alert-warning"><?= esc($e) ?></div><?php endforeach ?>
<?php if (! $errors): ?>
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
        <div>
            <h1 class="h5 fw-bold mb-0"><?= esc($q['origin']) ?> <i class="bi bi-arrow-right text-tb"></i> <?= esc($q['destination']) ?></h1>
            <div class="small text-muted"><?= tgl($q['date'], false) ?> · <?= count($schedules) ?> jadwal ditemukan</div>
        </div>
        <div class="btn-group btn-group-sm">
            <?php $base = url_to('search') . '?asal=' . urlencode($q['origin']) . '&tujuan=' . urlencode($q['destination']) . '&tanggal=' . $q['date']; ?>
            <a href="<?= $base ?>" class="btn <?= $q['class'] === '' ? 'btn-primary' : 'btn-outline-primary' ?>">Semua kelas</a>
            <?php foreach ($classes as $c): ?><a href="<?= $base ?>&kelas=<?= $c ?>" class="btn <?= $q['class'] === $c ? 'btn-primary' : 'btn-outline-primary' ?>"><?= $c ?></a><?php endforeach ?>
        </div>
    </div>
    <?php if ($schedules === []): ?>
        <div class="card p-5 text-center text-muted"><i class="bi bi-calendar-x fs-1"></i><p class="mb-0 mt-2">Tidak ada jadwal untuk rute dan tanggal ini. Coba tanggal lain.</p></div>
    <?php endif ?>
    <div class="row g-3">
        <?php foreach ($schedules as $s): ?><div class="col-lg-6"><?= view('schedules/_card', ['s' => $s]) ?></div><?php endforeach ?>
    </div>
<?php endif ?>
<?= $this->endSection() ?>
