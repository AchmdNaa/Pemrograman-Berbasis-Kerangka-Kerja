<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-xl-8"><div class="card p-3">
        <form class="d-flex gap-2 mb-3" method="get">
            <input type="date" class="form-control form-control-sm" name="tanggal" value="<?= esc($date) ?>" style="max-width: 170px">
            <select class="form-select form-select-sm" name="rute" style="max-width: 230px"><option value="">Semua rute</option><?php foreach ($routes as $r): ?><option value="<?= $r['id'] ?>" <?= $route == $r['id'] ? 'selected' : '' ?>><?= esc($r['origin']) ?> → <?= esc($r['destination']) ?></option><?php endforeach ?></select>
            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i></button>
        </form>
        <table class="table align-middle small">
            <thead class="table-light"><tr><th>Berangkat</th><th>Rute</th><th>Bus</th><th class="text-end">Harga</th><th class="text-center">Terisi</th><th>Status</th><th></th></tr></thead>
            <?php foreach ($schedules as $s): ?>
                <tr class="<?= $s['status'] === 'batal' ? 'table-secondary' : '' ?>"><td class="text-nowrap"><?= tgl($s['depart_at'], true, false) ?></td><td><?= esc($s['origin']) ?> → <?= esc($s['destination']) ?></td><td><?= esc($s['operator']) ?> <span class="text-muted"><?= esc($s['class']) ?></span></td><td class="text-end"><?= rupiah($s['price']) ?></td><td class="text-center"><?= $s['booked'] ?>/<?= $s['capacity'] ?></td><td><?= status_badge($s['status']) ?></td>
                    <td class="text-end text-nowrap"><a href="?edit=<?= $s['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="<?= url_to('admin.schedules.delete', $s['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus jadwal ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            <?php endforeach ?>
        </table>
        <?= $pager->links('default', 'bootstrap') ?>
    </div></div>
    <div class="col-xl-4"><div class="card p-4">
        <h2 class="h6 fw-bold"><?= $edit ? 'Edit Jadwal' : 'Tambah Jadwal' ?></h2>
        <form action="<?= url_to('admin.schedules') ?>" method="post">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <div class="mb-2"><label class="form-label small">Rute</label><select class="form-select" name="route_id"><?php foreach ($routes as $r): ?><option value="<?= $r['id'] ?>" <?= (string) old('route_id', $edit['route_id'] ?? '') === (string) $r['id'] ? 'selected' : '' ?>><?= esc($r['origin']) ?> → <?= esc($r['destination']) ?></option><?php endforeach ?></select></div>
            <div class="mb-2"><label class="form-label small">Bus</label><select class="form-select" name="bus_id"><?php foreach ($buses as $b): ?><option value="<?= $b['id'] ?>" <?= (string) old('bus_id', $edit['bus_id'] ?? '') === (string) $b['id'] ? 'selected' : '' ?>><?= esc($b['operator']) ?> · <?= esc($b['class']) ?> (<?= esc($b['code']) ?>)</option><?php endforeach ?></select></div>
            <div class="mb-2"><label class="form-label small">Waktu berangkat</label><input type="datetime-local" class="form-control" name="depart_at" value="<?= esc(old('depart_at', isset($edit['depart_at']) ? date('Y-m-d\TH:i', strtotime($edit['depart_at'])) : '')) ?>"><div class="form-text">Waktu tiba dihitung otomatis dari durasi rute.</div></div>
            <div class="row"><div class="col mb-3"><label class="form-label small">Harga (Rp)</label><input type="number" class="form-control" name="price" value="<?= esc(old('price', $edit['price'] ?? '')) ?>"></div>
                <div class="col mb-3"><label class="form-label small">Status</label><select class="form-select" name="status"><option value="aktif">Aktif</option><option value="batal" <?= old('status', $edit['status'] ?? '') === 'batal' ? 'selected' : '' ?>>Batal</option></select></div></div>
            <button class="btn btn-primary">Simpan</button><?php if ($edit): ?> <a href="<?= url_to('admin.schedules') ?>" class="btn btn-light">Batal</a><?php endif ?>
        </form>
    </div></div>
</div>
<?= $this->endSection() ?>
