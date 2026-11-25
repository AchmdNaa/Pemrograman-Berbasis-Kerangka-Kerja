<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card p-3">
    <div class="d-flex flex-wrap gap-2 justify-content-between mb-3">
        <form class="d-flex gap-2" method="get">
            <input type="search" name="q" value="<?= esc($q) ?>" class="form-control" placeholder="Cari NIM atau nama...">
            <select name="prodi" class="form-select">
                <option value="">Semua prodi</option>
                <?php foreach ($daftarProdi as $p): ?>
                    <option <?= $p === $prodi ? 'selected' : '' ?>><?= esc($p) ?></option>
                <?php endforeach ?>
            </select>
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
        <a href="<?= site_url('admin/mahasiswa/new') ?>" class="btn btn-primary"><i class="bi bi-plus-lg"></i> Tambah Mahasiswa</a>
    </div>

    <div class="table-responsive">
        <table class="table table-hover">
            <thead class="table-light">
            <tr><th>Mahasiswa</th><th>NIM</th><th>Prodi</th><th>Angkatan</th><th>SKS</th><th>IPK</th><th class="text-end">Aksi</th></tr>
            </thead>
            <tbody>
            <?php if ($mahasiswa === []): ?>
                <tr><td colspan="7" class="text-center text-muted py-4">Tidak ada data yang cocok.</td></tr>
            <?php endif ?>
            <?php foreach ($mahasiswa as $m): ?>
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <img src="<?= foto_mhs($m) ?>" class="avatar" alt="">
                            <div><div class="fw-semibold"><?= esc($m['nama']) ?></div><div class="small text-muted"><?= esc($m['email'] ?? '-') ?></div></div>
                        </div>
                    </td>
                    <td><?= esc($m['nim']) ?></td>
                    <td><?= esc($m['prodi']) ?></td>
                    <td><?= esc($m['angkatan']) ?></td>
                    <td><?= $ipk[$m['id']]['sks'] ?? 0 ?></td>
                    <td><span class="fw-bold"><?= number_format($ipk[$m['id']]['ipk'] ?? 0, 2) ?></span></td>
                    <td class="text-end text-nowrap">
                        <a href="<?= site_url('admin/mahasiswa/' . $m['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Transkrip"><i class="bi bi-card-list"></i></a>
                        <a href="<?= site_url('admin/mahasiswa/' . $m['id'] . '/edit') ?>" class="btn btn-sm btn-outline-primary" title="Edit"><i class="bi bi-pencil"></i></a>
                        <?php if (is_admin()): ?>
                            <form action="<?= site_url('admin/mahasiswa/' . $m['id'] . '/delete') ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus <?= esc($m['nama'], 'js') ?>?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-outline-danger" title="Hapus"><i class="bi bi-trash"></i></button>
                            </form>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
    <?= $pager->links('default', 'bootstrap') ?>
</div>
<?= $this->endSection() ?>
