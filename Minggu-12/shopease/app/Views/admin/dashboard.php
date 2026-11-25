<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Dashboard Admin</h1>
<div class="row g-3 mb-4">
    <?php foreach ([
        ['Customer', $roles['customer'], 'bi-people', '#10b981'],
        ['Operator', $roles['operator'], 'bi-person-badge', '#0ea5e9'],
        ['Admin', $roles['admin'], 'bi-shield-lock', '#dc2626'],
        ['Produk aktif', $products, 'bi-box-seam', '#f97316'],
    ] as [$label, $num, $icon, $color]): ?>
        <div class="col-6 col-xl-3">
            <div class="card stat p-3 h-100" style="--accent: <?= $color ?>">
                <div class="d-flex justify-content-between"><span class="small text-muted"><?= $label ?></span><i class="bi <?= $icon ?>" style="color: <?= $color ?>"></i></div>
                <div class="num"><?= $num ?></div>
            </div>
        </div>
    <?php endforeach ?>
</div>
<div class="row g-4">
    <div class="col-xl-6">
        <div class="card p-3 h-100">
            <div class="d-flex justify-content-between mb-2"><h2 class="h6 fw-bold mb-0">Pengguna Terbaru</h2><a href="<?= url_to('admin.users') ?>" class="small">Kelola</a></div>
            <?php foreach ($newUsers as $u): ?>
                <div class="d-flex justify-content-between align-items-center border-bottom py-2 small">
                    <div><div class="fw-semibold"><?= esc($u['name']) ?></div><div class="text-muted"><?= esc($u['email']) ?></div></div>
                    <?= role_badge($u['role']) ?>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="card p-3 mb-4">
            <div class="d-flex justify-content-between mb-2"><h2 class="h6 fw-bold mb-0">Stok Menipis (&lt; 10)</h2><a href="<?= url_to('operator.dashboard') ?>" class="small">Atur stok</a></div>
            <?php foreach ($lowStock as $p): ?>
                <div class="d-flex justify-content-between small border-bottom py-2"><span><?= esc($p['name']) ?></span><span class="badge <?= $p['stock'] == 0 ? 'text-bg-danger' : 'text-bg-warning' ?>"><?= $p['stock'] == 0 ? 'Habis' : $p['stock'] ?></span></div>
            <?php endforeach ?>
        </div>
        <div class="card p-3">
            <h2 class="h6 fw-bold">Pesan Kontak Terbaru</h2>
            <?php if ($messages === []): ?><p class="small text-muted mb-0">Belum ada pesan.</p><?php endif ?>
            <?php foreach ($messages as $m): ?>
                <div class="small border-bottom py-2"><div class="fw-semibold"><?= esc($m['subject']) ?></div><div class="text-muted"><?= esc($m['name']) ?> · <?= esc($m['email']) ?></div></div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
