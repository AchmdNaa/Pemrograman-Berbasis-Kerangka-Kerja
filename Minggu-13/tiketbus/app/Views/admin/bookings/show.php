<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<a href="<?= url_to('admin.bookings') ?>" class="small"><i class="bi bi-arrow-left"></i> Daftar pemesanan</a>
<div class="row g-4 mt-1">
    <div class="col-xl-7">
        <div class="card p-4 mb-4">
            <div class="d-flex justify-content-between"><span class="fs-5 fw-bold font-monospace"><?= esc($b['code']) ?></span><?= status_badge($b['status']) ?></div>
            <div class="fw-semibold mt-2"><?= esc($b['origin']) ?> → <?= esc($b['destination']) ?></div>
            <div class="small text-muted mb-3"><?= tgl($b['depart_at']) ?> · <?= esc($b['operator']) ?> <?= esc($b['class']) ?> (<?= esc($b['bus_code']) ?>)</div>
            <div class="row small mb-3">
                <div class="col-md-6"><div class="text-muted">Pelanggan</div><?= esc($b['customer']) ?> · <?= esc($b['customer_email']) ?> · <?= esc($b['customer_phone']) ?></div>
                <div class="col-md-6"><div class="text-muted">Dipesan / batas bayar</div><?= tgl($b['created_at']) ?> / <?= tgl($b['expires_at']) ?></div>
            </div>
            <table class="table table-sm small mb-2">
                <thead class="table-light"><tr><th>Kursi</th><th>Nama</th><th>NIK</th><th>No. HP</th></tr></thead>
                <?php foreach ($passengers as $p): ?><tr><td><span class="badge bg-primary"><?= esc($p['seat_no']) ?></span></td><td><?= esc($p['name']) ?></td><td class="font-monospace"><?= esc($p['id_number']) ?></td><td><?= esc($p['phone'] ?? '-') ?></td></tr><?php endforeach ?>
            </table>
            <div class="text-end fw-bold">Total <?= rupiah($b['total']) ?> → <?= esc($b['bank'] ?? '-') ?> <?= esc($b['account_no'] ?? '') ?></div>
            <?php if ($b['status'] === 'lunas'): ?><a href="<?= url_to('booking.ticket', $b['code']) ?>" target="_blank" class="btn btn-outline-primary btn-sm mt-2 align-self-start"><i class="bi bi-qr-code"></i> Lihat e-tiket</a><?php endif ?>
        </div>
    </div>
    <div class="col-xl-5">
        <div class="card p-4">
            <h2 class="h6 fw-bold">Konfirmasi Pembayaran</h2>
            <?php if ($payments === []): ?><p class="small text-muted mb-0">Pelanggan belum mengirim bukti pembayaran.</p><?php endif ?>
            <?php foreach ($payments as $p): ?>
                <div class="border rounded-3 p-3 mb-3 small">
                    <div class="d-flex justify-content-between"><span class="fw-semibold"><?= esc($p['sender_name']) ?></span><?= status_badge($p['status']) ?></div>
                    <div class="text-muted"><?= esc($p['sender_bank']) ?> · <?= esc($p['sender_account']) ?> · <?= tgl($p['created_at']) ?></div>
                    <div class="my-1">Jumlah: <strong class="<?= $p['amount'] >= $b['total'] ? 'text-success' : 'text-danger' ?>"><?= rupiah($p['amount']) ?></strong> (tagihan <?= rupiah($b['total']) ?>)</div>
                    <?php $isPdf = str_ends_with(strtolower($p['proof']), '.pdf'); ?>
                    <a href="<?= url_to('payment.proof', $p['id']) ?>" target="_blank">
                        <?php if ($isPdf): ?><i class="bi bi-file-earmark-pdf"></i> Buka bukti (PDF)<?php else: ?><img src="<?= url_to('payment.proof', $p['id']) ?>" class="img-fluid rounded border mt-1" style="max-height: 220px" alt="Bukti transfer"><?php endif ?>
                    </a>
                    <?php if ($p['note']): ?><div class="mt-1">Catatan: <?= esc($p['note']) ?></div><?php endif ?>
                    <?php if ($p['status'] === 'menunggu' && $b['status'] === 'menunggu_verifikasi'): ?>
                        <form action="<?= url_to('admin.payments.verify', $p['id']) ?>" method="post" class="mt-2">
                            <?= csrf_field() ?>
                            <input class="form-control form-control-sm mb-2" name="note" placeholder="Catatan (wajib bila ditolak)">
                            <div class="d-flex gap-2">
                                <button name="action" value="terima" class="btn btn-sm btn-success flex-fill"><i class="bi bi-check-lg"></i> Terima</button>
                                <button name="action" value="tolak" class="btn btn-sm btn-outline-danger flex-fill"><i class="bi bi-x-lg"></i> Tolak</button>
                            </div>
                        </form>
                    <?php endif ?>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
