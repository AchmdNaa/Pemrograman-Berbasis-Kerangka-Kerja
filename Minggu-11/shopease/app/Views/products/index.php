<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>
<nav class="small mb-3" aria-label="breadcrumb">
    <ol class="breadcrumb mb-0">
        <li class="breadcrumb-item"><a href="<?= url_to('home') ?>">Beranda</a></li>
        <li class="breadcrumb-item <?= $active ? '' : 'active' ?>"><?= $active ? '<a href="' . url_to('products') . '">Produk</a>' : 'Produk' ?></li>
        <?php if ($active): ?><li class="breadcrumb-item active"><?= esc($active['name']) ?></li><?php endif ?>
    </ol>
</nav>

<div class="row g-4">
    <aside class="col-lg-3">
        <div class="card p-3">
            <h2 class="h6 fw-bold">Kategori</h2>
            <div class="list-group list-group-flush">
                <a href="<?= url_to('products') ?>" class="list-group-item list-group-item-action d-flex justify-content-between <?= $active ? '' : 'active' ?>" style="<?= $active ? '' : 'background: var(--se-orange); border-color: var(--se-orange)' ?>">Semua</a>
                <?php foreach ($categories as $c): ?>
                    <?php $on = $active && $active['id'] === $c['id']; ?>
                    <a href="<?= url_to('category', $c['slug']) ?>" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $on ? 'active' : '' ?>" style="<?= $on ? 'background: var(--se-orange); border-color: var(--se-orange)' : '' ?>">
                        <span><i class="bi <?= esc($c['icon']) ?> me-1"></i> <?= esc($c['name']) ?></span><span class="badge <?= $on ? 'bg-light text-dark' : 'bg-secondary-subtle text-secondary' ?>"><?= $c['total'] ?></span>
                    </a>
                <?php endforeach ?>
            </div>
        </div>
    </aside>

    <section class="col-lg-9">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
            <div>
                <h1 class="h4 section-title mb-0"><?= esc($heading) ?></h1>
                <div class="small text-muted">
                    <?= $total ?> produk<?= $keyword ? ' untuk "<strong>' . esc($keyword) . '</strong>"' : '' ?>
                    <?php if ($keyword): ?> · <a href="<?= current_url() ?>">hapus pencarian</a><?php endif ?>
                </div>
            </div>
            <form method="get" class="d-flex align-items-center gap-2">
                <?php if ($keyword): ?><input type="hidden" name="q" value="<?= esc($keyword) ?>"><?php endif ?>
                <label class="small text-muted text-nowrap" for="urut">Urutkan</label>
                <select id="urut" name="urut" class="form-select form-select-sm" onchange="this.form.submit()">
                    <?php foreach (['terbaru' => 'Terbaru', 'terlaris' => 'Terlaris', 'termurah' => 'Harga terendah', 'termahal' => 'Harga tertinggi', 'rating' => 'Rating tertinggi'] as $val => $label): ?>
                        <option value="<?= $val ?>" <?= $sort === $val ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach ?>
                </select>
            </form>
        </div>

        <?php if ($products === []): ?>
            <div class="card p-5 text-center text-muted">
                <i class="bi bi-search fs-1"></i>
                <p class="mt-2 mb-3">Produk yang Anda cari tidak ditemukan.</p>
                <a href="<?= url_to('products') ?>" class="btn btn-primary align-self-center">Lihat semua produk</a>
            </div>
        <?php endif ?>

        <div class="row g-3">
            <?php foreach ($products as $p): ?><div class="col-6 col-md-4 col-xl-3"><?= view('partials/product_card', ['p' => $p]) ?></div><?php endforeach ?>
        </div>
        <div class="mt-4 d-flex justify-content-center"><?= $pager->links('default', 'bootstrap') ?></div>
    </section>
</div>
<?= $this->endSection() ?>
