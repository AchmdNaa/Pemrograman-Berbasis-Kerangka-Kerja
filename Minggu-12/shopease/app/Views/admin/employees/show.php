<?= $this->extend('layouts/dashboard') ?>
<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h4 fw-bold mb-0">Detail Karyawan</h1>
    <a href="<?= url_to('admin.employees') ?>" class="btn btn-light">&larr; Kembali</a>
</div>
<div class="card p-4">
    <div class="d-flex align-items-center gap-3 mb-4">
        <?= view('admin/employees/_avatar', ['e' => $e, 'size' => 80]) ?>
        <div><h2 class="h4 fw-bold mb-0"><?= esc($e['name']) ?></h2><div class="text-muted"><?= esc($e['position']) ?> · <?= esc($e['department']) ?></div></div>
    </div>
    <?php $masa = (new DateTime($e['hire_date']))->diff(new DateTime()); ?>
    <table class="table">
        <tr><th class="text-muted fw-normal w-25">NIP</th><td><code><?= esc($e['nip']) ?></code></td></tr>
        <tr><th class="text-muted fw-normal">Email</th><td><?= esc($e['email']) ?></td></tr>
        <tr><th class="text-muted fw-normal">Nomor HP</th><td><?= esc($e['phone'] ?? '-') ?></td></tr>
        <tr><th class="text-muted fw-normal">Tanggal masuk</th><td><?= date('d F Y', strtotime($e['hire_date'])) ?> (<?= $masa->y ?> tahun <?= $masa->m ?> bulan)</td></tr>
        <tr><th class="text-muted fw-normal">Gaji pokok</th><td><?= rupiah($e['salary']) ?></td></tr>
        <tr><th class="text-muted fw-normal">Status</th><td><?= ucfirst($e['status']) ?></td></tr>
        <tr><th class="text-muted fw-normal">Terakhir diubah</th><td><?= date('d/m/Y H:i', strtotime($e['updated_at'])) ?></td></tr>
    </table>
    <div><a href="<?= url_to('admin.employees.edit', $e['id']) ?>" class="btn btn-primary"><i class="bi bi-pencil"></i> Edit Data</a></div>
</div>
<?= $this->endSection() ?>
