<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<?php $badge = ['aktif' => 'success', 'cuti' => 'warning', 'nonaktif' => 'secondary']; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0">Data Karyawan</h1>
    <a href="<?= url_to('admin.employees.new') ?>" class="btn btn-primary"><i class="bi bi-person-plus"></i> Tambah Karyawan</a>
</div>
<div class="row g-3 mb-4">
    <?php foreach ([['Total karyawan', $summary['total'], '#f97316'], ['Aktif', $summary['aktif'], '#10b981'], ['Cuti', $summary['cuti'], '#f59e0b'], ['Beban gaji / bulan', rupiah($summary['payroll']), '#0ea5e9']] as [$l, $n, $c]): ?>
        <div class="col-6 col-xl-3"><div class="card stat p-3 h-100" style="--accent: <?= $c ?>"><div class="small text-muted"><?= $l ?></div><div class="num fs-5 text-nowrap"><?= $n ?></div></div></div>
    <?php endforeach ?>
</div>
<div class="card p-3">
    <ul class="nav nav-pills small mb-3">
        <li class="nav-item"><a class="nav-link <?= $trash ? '' : 'active' ?>" href="<?= url_to('admin.employees') ?>">Karyawan</a></li>
        <li class="nav-item"><a class="nav-link <?= $trash ? 'active' : '' ?>" href="?arsip=1"><i class="bi bi-archive"></i> Arsip (<?= $archived ?>)</a></li>
    </ul>
    <form class="row g-2 mb-3" method="get">
        <?php if ($trash): ?><input type="hidden" name="arsip" value="1"><?php endif ?>
        <div class="col-md-5"><input type="search" name="q" value="<?= esc($q) ?>" class="form-control form-control-sm" placeholder="Cari nama, NIP, atau jabatan"></div>
        <div class="col-md-3"><select name="departemen" class="form-select form-select-sm"><option value="">Semua departemen</option><?php foreach ($departments as $d): ?><option <?= $dept === $d ? 'selected' : '' ?>><?= $d ?></option><?php endforeach ?></select></div>
        <div class="col-md-3"><select name="status" class="form-select form-select-sm"><option value="">Semua status</option><?php foreach ($statuses as $s): ?><option value="<?= $s ?>" <?= $status === $s ? 'selected' : '' ?>><?= ucfirst($s) ?></option><?php endforeach ?></select></div>
        <div class="col-md-1"><button class="btn btn-sm btn-outline-primary w-100"><i class="bi bi-funnel"></i></button></div>
    </form>
    <div class="table-responsive">
        <table class="table align-middle small">
            <thead class="table-light"><tr><th>Karyawan</th><th>NIP</th><th>Departemen</th><th>Masuk</th><th class="text-end">Gaji</th><th>Status</th><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php if ($employees === []): ?><tr><td colspan="7" class="text-center text-muted py-4"><?= $trash ? 'Arsip kosong.' : 'Tidak ada data karyawan.' ?></td></tr><?php endif ?>
            <?php foreach ($employees as $e): ?>
                <tr>
                    <td><div class="d-flex align-items-center gap-2"><?= view('admin/employees/_avatar', ['e' => $e, 'size' => 38]) ?><div><div class="fw-semibold"><?= esc($e['name']) ?></div><div class="text-muted"><?= esc($e['position']) ?></div></div></div></td>
                    <td><code><?= esc($e['nip']) ?></code></td>
                    <td><?= esc($e['department']) ?></td>
                    <td><?= date('d/m/Y', strtotime($e['hire_date'])) ?></td>
                    <td class="text-end"><?= rupiah($e['salary']) ?></td>
                    <td><span class="badge text-bg-<?= $badge[$e['status']] ?>"><?= ucfirst($e['status']) ?></span></td>
                    <td class="text-end text-nowrap">
                        <?php if ($trash): ?>
                            <form action="<?= url_to('admin.employees.restore', $e['id']) ?>" method="post" class="d-inline"><?= csrf_field() ?><button class="btn btn-sm btn-outline-success"><i class="bi bi-arrow-counterclockwise"></i> Pulihkan</button></form>
                            <form action="<?= url_to('admin.employees.purge', $e['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus permanen? Data tidak bisa dikembalikan.')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-x-octagon"></i></button></form>
                        <?php else: ?>
                            <a href="<?= url_to('admin.employees.show', $e['id']) ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a>
                            <a href="<?= url_to('admin.employees.edit', $e['id']) ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                            <form action="<?= url_to('admin.employees.delete', $e['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Pindahkan <?= esc($e['name'], 'js') ?> ke arsip?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form>
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
