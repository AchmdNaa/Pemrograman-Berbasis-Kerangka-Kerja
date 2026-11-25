<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card p-3">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <ul class="nav nav-pills small">
            <li class="nav-item"><a class="nav-link <?= $status === '' ? 'active' : '' ?>" href="<?= url_to('admin.bookings') ?>">Semua</a></li>
            <?php foreach (['menunggu_pembayaran' => 'Menunggu bayar', 'menunggu_verifikasi' => 'Verifikasi', 'lunas' => 'Lunas', 'dibatalkan' => 'Batal', 'kedaluwarsa' => 'Kedaluwarsa'] as $k => $l): ?>
                <li class="nav-item"><a class="nav-link <?= $status === $k ? 'active' : '' ?>" href="?status=<?= $k ?>"><?= $l ?></a></li>
            <?php endforeach ?>
        </ul>
        <form class="d-flex gap-2" method="get"><?php if ($status): ?><input type="hidden" name="status" value="<?= esc($status) ?>"><?php endif ?>
            <input type="search" class="form-control form-control-sm" name="q" value="<?= esc($q) ?>" placeholder="Kode / nama pelanggan"><button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button></form>
    </div>
    <table class="table align-middle small">
        <thead class="table-light"><tr><th>Kode</th><th>Pelanggan</th><th>Perjalanan</th><th class="text-end">Total</th><th>Status</th><th></th></tr></thead>
        <?php if ($bookings === []): ?><tr><td colspan="6" class="text-center text-muted py-4">Tidak ada data.</td></tr><?php endif ?>
        <?php foreach ($bookings as $b): ?>
            <tr><td class="font-monospace fw-semibold"><?= esc($b['code']) ?></td><td><?= esc($b['customer']) ?><div class="text-muted"><?= esc($b['customer_phone']) ?></div></td>
                <td><?= esc($b['origin']) ?> → <?= esc($b['destination']) ?><div class="text-muted"><?= tgl($b['depart_at']) ?></div></td><td class="text-end"><?= rupiah($b['total']) ?></td><td><?= status_badge($b['status']) ?></td>
                <td class="text-end"><a href="<?= url_to('admin.bookings.show', $b['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td></tr>
        <?php endforeach ?>
    </table>
    <?= $pager->links('default', 'bootstrap') ?>
</div>
<?= $this->endSection() ?>
