<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <?php foreach ([['Pendapatan bulan ini', rupiah($revenue), '#0f766e', 'bi-cash-coin'], ['Tiket terjual bulan ini', $sold, '#0ea5e9', 'bi-ticket-perforated'], ['Menunggu verifikasi', $waiting, '#f59e0b', 'bi-hourglass-split'], ['Pelanggan', $customers, '#8b5cf6', 'bi-people']] as [$l, $n, $c, $i]): ?>
        <div class="col-sm-6 col-xl-3"><div class="card stat p-3 h-100" style="--accent: <?= $c ?>"><div class="d-flex justify-content-between small text-muted"><?= $l ?><i class="bi <?= $i ?>" style="color: <?= $c ?>"></i></div><div class="num"><?= $n ?></div></div></div>
    <?php endforeach ?>
</div>
<div class="row g-4">
    <div class="col-xl-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between mb-2"><h2 class="h6 fw-bold mb-0">Pembayaran Perlu Diverifikasi</h2><a href="<?= url_to('admin.bookings') ?>?status=menunggu_verifikasi" class="small">Semua</a></div>
            <?php if ($pending === []): ?><p class="small text-muted mb-0">Tidak ada pembayaran yang menunggu.</p><?php endif ?>
            <?php foreach ($pending as $b): ?>
                <a href="<?= url_to('admin.bookings.show', $b['id']) ?>" class="d-flex justify-content-between border-bottom py-2 small text-dark">
                    <span><span class="font-monospace fw-semibold"><?= esc($b['code']) ?></span> · <?= esc($b['customer']) ?><div class="text-muted"><?= esc($b['origin']) ?> → <?= esc($b['destination']) ?>, <?= tgl($b['depart_at']) ?></div></span>
                    <span class="fw-bold"><?= rupiah($b['total']) ?></span>
                </a>
            <?php endforeach ?>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold">Keberangkatan Hari Ini</h2>
            <table class="table table-sm small mb-0">
                <thead><tr><th>Jam</th><th>Rute</th><th>Bus</th><th class="text-end">Terisi</th></tr></thead>
                <?php foreach ($today as $s): ?>
                    <tr><td class="fw-semibold"><?= date('H:i', strtotime($s['depart_at'])) ?></td><td><?= esc($s['origin']) ?> → <?= esc($s['destination']) ?></td><td><?= esc($s['operator']) ?> <span class="text-muted"><?= esc($s['class']) ?></span></td><td class="text-end"><?= $s['booked'] ?>/<?= $s['capacity'] ?></td></tr>
                <?php endforeach ?>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
