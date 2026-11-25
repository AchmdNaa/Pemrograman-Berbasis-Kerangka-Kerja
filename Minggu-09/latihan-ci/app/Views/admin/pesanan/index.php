<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="card p-3">
    <ul class="nav nav-pills mb-3">
        <li class="nav-item"><a class="nav-link <?= $status === '' ? 'active' : '' ?>" href="<?= site_url('admin/pesanan') ?>">Semua</a></li>
        <?php foreach (\App\Models\PesananModel::STATUS as $s): ?>
            <li class="nav-item"><a class="nav-link text-capitalize <?= $status === $s ? 'active' : '' ?>" href="?status=<?= $s ?>"><?= $s ?></a></li>
        <?php endforeach ?>
    </ul>
    <table class="table table-hover">
        <thead class="table-light"><tr><th>Kode</th><th>Pembeli</th><th>Tanggal</th><th>Total</th><th>Status</th><th></th></tr></thead>
        <tbody>
        <?php if ($pesanan === []): ?>
            <tr><td colspan="6" class="text-center text-muted py-4">Belum ada pesanan.</td></tr>
        <?php endif ?>
        <?php foreach ($pesanan as $o): ?>
            <tr>
                <td class="fw-semibold"><?= esc($o['kode']) ?></td>
                <td><?= esc($o['nama_pembeli']) ?><div class="small text-muted"><?= esc($o['telepon']) ?></div></td>
                <td><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></td>
                <td><?= rupiah($o['total']) ?></td>
                <td><?= view('admin/pesanan/_badge', ['status' => $o['status']]) ?></td>
                <td class="text-end"><a href="<?= site_url('admin/pesanan/' . $o['id']) ?>" class="btn btn-sm btn-outline-primary">Detail</a></td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>
    <?= $pager->links('default', 'bootstrap') ?>
</div>
<?= $this->endSection() ?>
