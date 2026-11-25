<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-xl-8"><div class="card p-3">
        <table class="table align-middle small mb-0">
            <thead class="table-light"><tr><th>Bank</th><th>No. Rekening</th><th>Atas Nama</th><th>Status</th><th></th></tr></thead>
            <?php foreach ($rows as $r): ?>
                <tr><td class="fw-semibold"><?= esc($r['bank']) ?></td><td class="font-monospace"><?= esc($r['account_no']) ?></td><td><?= esc($r['account_name']) ?></td><td><?= status_badge($r['is_active'] ? 'aktif' : 'batal') ?></td>
                    <td class="text-end text-nowrap"><a href="?edit=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                        <form action="<?= url_to('admin.banks.delete', $r['id']) ?>" method="post" class="d-inline" onsubmit="return confirm('Hapus rekening ini?')"><?= csrf_field() ?><button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button></form></td></tr>
            <?php endforeach ?>
        </table>
    </div></div>
    <div class="col-xl-4"><div class="card p-4">
        <h2 class="h6 fw-bold"><?= $edit ? 'Edit Rekening' : 'Tambah Rekening' ?></h2>
        <form action="<?= url_to('admin.banks') ?>" method="post">
            <?= csrf_field() ?><input type="hidden" name="id" value="<?= $edit['id'] ?? '' ?>">
            <div class="mb-2"><label class="form-label small">Bank</label><input class="form-control" name="bank" value="<?= esc(old('bank', $edit['bank'] ?? '')) ?>"></div>
            <div class="mb-2"><label class="form-label small">Nomor rekening</label><input class="form-control" name="account_no" value="<?= esc(old('account_no', $edit['account_no'] ?? '')) ?>"></div>
            <div class="mb-2"><label class="form-label small">Atas nama</label><input class="form-control" name="account_name" value="<?= esc(old('account_name', $edit['account_name'] ?? '')) ?>"></div>
            <div class="form-check form-switch mb-3"><input class="form-check-input" type="checkbox" name="is_active" value="1" id="act" <?= old('is_active', $edit['is_active'] ?? 1) ? 'checked' : '' ?>><label class="form-check-label" for="act">Tampilkan sebagai metode pembayaran</label></div>
            <button class="btn btn-primary">Simpan</button><?php if ($edit): ?> <a href="<?= url_to('admin.banks') ?>" class="btn btn-light">Batal</a><?php endif ?>
        </form>
    </div></div>
</div>
<?= $this->endSection() ?>
