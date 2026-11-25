<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-7">
        <div class="card p-3">
            <table class="table mb-0">
                <thead class="table-light"><tr><th>Nama</th><th>Username</th><th>Peran</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td><?= esc($u['name']) ?></td>
                        <td><code><?= esc($u['username']) ?></code></td>
                        <td><span class="badge <?= $u['role'] === 'admin' ? 'bg-primary' : 'bg-secondary' ?>"><?= esc($u['role']) ?></span></td>
                        <td class="text-end">
                            <?php if ((int) $u['id'] !== (int) session('userId')): ?>
                                <form action="<?= site_url('admin/users/' . $u['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus akun ini?')">
                                    <?= csrf_field() ?>
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            <?php else: ?>
                                <span class="small text-muted">akun Anda</span>
                            <?php endif ?>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
        <div class="card p-3 mt-4 small">
            <h2 class="h6 fw-bold">Hak akses per peran</h2>
            <table class="table table-sm mb-0">
                <tr><th>Fitur</th><th class="text-center">admin</th><th class="text-center">staff</th></tr>
                <tr><td>Melihat, menambah, mengubah data</td><td class="text-center">✅</td><td class="text-center">✅</td></tr>
                <tr><td>Menghapus mahasiswa / mata kuliah / produk</td><td class="text-center">✅</td><td class="text-center">—</td></tr>
                <tr><td>Mengelola akun pengguna</td><td class="text-center">✅</td><td class="text-center">—</td></tr>
            </table>
        </div>
    </div>
    <div class="col-lg-5">
        <div class="card p-4">
            <h2 class="h6 fw-bold">Tambah Akun</h2>
            <form action="<?= site_url('admin/users') ?>" method="post">
                <?= csrf_field() ?>
                <div class="mb-2"><label class="form-label">Nama</label><input class="form-control" name="name" value="<?= esc(old('name')) ?>" required></div>
                <div class="mb-2"><label class="form-label">Username</label><input class="form-control" name="username" value="<?= esc(old('username')) ?>" required></div>
                <div class="mb-2"><label class="form-label">Password</label><input type="password" class="form-control" name="password" minlength="8" required></div>
                <div class="mb-3">
                    <label class="form-label">Peran</label>
                    <select class="form-select" name="role"><option value="staff">staff</option><option value="admin">admin</option></select>
                </div>
                <button class="btn btn-primary"><i class="bi bi-person-plus"></i> Simpan</button>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
