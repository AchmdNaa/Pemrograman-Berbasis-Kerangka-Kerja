<div class="card product-card h-100">
    <a href="<?= site_url('toko/' . $p['slug']) ?>"><img src="<?= produk_img($p) ?>" class="w-100" alt="<?= esc($p['nama']) ?>"></a>
    <div class="card-body d-flex flex-column">
        <span class="badge badge-soft align-self-start mb-2"><?= esc($p['kategori']) ?></span>
        <a href="<?= site_url('toko/' . $p['slug']) ?>" class="title fw-semibold text-dark small"><?= esc($p['nama']) ?></a>
        <div class="fw-bold text-primary mt-2"><?= rupiah($p['harga']) ?></div>
        <div class="small <?= $p['stok'] > 0 ? 'text-muted' : 'text-danger' ?> mb-2">
            <?= $p['stok'] > 0 ? 'Stok ' . $p['stok'] : 'Stok habis' ?>
        </div>
        <form action="<?= site_url('keranjang/tambah') ?>" method="post" class="mt-auto">
            <?= csrf_field() ?>
            <input type="hidden" name="produk_id" value="<?= $p['id'] ?>">
            <input type="hidden" name="qty" value="1">
            <button class="btn btn-sm btn-primary w-100" <?= $p['stok'] > 0 ? '' : 'disabled' ?>><i class="bi bi-cart-plus"></i> Tambah</button>
        </form>
    </div>
</div>
