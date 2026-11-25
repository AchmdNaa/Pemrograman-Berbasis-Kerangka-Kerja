<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<section class="hero p-4 p-lg-5 pb-5 mb-0">
    <div class="row align-items-center" style="position: relative; z-index: 1">
        <div class="col-lg-8">
            <span class="badge bg-light text-tb mb-3"><i class="bi bi-lightning-charge-fill"></i> Pilih kursi sendiri · E-tiket instan</span>
            <h1 class="display-6 fw-bold">Pesan tiket bus antarkota, tanpa antre di terminal.</h1>
            <p class="lead opacity-75 mb-4">Cek jadwal dan ketersediaan kursi secara real-time, bayar lewat transfer bank, lalu tunjukkan e-tiket ber-QR saat naik.</p>
        </div>
        <div class="col-lg-4 text-center d-none d-lg-block"><i class="bi bi-bus-front" style="font-size: 9rem; opacity: .3"></i></div>
    </div>
</section>
<div class="card search-card p-4 mx-lg-4 mb-5">
    <?= view('partials/search_form', ['cities' => $cities]) ?>
</div>

<div class="row g-4 mb-5">
    <?php foreach ([['bi-grid-3x3-gap', 'Pilih kursi favorit', 'Denah kursi interaktif, kursi terisi langsung terkunci.'], ['bi-bank', 'Bayar via transfer', 'BCA, Mandiri, atau BRI. Unggah bukti, admin verifikasi.'], ['bi-qr-code', 'E-tiket ber-QR', 'Cetak atau simpan sebagai PDF, tunjukkan ke petugas.']] as [$i, $t, $d]): ?>
        <div class="col-md-4"><div class="card p-4 h-100 d-flex flex-row gap-3"><i class="bi <?= $i ?> fs-2 text-tb"></i><div><div class="fw-semibold"><?= $t ?></div><div class="small text-muted"><?= $d ?></div></div></div></div>
    <?php endforeach ?>
</div>

<h2 class="h5 fw-bold mb-3">Rute Populer</h2>
<div class="row g-3 mb-5">
    <?php foreach ($routes as $r): ?>
        <div class="col-6 col-lg-4">
            <a href="<?= url_to('search') ?>?asal=<?= urlencode($r['origin']) ?>&tujuan=<?= urlencode($r['destination']) ?>&tanggal=<?= date('Y-m-d', strtotime('+1 day')) ?>" class="card p-3 text-dark h-100">
                <div class="fw-semibold"><?= esc($r['origin']) ?> <i class="bi bi-arrow-right text-tb"></i> <?= esc($r['destination']) ?></div>
                <div class="small text-muted"><?= $r['distance_km'] ?> km · ± <?= durasi((int) $r['duration_min']) ?></div>
            </a>
        </div>
    <?php endforeach ?>
</div>

<h2 class="h5 fw-bold mb-3">Keberangkatan Terdekat</h2>
<div class="row g-3">
    <?php foreach ($upcoming as $s): ?>
        <div class="col-md-6"><?= view('schedules/_card', ['s' => $s]) ?></div>
    <?php endforeach ?>
</div>
<?= $this->endSection() ?>
