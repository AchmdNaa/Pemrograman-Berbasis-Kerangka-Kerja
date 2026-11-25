<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<div class="mx-auto" style="max-width: 720px">
    <h1 class="h4 fw-bold">Cek Status Tiket</h1>
    <p class="text-muted">Masukkan kode pemesanan (contoh: TB-DEMO01) untuk melihat status tiket.</p>
    <form class="d-flex gap-2 mb-4" method="get">
        <input class="form-control form-control-lg text-uppercase" name="kode" value="<?= esc($code) ?>" placeholder="TB-XXXXXX" required>
        <button class="btn btn-primary btn-lg"><i class="bi bi-search"></i> Cek</button>
    </form>
    <?php if ($code !== '' && ! $b): ?>
        <div class="alert alert-warning">Kode pemesanan <strong><?= esc($code) ?></strong> tidak ditemukan.</div>
    <?php elseif ($b): ?>
        <div class="card p-4">
            <div class="d-flex justify-content-between mb-3"><span class="fs-5 fw-bold font-monospace"><?= esc($b['code']) ?></span><?= status_badge($b['status']) ?></div>
            <div class="fw-semibold"><?= esc($b['origin']) ?> → <?= esc($b['destination']) ?></div>
            <div class="small text-muted mb-3"><?= tgl($b['depart_at']) ?> · <?= esc($b['operator']) ?> (<?= esc($b['bus_code']) ?>)</div>
            <table class="table table-sm small mb-0">
                <thead><tr><th>Kursi</th><th>Penumpang</th></tr></thead>
                <?php foreach ($passengers as $p): ?>
                    <!-- Nama disamarkan agar data pribadi tidak terbuka untuk publik -->
                    <tr><td><?= esc($p['seat_no']) ?></td><td><?= esc(mb_substr($p['name'], 0, 2) . str_repeat('*', max(3, mb_strlen($p['name']) - 2))) ?></td></tr>
                <?php endforeach ?>
            </table>
        </div>
    <?php endif ?>
</div>
<?= $this->endSection() ?>
