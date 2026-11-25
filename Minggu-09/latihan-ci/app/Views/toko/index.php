<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
<div class="d-flex flex-wrap justify-content-between align-items-center mb-3 gap-2">
    <h1 class="h4 fw-bold mb-0">Toko Online KampusKu</h1>
    <form class="d-flex gap-2" method="get">
        <?php if ($aktif): ?><input type="hidden" name="kategori" value="<?= esc($aktif) ?>"><?php endif ?>
        <input type="search" class="form-control" name="q" value="<?= esc($q) ?>" placeholder="Cari produk...">
        <button class="btn btn-primary"><i class="bi bi-search"></i></button>
    </form>
</div>

<div class="mb-4 d-flex flex-wrap gap-2">
    <a href="<?= site_url('toko') . ($q ? '?q=' . urlencode($q) : '') ?>" class="btn btn-sm <?= $aktif === '' ? 'btn-dark' : 'btn-outline-secondary' ?>">Semua</a>
    <?php foreach ($kategori as $k): ?>
        <a href="?kategori=<?= esc($k['slug'], 'url') ?><?= $q ? '&q=' . urlencode($q) : '' ?>" class="btn btn-sm <?= $aktif === $k['slug'] ? 'btn-dark' : 'btn-outline-secondary' ?>"><?= esc($k['nama']) ?></a>
    <?php endforeach ?>
</div>

<?php if ($produk === []): ?>
    <div class="text-center text-muted py-5"><i class="bi bi-search fs-1"></i><p>Produk tidak ditemukan.</p></div>
<?php endif ?>
<div class="row g-3">
    <?php foreach ($produk as $p): ?>
        <div class="col-6 col-md-4 col-lg-3"><?= view('toko/_card', ['p' => $p]) ?></div>
    <?php endforeach ?>
</div>
<div class="mt-4"><?= $pager->links('default', 'bootstrap') ?></div>
<?= $this->endSection() ?>
