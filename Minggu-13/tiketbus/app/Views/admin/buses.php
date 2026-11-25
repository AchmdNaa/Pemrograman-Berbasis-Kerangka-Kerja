<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-xl-8"><div class="card p-3">
        <table class="table align-middle small mb-0">
            <thead class="table-light"><tr><th>No. Polisi</th><th>PO</th><th>Kelas</th><th>Kursi</th><th>Fasilitas</th><th></th></tr></thead>
            <?php foreach ($rows as $r): ?>
                <tr><td class="fw-semibold"><?= esc($r['code']) ?></td><td><?= esc($r['operator']) ?></td><td><?= esc($r['class']) ?></td><td><?= $r['seat_rows'] * $r['seat_cols'] ?> <span class="text-muted">(<?= $r['seat_rows'] ?>×<?= $r['seat_cols'] ?>)</span></td><td class="text-muted"><?= esc($r['facilities']) ?></td>
                    <td class="text-end text-nowrap"><a href="?edit=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="<?= url_to('admin.buses.delete', $r['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus bus ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            <?php endforeach ?>
        </table>
    </div></div>
    <div class="col-xl-4"><div class="card p-4">
        <h2 class="h6 fw-bold"><?= $edit ? 'Edit Bus' : 'Tambah Bus' ?></h2>
        <form action="<?= url_to('admin.buses') ?>" method="post">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <div class="mb-2"><label class="form-label small">Nomor polisi</label><input class="form-control" name="code" value="<?= esc(old('code', $edit['code'] ?? '')) ?>" placeholder="L 1234 AB"></div>
            <div class="mb-2"><label class="form-label small">Nama PO</label><input class="form-control" name="operator" value="<?= esc(old('operator', $edit['operator'] ?? '')) ?>"></div>
            <div class="mb-2"><label class="form-label small">Kelas</label><select class="form-select" name="class"><?php foreach ($classes as $c): ?><option <?= old('class', $edit['class'] ?? '') === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach ?></select></div>
            <div class="row"><div class="col mb-2"><label class="form-label small">Baris kursi</label><input type="number" class="form-control" name="seat_rows" min="2" max="15" value="<?= esc(old('seat_rows', $edit['seat_rows'] ?? 10)) ?>"></div>
                <div class="col mb-2"><label class="form-label small">Kursi/baris</label><select class="form-select" name="seat_cols"><option value="4" <?= (string) old('seat_cols', $edit['seat_cols'] ?? 4) === '4' ? 'selected' : '' ?>>4 (2-2)</option><option value="3" <?= (string) old('seat_cols', $edit['seat_cols'] ?? 4) === '3' ? 'selected' : '' ?>>3 (1-2)</option></select></div></div>
            <div class="mb-3"><label class="form-label small">Fasilitas</label><input class="form-control" name="facilities" value="<?= esc(old('facilities', $edit['facilities'] ?? '')) ?>" placeholder="AC, Toilet, USB charger"></div>
            <button class="btn btn-primary">Simpan</button><?php if ($edit): ?> <a href="<?= url_to('admin.buses') ?>" class="btn btn-light">Batal</a><?php endif ?>
        </form>
    </div></div>
</div>
<?= $this->endSection() ?>
