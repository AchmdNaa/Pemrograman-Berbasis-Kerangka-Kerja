<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card p-3">
    <table class="table align-middle small mb-0">
        <thead class="table-light"><tr><th>Nama</th><th>Email</th><th>No. HP</th><th>Terdaftar</th><th class="text-center">Pesanan</th><th class="text-end">Total belanja (lunas)</th></tr></thead>
        <?php foreach ($rows as $r): ?>
            <tr><td class="fw-semibold"><?= esc($r['name']) ?></td><td><?= esc($r['email']) ?></td><td><?= esc($r['phone']) ?></td><td><?= tgl($r['created_at'], false, false) ?></td><td class="text-center"><?= $r['orders'] ?></td><td class="text-end"><?= rupiah((int) $r['spent']) ?></td></tr>
        <?php endforeach ?>
    </table>
</div>
<?= $this->endSection() ?>
