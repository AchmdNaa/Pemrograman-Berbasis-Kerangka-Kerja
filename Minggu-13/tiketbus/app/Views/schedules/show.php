<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<?php
$old = old('seats') ?? [];
$oldP = old('passengers') ?? [];
$left = (int) $s['capacity'] - count($booked);
$canBook = ! $closed && session('user_role') !== 'admin';
?>
<a href="javascript:history.back()" class="small"><i class="bi bi-arrow-left"></i> Kembali ke hasil pencarian</a>
<div class="card p-4 my-3">
    <div class="row g-3 align-items-center">
        <div class="col-md-5">
            <div class="small text-muted"><?= esc($s['operator']) ?> · <?= esc($s['bus_code']) ?> · <span class="badge bg-tb-soft text-tb border"><?= esc($s['class']) ?></span></div>
            <div class="d-flex align-items-center gap-2 mt-2">
                <div><div class="time"><?= date('H:i', strtotime($s['depart_at'])) ?></div><div class="small"><?= esc($s['origin']) ?></div></div>
                <div class="route-line"></div><i class="bi bi-bus-front text-tb"></i><div class="route-line"></div>
                <div class="text-end"><div class="time"><?= date('H:i', strtotime($s['arrive_at'])) ?></div><div class="small"><?= esc($s['destination']) ?></div></div>
            </div>
        </div>
        <div class="col-md-4 small text-muted">
            <div><i class="bi bi-calendar3"></i> <?= tgl($s['depart_at'], false) ?></div>
            <div><i class="bi bi-clock"></i> Perjalanan ± <?= durasi((int) $s['duration_min']) ?> · <?= $s['distance_km'] ?> km</div>
            <div><i class="bi bi-stars"></i> <?= esc($s['facilities']) ?></div>
        </div>
        <div class="col-md-3 text-md-end">
            <div class="fs-3 fw-bold text-tb"><?= rupiah($s['price']) ?></div>
            <div class="small text-muted">per kursi · sisa <?= $left ?> dari <?= $s['capacity'] ?></div>
        </div>
    </div>
</div>

<?php if ($closed): ?>
    <div class="alert alert-secondary">Jadwal ini sudah berangkat atau dibatalkan, sehingga tidak dapat dipesan.</div>
<?php elseif (! session('user_id')): ?>
    <div class="alert alert-info d-flex justify-content-between align-items-center">
        <span><i class="bi bi-info-circle"></i> Masuk atau daftar terlebih dahulu untuk memesan kursi.</span>
        <span><a href="<?= url_to('login') ?>" class="btn btn-sm btn-primary">Masuk</a> <a href="<?= url_to('register') ?>" class="btn btn-sm btn-outline-primary">Daftar</a></span>
    </div>
<?php endif ?>

<form action="<?= url_to('booking.create', $s['id']) ?>" method="post" id="bookingForm" novalidate>
    <?= csrf_field() ?>
    <div class="row g-4">
        <div class="col-lg-5">
            <div class="card p-4 text-center">
                <h2 class="h6 fw-bold mb-3">1. Pilih Kursi <span class="text-muted fw-normal">(maks 6)</span></h2>
                <div class="bus mx-auto">
                    <div class="driver"><i class="bi bi-person-badge me-1"></i> Sopir</div>
                    <?php foreach ($layout as $row): ?>
                        <div class="seat-row">
                            <?php foreach ($row as $seat): ?>
                                <?php if ($seat === null): ?><div class="aisle"></div><?php continue; endif ?>
                                <?php $isBooked = in_array($seat, $booked, true); ?>
                                <div class="seat">
                                    <input type="checkbox" name="seats[]" value="<?= $seat ?>" id="seat-<?= $seat ?>"
                                        <?= $isBooked || ! $canBook ? 'disabled' : '' ?> <?= in_array($seat, $old, true) && ! $isBooked ? 'checked' : '' ?>>
                                    <label for="seat-<?= $seat ?>" title="Kursi <?= $seat ?><?= $isBooked ? ' (terisi)' : '' ?>"><?= $seat ?></label>
                                </div>
                            <?php endforeach ?>
                        </div>
                    <?php endforeach ?>
                </div>
                <div class="legend small text-muted mt-3">
                    <span style="background: #f0fdfa; border: 2px solid #99f6e4"></span>Tersedia
                    <span class="ms-3" style="background: var(--tb)"></span>Dipilih
                    <span class="ms-3" style="background: #e2e8f0"></span>Terisi
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card p-4 mb-4">
                <h2 class="h6 fw-bold mb-3">2. Data Penumpang</h2>
                <div id="passengers"><p class="text-muted small mb-0" id="noSeat">Belum ada kursi yang dipilih.</p></div>
            </div>
            <div class="card p-4">
                <h2 class="h6 fw-bold mb-3">3. Metode Pembayaran (Transfer Bank)</h2>
                <?php foreach ($banks as $i => $bank): ?>
                    <div class="form-check border rounded-3 p-3 ps-5 mb-2">
                        <input class="form-check-input" type="radio" name="bank_account_id" id="bank<?= $bank['id'] ?>" value="<?= $bank['id'] ?>" <?= (string) old('bank_account_id', $i === 0 ? $bank['id'] : '') === (string) $bank['id'] ? 'checked' : '' ?> <?= $canBook ? '' : 'disabled' ?>>
                        <label class="form-check-label w-100" for="bank<?= $bank['id'] ?>"><span class="fw-semibold"><?= esc($bank['bank']) ?></span> <span class="text-muted small">a.n. <?= esc($bank['account_name']) ?></span></label>
                    </div>
                <?php endforeach ?>
                <hr>
                <div class="d-flex justify-content-between align-items-center">
                    <div><div class="small text-muted"><span id="seatCount">0</span> kursi × <?= rupiah($s['price']) ?></div><div class="fs-4 fw-bold">Total <span id="total" class="text-tb">Rp0</span></div></div>
                    <button class="btn btn-primary btn-lg" id="submitBtn" disabled><i class="bi bi-ticket-perforated"></i> Pesan Sekarang</button>
                </div>
                <div class="form-text">Selesaikan pembayaran maksimal 2 jam setelah memesan, atau kursi akan dilepas otomatis.</div>
            </div>
        </div>
    </div>
</form>
<template id="paxTpl">
    <div class="border rounded-3 p-3 mb-3 pax">
        <div class="fw-semibold mb-2"><i class="bi bi-person-fill text-tb"></i> Penumpang kursi <span class="badge bg-primary seat-no"></span></div>
        <div class="row g-2">
            <div class="col-md-5"><input class="form-control form-control-sm" data-f="name" placeholder="Nama sesuai KTP" required></div>
            <div class="col-md-4"><input class="form-control form-control-sm" data-f="id_number" placeholder="NIK (16 digit)" inputmode="numeric" maxlength="16" required></div>
            <div class="col-md-3"><input class="form-control form-control-sm" data-f="phone" placeholder="No. HP (opsional)"></div>
        </div>
    </div>
</template>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(() => {
    const price = <?= (int) $s['price'] ?>, max = 6;
    const oldData = <?= json_encode($oldP, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) ?>;
    const box = document.getElementById('passengers'), tpl = document.getElementById('paxTpl');
    const cache = {};
    const rupiah = n => 'Rp' + n.toLocaleString('id-ID');

    function render() {
        // simpan isian yang sudah diketik agar tidak hilang saat kursi ditambah/dikurangi
        box.querySelectorAll('.pax').forEach(el => {
            const seat = el.dataset.seat; cache[seat] = {};
            el.querySelectorAll('input').forEach(i => cache[seat][i.dataset.f] = i.value);
        });
        const seats = [...document.querySelectorAll('input[name="seats[]"]:checked')].map(c => c.value);
        box.innerHTML = seats.length ? '' : '<p class="text-muted small mb-0">Belum ada kursi yang dipilih.</p>';
        seats.forEach(seat => {
            const el = tpl.content.firstElementChild.cloneNode(true);
            el.dataset.seat = seat;
            el.querySelector('.seat-no').textContent = seat;
            el.querySelectorAll('input').forEach(i => {
                i.name = `passengers[${seat}][${i.dataset.f}]`;
                i.value = (cache[seat] || oldData[seat] || {})[i.dataset.f] || '';
            });
            box.appendChild(el);
        });
        document.getElementById('seatCount').textContent = seats.length;
        document.getElementById('total').textContent = rupiah(seats.length * price);
        document.getElementById('submitBtn').disabled = seats.length === 0;
    }

    document.querySelectorAll('input[name="seats[]"]').forEach(cb => cb.addEventListener('change', e => {
        if (document.querySelectorAll('input[name="seats[]"]:checked').length > max) {
            e.target.checked = false;
            alert('Maksimal ' + max + ' kursi per pemesanan.');
        }
        render();
    }));
    render();
})();
</script>
<?= $this->endSection() ?>
