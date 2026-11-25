<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<h1 class="h4 fw-bold mb-3">Manajemen Pengguna</h1>
<div class="card p-3 mb-4">
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <ul class="nav nav-pills small">
            <li class="nav-item"><a class="nav-link <?= $role === '' ? 'active' : '' ?>" href="<?= url_to('admin.users') ?>">Semua</a></li>
            <?php foreach (\App\Models\UserModel::ROLES as $r): ?>
                <li class="nav-item"><a class="nav-link text-capitalize <?= $role === $r ? 'active' : '' ?>" href="?role=<?= $r ?>"><?= $r ?></a></li>
            <?php endforeach ?>
        </ul>
        <form class="d-flex gap-2" method="get">
            <?php if ($role): ?><input type="hidden" name="role" value="<?= esc($role) ?>"><?php endif ?>
            <input type="search" name="q" value="<?= esc($q) ?>" class="form-control form-control-sm" placeholder="Cari nama/email">
            <button class="btn btn-sm btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table align-middle small">
            <thead class="table-light"><tr><th>Pengguna</th><th>Login terakhir</th><th style="width: 330px">Peran &amp; status</th></tr></thead>
            <tbody>
            <?php foreach ($users as $u): $self = (int) $u['id'] === (int) auth_user('id'); ?>
                <tr class="<?= $u['is_active'] ? '' : 'table-secondary' ?>">
                    <td><div class="fw-semibold"><?= esc($u['name']) ?> <?= $self ? '<span class="text-muted">(Anda)</span>' : '' ?></div><div class="text-muted"><?= esc($u['email']) ?></div></td>
                    <td><?= $u['last_login_at'] ? date('d/m/Y H:i', strtotime($u['last_login_at'])) : '<span class="text-muted">belum pernah</span>' ?></td>
                    <td>
                        <?php if ($self): ?>
                            <?= role_badge($u['role']) ?> <span class="badge text-bg-light border">aktif</span>
                        <?php else: ?>
                            <form action="<?= url_to('admin.users.update', $u['id']) ?>" method="post" class="d-flex gap-2 align-items-center">
                                <?= csrf_field() ?>
                                <select name="role" class="form-select form-select-sm">
                                    <?php foreach (\App\Models\UserModel::ROLES as $r): ?><option value="<?= $r ?>" <?= $u['role'] === $r ? 'selected' : '' ?>><?= ucfirst($r) ?></option><?php endforeach ?>
                                </select>
                                <select name="is_active" class="form-select form-select-sm">
                                    <option value="1" <?= $u['is_active'] ? 'selected' : '' ?>>Aktif</option>
                                    <option value="0" <?= $u['is_active'] ? '' : 'selected' ?>>Nonaktif</option>
                                </select>
                                <button class="btn btn-sm btn-primary">Simpan</button>
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

<div class="card p-4">
    <h2 class="h6 fw-bold mb-3"><i class="bi bi-person-plus"></i> Buat Akun Staf / Pengguna</h2>
    <?php $err = session('errors') ?? []; ?>
    <form action="<?= url_to('admin.users') ?>" method="post" class="row g-3" novalidate>
        <?= csrf_field() ?>
        <?php foreach (['name' => 'Nama', 'email' => 'Email', 'phone' => 'Nomor HP', 'password' => 'Password awal'] as $f => $label): ?>
            <div class="col-md-6">
                <label class="form-label small" for="new_<?= $f ?>"><?= $label ?></label>
                <input type="<?= $f === 'password' ? 'password' : 'text' ?>" class="form-control <?= isset($err[$f]) ? 'is-invalid' : '' ?>" id="new_<?= $f ?>" name="<?= $f ?>" value="<?= $f === 'password' ? '' : esc(old($f)) ?>">
                <div class="invalid-feedback"><?= esc($err[$f] ?? '') ?></div>
            </div>
        <?php endforeach ?>
        <div class="col-md-6">
            <label class="form-label small" for="new_role">Peran</label>
            <select class="form-select" id="new_role" name="role"><option value="operator">Operator</option><option value="admin">Admin</option><option value="customer">Customer</option></select>
        </div>
        <div class="col-md-6 d-flex align-items-end"><button class="btn btn-primary w-100"><i class="bi bi-save"></i> Buat Akun</button></div>
    </form>
</div>
<?= $this->endSection() ?>
