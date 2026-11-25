<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<form class="card p-3 mb-4 d-flex flex-row flex-wrap align-items-end gap-2" method="get">
    <div><label class="form-label small mb-1">Dari</label><input type="date" class="form-control form-control-sm" name="dari" value="<?= esc($from) ?>"></div>
    <div><label class="form-label small mb-1">Sampai</label><input type="date" class="form-control form-control-sm" name="sampai" value="<?= esc($to) ?>"></div>
    <button class="btn btn-sm btn-primary">Tampilkan</button>
    <button type="button" class="btn btn-sm btn-outline-secondary ms-auto" onclick="window.print()"><i class="bi bi-printer"></i> Cetak</button>
</form>
<div class="row g-3 mb-4">
    <div class="col-md-6"><div class="card stat p-3"><div class="small text-muted">Total pendapatan</div><div class="num"><?= rupiah($revenue) ?></div></div></div>
    <div class="col-md-6"><div class="card stat p-3" style="--accent: #0ea5e9"><div class="small text-muted">Tiket terjual</div><div class="num"><?= $tickets ?></div></div></div>
</div>
<div class="row g-4">
    <div class="col-xl-6"><div class="card p-3">
        <h2 class="h6 fw-bold">Per Rute</h2>
        <?php $max = max(array_column($perRoute, 'revenue') ?: [1]); ?>
        <table class="table table-sm small mb-0">
            <thead><tr><th>Rute</th><th class="text-center">Pesanan</th><th class="text-center">Tiket</th><th class="text-end">Pendapatan</th></tr></thead>
            <?php foreach ($perRoute as $r): ?>
                <tr><td><?= esc($r['origin']) ?> → <?= esc($r['destination']) ?><div class="progress mt-1" style="height: 4px"><div class="progress-bar bg-success" style="width: <?= round($r['revenue'] / $max * 100) ?>%"></div></div></td><td class="text-center"><?= $r['orders'] ?></td><td class="text-center"><?= $r['tickets'] ?></td><td class="text-end"><?= rupiah($r['revenue']) ?></td></tr>
            <?php endforeach ?>
            <?php if ($perRoute === []): ?><tr><td colspan="4" class="text-muted text-center py-3">Belum ada penjualan pada periode ini.</td></tr><?php endif ?>
        </table>
    </div></div>
    <div class="col-xl-6"><div class="card p-3">
        <h2 class="h6 fw-bold">Transaksi Lunas</h2>
        <table class="table table-sm small mb-0">
            <thead><tr><th>Kode</th><th>Rute</th><th>Dibayar</th><th class="text-end">Total</th></tr></thead>
            <?php foreach ($list as $r): ?><tr><td class="font-monospace"><?= esc($r['code']) ?></td><td><?= esc($r['origin']) ?> → <?= esc($r['destination']) ?></td><td><?= tgl($r['paid_at'], true, false) ?></td><td class="text-end"><?= rupiah($r['total']) ?></td></tr><?php endforeach ?>
        </table>
    </div></div>
</div>
<?= $this->endSection() ?>
