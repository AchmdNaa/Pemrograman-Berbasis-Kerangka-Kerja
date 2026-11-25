<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php $err = session('errors') ?? []; ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <div><a href="<?= url_to('my.bookings') ?>" class="small"><i class="bi bi-arrow-left"></i> Pesanan Saya</a>
        <h1 class="h4 fw-bold mb-0 mt-1">Pesanan <span class="font-monospace"><?= esc($b['code']) ?></span></h1></div>
    <div class="d-flex gap-2 align-items-center">
        <?= status_badge($b['status']) ?>
        <?php if ($b['status'] === 'lunas'): ?><a href="<?= url_to('booking.ticket', $b['code']) ?>" class="btn btn-primary" target="_blank"><i class="bi bi-qr-code"></i> E-Tiket</a><?php endif ?>
        <?php if (in_array($b['status'], ['menunggu_pembayaran', 'menunggu_verifikasi'], true)): ?>
            <form action="<?= url_to('booking.cancel', $b['code']) ?>" method="post" onsubmit="return confirm('Batalkan pesanan ini? Kursi akan dilepas.')"><?= csrf_field() ?><button class="btn btn-outline-danger btn-sm">Batalkan</button></form>
        <?php endif ?>
    </div>
</div>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-4 mb-4">
            <div class="fw-semibold fs-5"><?= esc($b['origin']) ?> <i class="bi bi-arrow-right text-tb"></i> <?= esc($b['destination']) ?></div>
            <div class="text-muted small mb-3"><?= tgl($b['depart_at']) ?> → <?= date('H:i', strtotime($b['arrive_at'])) ?> · <?= esc($b['operator']) ?> <?= esc($b['class']) ?> (<?= esc($b['bus_code']) ?>)</div>
            <table class="table small mb-0">
                <thead class="table-light"><tr><th>Kursi</th><th>Nama</th><th>NIK</th><th>No. HP</th></tr></thead>
                <?php foreach ($passengers as $p): ?>
                    <tr><td><span class="badge bg-primary"><?= esc($p['seat_no']) ?></span></td><td><?= esc($p['name']) ?></td><td><?= esc(substr($p['id_number'], 0, 6) . '******' . substr($p['id_number'], -4)) ?></td><td><?= esc($p['phone'] ?? '-') ?></td></tr>
                <?php endforeach ?>
            </table>
            <div class="d-flex justify-content-between mt-3"><span class="text-muted"><?= count($passengers) ?> × <?= rupiah($b['price']) ?></span><span class="fs-5 fw-bold">Total <?= rupiah($b['total']) ?></span></div>
        </div>
        <?php if ($payments): ?>
            <div class="card p-4">
                <h2 class="h6 fw-bold">Riwayat Konfirmasi Pembayaran</h2>
                <?php foreach ($payments as $p): ?>
                    <div class="d-flex justify-content-between border-bottom py-2 small">
                        <div><?= esc($p['sender_name']) ?> · <?= esc($p['sender_bank']) ?> <?= esc($p['sender_account']) ?><div class="text-muted"><?= tgl($p['created_at']) ?><?= $p['note'] ? ' · Catatan: ' . esc($p['note']) : '' ?></div></div>
                        <div class="text-end"><?= rupiah($p['amount']) ?><br><?= status_badge($p['status']) ?> <a href="<?= url_to('payment.proof', $p['id']) ?>" target="_blank" class="ms-1">bukti</a></div>
                    </div>
                <?php endforeach ?>
            </div>
        <?php endif ?>
    </div>
    <div class="col-lg-5">
        <?php if ($b['status'] === 'menunggu_pembayaran'): ?>
            <div class="card p-4 border border-warning">
                <div class="alert alert-warning small"><i class="bi bi-hourglass-split"></i> Bayar sebelum <strong><?= tgl($b['expires_at']) ?> WIB</strong> atau kursi akan dilepas otomatis.</div>
                <h2 class="h6 fw-bold">Transfer ke rekening</h2>
                <div class="bg-tb-soft rounded-3 p-3 mb-3">
                    <div class="small text-muted"><?= esc($b['bank']) ?> a.n. <?= esc($b['account_name']) ?></div>
                    <div class="fs-4 fw-bold font-monospace"><?= esc($b['account_no']) ?></div>
                    <div class="small">Jumlah: <strong class="text-tb"><?= rupiah($b['total']) ?></strong></div>
                </div>
                <h2 class="h6 fw-bold">Konfirmasi Pembayaran</h2>
                <form action="<?= url_to('booking.pay', $b['code']) ?>" method="post" enctype="multipart/form-data" novalidate>
                    <?= csrf_field() ?>
                    <?php foreach (['sender_name' => 'Nama pemilik rekening pengirim', 'sender_bank' => 'Bank pengirim', 'sender_account' => 'Nomor rekening pengirim', 'amount' => 'Jumlah yang ditransfer (Rp)'] as $f => $l): ?>
                        <div class="mb-2">
                            <label class="form-label small mb-1" for="<?= $f ?>"><?= $l ?></label>
                            <input class="form-control form-control-sm <?= isset($err[$f]) ? 'is-invalid' : '' ?>" id="<?= $f ?>" name="<?= $f ?>" value="<?= esc(old($f, $f === 'amount' ? $b['total'] : ($f === 'sender_name' ? auth_user('name') : ''))) ?>">
                            <div class="invalid-feedback"><?= esc($err[$f] ?? '') ?></div>
                        </div>
                    <?php endforeach ?>
                    <div class="mb-3">
                        <label class="form-label small mb-1" for="proof">Bukti transfer (JPG/PNG/PDF, maks 2 MB)</label>
                        <input type="file" class="form-control form-control-sm <?= isset($err['proof']) ? 'is-invalid' : '' ?>" id="proof" name="proof" accept=".jpg,.jpeg,.png,.pdf">
                        <div class="invalid-feedback"><?= esc($err['proof'] ?? '') ?></div>
                    </div>
                    <button class="btn btn-primary w-100"><i class="bi bi-upload"></i> Kirim Bukti Pembayaran</button>
                </form>
            </div>
        <?php elseif ($b['status'] === 'menunggu_verifikasi'): ?>
            <div class="card p-4 text-center"><i class="bi bi-hourglass-split fs-1 text-info"></i><div class="fw-semibold mt-2">Bukti pembayaran sedang diverifikasi</div><div class="small text-muted">E-tiket akan tersedia setelah admin memverifikasi pembayaran Anda.</div></div>
        <?php elseif ($b['status'] === 'lunas'): ?>
            <div class="card p-4 text-center"><i class="bi bi-check-circle-fill fs-1 text-success"></i><div class="fw-semibold mt-2">Pembayaran terverifikasi</div><div class="small text-muted mb-3">Lunas pada <?= tgl($b['paid_at']) ?></div><a href="<?= url_to('booking.ticket', $b['code']) ?>" target="_blank" class="btn btn-primary"><i class="bi bi-qr-code"></i> Lihat &amp; Cetak E-Tiket</a></div>
        <?php else: ?>
            <div class="card p-4 text-center text-muted"><i class="bi bi-x-circle fs-1"></i><div class="mt-2">Pesanan ini <?= $b['status'] === 'kedaluwarsa' ? 'kedaluwarsa karena tidak dibayar tepat waktu' : 'telah dibatalkan' ?>. Kursi sudah dilepas.</div><a href="<?= url_to('schedule', $b['schedule_id']) ?>" class="btn btn-outline-primary btn-sm mt-3">Pesan Ulang</a></div>
        <?php endif ?>
    </div>
</div>
<?= $this->endSection() ?>
