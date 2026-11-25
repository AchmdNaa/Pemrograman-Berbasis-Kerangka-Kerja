<?php $q ??= ['origin' => '', 'destination' => '', 'date' => date('Y-m-d'), 'class' => '']; ?>
<form action="<?= url_to('search') ?>" method="get" class="row g-2 align-items-end">
    <div class="col-md-3">
        <label class="form-label small fw-semibold" for="asal"><i class="bi bi-geo-alt text-tb"></i> Dari</label>
        <select class="form-select" id="asal" name="asal" required>
            <option value="">Kota asal</option>
            <?php foreach ($cities as $c): ?><option <?= $q['origin'] === $c ? 'selected' : '' ?>><?= esc($c) ?></option><?php endforeach ?>
        </select>
    </div>
    <div class="col-md-auto d-none d-md-block pb-1">
        <button type="button" class="btn btn-light border rounded-circle" title="Tukar" onclick="const a=document.getElementById('asal'),t=document.getElementById('tujuan');[a.value,t.value]=[t.value,a.value]"><i class="bi bi-arrow-left-right"></i></button>
    </div>
    <div class="col-md-3">
        <label class="form-label small fw-semibold" for="tujuan"><i class="bi bi-geo-alt-fill text-tb"></i> Ke</label>
        <select class="form-select" id="tujuan" name="tujuan" required>
            <option value="">Kota tujuan</option>
            <?php foreach ($cities as $c): ?><option <?= $q['destination'] === $c ? 'selected' : '' ?>><?= esc($c) ?></option><?php endforeach ?>
        </select>
    </div>
    <div class="col-md-2">
        <label class="form-label small fw-semibold" for="tanggal"><i class="bi bi-calendar3 text-tb"></i> Tanggal</label>
        <input type="date" class="form-control" id="tanggal" name="tanggal" min="<?= date('Y-m-d') ?>" value="<?= esc($q['date']) ?>" required>
    </div>
    <div class="col-md">
        <button class="btn btn-primary w-100"><i class="bi bi-search"></i> Cari Bus</button>
    </div>
</form>
