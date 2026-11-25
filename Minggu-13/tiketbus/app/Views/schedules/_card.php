<?php $left = (int) $s['capacity'] - (int) $s['booked']; ?>
<div class="card schedule-card p-3 h-100">
    <div class="d-flex justify-content-between align-items-start mb-2">
        <div><span class="fw-bold"><?= esc($s['operator']) ?></span> <span class="badge bg-tb-soft text-tb border"><?= esc($s['class']) ?></span><div class="small text-muted"><?= esc($s['bus_code']) ?> · <?= tgl($s['depart_at'], false) ?></div></div>
        <div class="text-end"><div class="fs-5 fw-bold text-tb"><?= rupiah($s['price']) ?></div><div class="small text-muted">/ kursi</div></div>
    </div>
    <div class="d-flex align-items-center gap-2 my-2">
        <div><div class="time"><?= date('H:i', strtotime($s['depart_at'])) ?></div><div class="small text-muted"><?= esc($s['origin']) ?></div></div>
        <div class="route-line"></div><span class="small text-muted text-nowrap"><?= durasi((int) $s['duration_min']) ?></span><div class="route-line"></div>
        <div class="text-end"><div class="time"><?= date('H:i', strtotime($s['arrive_at'])) ?></div><div class="small text-muted"><?= esc($s['destination']) ?></div></div>
    </div>
    <div class="d-flex justify-content-between align-items-center mt-2">
        <span class="small <?= $left <= 5 ? 'text-danger fw-semibold' : 'text-muted' ?>"><i class="bi bi-person-fill"></i> <?= $left > 0 ? "Sisa {$left} kursi" : 'Penuh' ?></span>
        <a href="<?= url_to('schedule', $s['id']) ?>" class="btn btn-sm btn-primary <?= $left > 0 ? '' : 'disabled' ?>">Pilih Kursi <i class="bi bi-chevron-right"></i></a>
    </div>
</div>
