<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-xl-8"><div class="card p-3">
        <table class="table align-middle small mb-0">
            <thead class="table-light"><tr><th>Asal</th><th>Tujuan</th><th>Jarak</th><th>Durasi</th><th></th></tr></thead>
            <?php foreach ($rows as $r): ?>
                <tr><td class="fw-semibold"><?= esc($r['origin']) ?></td><td class="fw-semibold"><?= esc($r['destination']) ?></td><td><?= $r['distance_km'] ?> km</td><td><?= durasi((int) $r['duration_min']) ?></td>
                    <td class="text-end text-nowrap"><a href="?edit=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="<?= url_to('admin.routes.delete', $r['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus rute ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            <?php endforeach ?>
        </table>
    </div></div>
    <div class="col-xl-4"><div class="card p-4">
        <h2 class="h6 fw-bold"><?= $edit ? 'Edit Rute' : 'Tambah Rute' ?></h2>
        <form action="<?= url_to('admin.routes') ?>" method="post">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <div class="mb-2"><label class="form-label small">Kota asal</label><input class="form-control" name="origin" value="<?= esc(old('origin', $edit['origin'] ?? '')) ?>"></div>
            <div class="mb-2"><label class="form-label small">Kota tujuan</label><input class="form-control" name="destination" value="<?= esc(old('destination', $edit['destination'] ?? '')) ?>"></div>
            <div class="row"><div class="col mb-3"><label class="form-label small">Jarak (km)</label><input type="number" class="form-control" name="distance_km" value="<?= esc(old('distance_km', $edit['distance_km'] ?? '')) ?>"></div>
                <div class="col mb-3"><label class="form-label small">Durasi (menit)</label><input type="number" class="form-control" name="duration_min" value="<?= esc(old('duration_min', $edit['duration_min'] ?? '')) ?>"></div></div>
            <button class="btn btn-primary">Simpan</button><?php if ($edit): ?> <a href="<?= url_to('admin.routes') ?>" class="btn btn-light">Batal</a><?php endif ?>
        </form>
    </div></div>
</div>
<?= $this->endSection() ?>
