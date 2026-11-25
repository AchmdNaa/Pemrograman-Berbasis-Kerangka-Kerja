<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['Mahasiswa', $jumlahMhs, 'bi-people', '#4e73df'],
        ['Mata Kuliah', $jumlahMk, 'bi-journal-bookmark', '#1cc88a'],
        ['Rata-rata IPK', number_format($rataIpk, 2), 'bi-graph-up', '#36b9cc'],
        ['Produk (stok habis)', $jumlahProduk . ' (' . $stokHabis . ')', 'bi-box-seam', '#f6c23e'],
        ['Pesanan Baru', $pesananBaru, 'bi-bell', '#e74a3b'],
        ['Omzet', rupiah($omzet), 'bi-cash-coin', '#858796'],
    ];
    foreach ($cards as [$label, $value, $icon, $color]): ?>
        <div class="col-sm-6 col-lg-4">
            <div class="card stat-card h-100 p-3" style="--accent: <?= $color ?>">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="label"><?= $label ?></div>
                        <div class="value"><?= $value ?></div>
                    </div>
                    <i class="bi <?= $icon ?> fs-2 text-secondary opacity-25"></i>
                </div>
            </div>
        </div>
    <?php endforeach ?>
</div>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold mb-3">Mahasiswa per Program Studi</h2>
            <?php $max = max(array_column($perProdi, 'jumlah') ?: [1]); ?>
            <?php foreach ($perProdi as $row): ?>
                <div class="mb-3">
                    <div class="d-flex justify-content-between small"><span><?= esc($row['prodi']) ?></span><strong><?= $row['jumlah'] ?></strong></div>
                    <div class="progress" style="height: 8px"><div class="progress-bar" style="width: <?= round($row['jumlah'] / $max * 100) ?>%"></div></div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold mb-3">Mahasiswa Terbaru</h2>
            <?php foreach ($mhsTerbaru as $m): ?>
                <a href="<?= site_url('admin/mahasiswa/' . $m['id']) ?>" class="d-flex align-items-center gap-2 mb-2 text-dark">
                    <img src="<?= foto_mhs($m) ?>" class="avatar" alt="">
                    <div class="small"><div class="fw-semibold"><?= esc($m['nama']) ?></div><div class="text-muted"><?= esc($m['nim']) ?> · <?= esc($m['prodi']) ?></div></div>
                </a>
            <?php endforeach ?>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-3 h-100">
            <h2 class="h6 fw-bold mb-3">Pesanan Terakhir</h2>
            <?php if ($pesananAkhir === []): ?>
                <p class="text-muted small">Belum ada pesanan. Coba belanja di <a href="<?= site_url('toko') ?>">halaman toko</a>.</p>
            <?php endif ?>
            <?php foreach ($pesananAkhir as $o): ?>
                <a href="<?= site_url('admin/pesanan/' . $o['id']) ?>" class="d-flex justify-content-between small mb-2 text-dark border-bottom pb-2">
                    <span><span class="fw-semibold"><?= esc($o['kode']) ?></span><br><span class="text-muted"><?= esc($o['nama_pembeli']) ?></span></span>
                    <span class="text-end"><?= rupiah($o['total']) ?><br><?= view('admin/pesanan/_badge', ['status' => $o['status']]) ?></span>
                </a>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
