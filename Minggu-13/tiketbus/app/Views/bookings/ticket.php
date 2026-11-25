<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
    <title>E-Tiket <?= esc($b['code']) ?></title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/tiket.css') ?>">
    <style>
        body { background: #e2e8f0; }
        .ticket { max-width: 760px; margin: 2rem auto; background: #fff; border-radius: 1rem; overflow: hidden; box-shadow: 0 1rem 2rem rgba(0,0,0,.08); page-break-inside: avoid; }
        .ticket-head { background: linear-gradient(120deg, #134e4a, #0f766e); color: #fff; }
        .stub { border-left: 2px dashed #cbd5e1; }
        .qr svg, .qr img { width: 130px; height: 130px; }
        @media print {
            body { background: #fff; }
            .no-print { display: none !important; }
            .ticket { box-shadow: none; margin: 0 auto 1rem; border: 1px solid #cbd5e1; }
            .ticket-head { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
        }
    </style>
</head>
<body>
<div class="text-center mt-3 no-print">
    <button onclick="window.print()" class="btn btn-primary"><i class="bi bi-printer"></i> Cetak / Simpan sebagai PDF</button>
    <a href="<?= url_to('booking.show', $b['code']) ?>" class="btn btn-light border">Kembali</a>
    <div class="small text-muted mt-1">Pilih "Save as PDF" / "Simpan sebagai PDF" pada dialog cetak untuk mengunduh e-tiket.</div>
</div>
<?php foreach ($passengers as $p): ?>
    <div class="ticket">
        <div class="ticket-head d-flex justify-content-between align-items-center px-4 py-3">
            <span class="fs-5 fw-bold"><i class="bi bi-bus-front-fill"></i> TiketBus · E-Tiket</span>
            <span class="font-monospace"><?= esc($b['code']) ?></span>
        </div>
        <div class="d-flex">
            <div class="flex-grow-1 p-4">
                <div class="d-flex align-items-center gap-3 mb-3">
                    <div><div class="small text-muted">Berangkat</div><div class="fs-3 fw-bold"><?= date('H:i', strtotime($b['depart_at'])) ?></div><div class="fw-semibold"><?= esc($b['origin']) ?></div></div>
                    <div class="route-line"></div><i class="bi bi-bus-front text-tb fs-4"></i><div class="route-line"></div>
                    <div class="text-end"><div class="small text-muted">Tiba (perkiraan)</div><div class="fs-3 fw-bold"><?= date('H:i', strtotime($b['arrive_at'])) ?></div><div class="fw-semibold"><?= esc($b['destination']) ?></div></div>
                </div>
                <div class="row small g-2">
                    <div class="col-6"><div class="text-muted">Penumpang</div><div class="fw-semibold"><?= esc($p['name']) ?></div></div>
                    <div class="col-3"><div class="text-muted">Kursi</div><div class="fw-bold fs-5 text-tb"><?= esc($p['seat_no']) ?></div></div>
                    <div class="col-3"><div class="text-muted">Kelas</div><div class="fw-semibold"><?= esc($b['class']) ?></div></div>
                    <div class="col-6"><div class="text-muted">Tanggal</div><div class="fw-semibold"><?= tgl($b['depart_at'], false) ?></div></div>
                    <div class="col-6"><div class="text-muted">Bus</div><div class="fw-semibold"><?= esc($b['operator']) ?> · <?= esc($b['bus_code']) ?></div></div>
                    <div class="col-6"><div class="text-muted">NIK</div><div class="fw-semibold"><?= esc(substr($p['id_number'], 0, 6) . '******' . substr($p['id_number'], -4)) ?></div></div>
                    <div class="col-6"><div class="text-muted">Status</div><div class="fw-semibold text-success">LUNAS · <?= tgl($b['paid_at'], true, false) ?></div></div>
                </div>
            </div>
            <div class="stub p-4 text-center d-flex flex-column justify-content-center">
                <div class="qr" data-qr="<?= esc($b['code'] . '|' . $p['seat_no'] . '|' . $p['name'], 'attr') ?>"></div>
                <div class="small font-monospace mt-2"><?= esc($b['code']) ?>-<?= esc($p['seat_no']) ?></div>
                <div class="small text-muted">Tunjukkan ke petugas</div>
            </div>
        </div>
        <div class="px-4 py-2 small text-muted border-top">Hadir di terminal 30 menit sebelum keberangkatan dan bawa KTP sesuai NIK di atas.</div>
    </div>
<?php endforeach ?>
<script src="<?= base_url('assets/vendor/qrcode/qrcode.js') ?>"></script>
<script>
    // QR code dibuat di browser dari kode booking + kursi + nama penumpang
    document.querySelectorAll('[data-qr]').forEach(el => {
        const qr = qrcode(0, 'M');
        qr.addData(el.dataset.qr);
        qr.make();
        el.innerHTML = qr.createSvgTag({ cellSize: 4, margin: 0 });
    });
</script>
</body>
</html>
