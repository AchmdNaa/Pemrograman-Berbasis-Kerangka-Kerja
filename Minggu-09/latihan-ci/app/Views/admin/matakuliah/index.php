<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card p-3">
            <table class="table table-hover mb-0">
                <thead class="table-light"><tr><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Semester</th><th class="text-end">Aksi</th></tr></thead>
                <tbody>
                <?php foreach ($matakuliah as $mk): ?>
                    <tr>
                        <td><code><?= esc($mk['kode']) ?></code></td>
                        <td><?= esc($mk['nama']) ?></td>
                        <td><?= $mk['sks'] ?></td>
                        <td><?= $mk['semester'] ?></td>
                        <td class="text-end text-nowrap">
                            <a href="?edit=<?= $mk['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <?php if (is_admin()): ?>
                                <form action="<?= site_url('admin/matakuliah/' . $mk['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus mata kuliah ini?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card p-4">
            <h2 class="h6 fw-bold"><?= $edit ? 'Edit Mata Kuliah' : 'Tambah Mata Kuliah' ?></h2>
            <form action="<?= site_url('admin/matakuliah') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
                <div class="mb-2"><label class="form-label">Kode</label><input class="form-control" name="kode" maxlength="10" value="<?= esc(old('kode', $edit['kode'] ?? '')) ?>" required></div>
                <div class="mb-2"><label class="form-label">Nama</label><input class="form-control" name="nama" value="<?= esc(old('nama', $edit['nama'] ?? '')) ?>" required></div>
                <div class="row">
                    <div class="col mb-3"><label class="form-label">SKS</label><input type="number" min="1" max="6" class="form-control" name="sks" value="<?= esc(old('sks', $edit['sks'] ?? 3)) ?>" required></div>
                    <div class="col mb-3"><label class="form-label">Semester</label><input type="number" min="1" max="8" class="form-control" name="semester" value="<?= esc(old('semester', $edit['semester'] ?? 1)) ?>" required></div>
                </div>
                <button class="btn btn-primary"><i class="bi bi-save"></i> Simpan</button>
                <?php if ($edit): ?><a href="<?= site_url('admin/matakuliah') ?>" class="btn btn-light">Batal</a><?php endif ?>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
