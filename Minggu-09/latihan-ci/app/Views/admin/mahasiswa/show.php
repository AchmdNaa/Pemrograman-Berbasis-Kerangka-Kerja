<?= $this->extend('layouts/admin') ?>
<?= $this->section('content') ?>
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card p-4 text-center">
            <img src="<?= foto_mhs($mhs) ?>" class="rounded-circle mx-auto mb-3" style="width: 120px; height: 120px; object-fit: cover" alt="">
            <h2 class="h5 fw-bold mb-0"><?= esc($mhs['nama']) ?></h2>
            <div class="text-muted"><?= esc($mhs['nim']) ?></div>
            <div class="small text-muted mb-3"><?= esc($mhs['prodi']) ?> · Angkatan <?= esc($mhs['angkatan']) ?></div>
            <div class="row border-top pt-3">
                <div class="col"><div class="small text-muted">Total SKS</div><div class="fs-4 fw-bold"><?= $ringkasan['sks'] ?></div></div>
                <div class="col"><div class="small text-muted">IPK</div><div class="fs-4 fw-bold text-primary"><?= number_format($ringkasan['ipk'], 2) ?></div></div>
            </div>
            <a href="<?= site_url('admin/mahasiswa/' . $mhs['id'] . '/edit') ?>" class="btn btn-outline-primary btn-sm mt-3"><i class="bi bi-pencil"></i> Edit Profil</a>
        </div>

        <div class="card p-4 mt-4">
            <h3 class="h6 fw-bold">Input Nilai</h3>
            <?php if ($mkTersedia === []): ?>
                <p class="small text-muted mb-0">Semua mata kuliah sudah memiliki nilai.</p>
            <?php else: ?>
                <form action="<?= site_url('admin/mahasiswa/' . $mhs['id'] . '/nilai') ?>" method="post">
                    <?= csrf_field() ?>
                    <select name="matakuliah_id" class="form-select mb-2" required>
                        <?php foreach ($mkTersedia as $mk): ?>
                            <option value="<?= $mk['id'] ?>">Smt <?= $mk['semester'] ?> · <?= esc($mk['nama']) ?> (<?= $mk['sks'] ?> SKS)</option>
                        <?php endforeach ?>
                    </select>
                    <div class="d-flex gap-2">
                        <select name="nilai_huruf" class="form-select" required>
                            <?php foreach ($bobot as $huruf => $b): ?>
                                <option value="<?= $huruf ?>"><?= $huruf ?> (<?= number_format($b, 1) ?>)</option>
                            <?php endforeach ?>
                        </select>
                        <button class="btn btn-primary"><i class="bi bi-plus-lg"></i></button>
                    </div>
                </form>
            <?php endif ?>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card p-4">
            <h3 class="h6 fw-bold mb-3">Transkrip Nilai</h3>
            <table class="table">
                <thead class="table-light"><tr><th>Smt</th><th>Kode</th><th>Mata Kuliah</th><th>SKS</th><th>Nilai</th><th>Mutu</th><th></th></tr></thead>
                <tbody>
                <?php if ($transkrip === []): ?>
                    <tr><td colspan="7" class="text-center text-muted py-4">Belum ada nilai.</td></tr>
                <?php endif ?>
                <?php foreach ($transkrip as $t): ?>
                    <tr>
                        <td><?= $t['semester'] ?></td>
                        <td><code><?= esc($t['kode']) ?></code></td>
                        <td><?= esc($t['nama']) ?></td>
                        <td><?= $t['sks'] ?></td>
                        <td><span class="badge bg-primary"><?= esc($t['nilai_huruf']) ?></span></td>
                        <td><?= number_format($bobot[$t['nilai_huruf']] * $t['sks'], 1) ?></td>
                        <td class="text-end">
                            <form action="<?= site_url('admin/mahasiswa/' . $mhs['id'] . '/nilai/' . $t['id'] . '/delete') ?>" method="post" onsubmit="return confirm('Hapus nilai ini?')">
                                <?= csrf_field() ?>
                                <button class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-x-circle"></i></button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
                <tfoot><tr class="fw-bold"><td colspan="3">Total</td><td><?= $ringkasan['sks'] ?></td><td colspan="3">IPK <?= number_format($ringkasan['ipk'], 2) ?></td></tr></tfoot>
            </table>
            <p class="small text-muted mb-0">IPK = Σ(bobot nilai × SKS) ÷ Σ SKS</p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
