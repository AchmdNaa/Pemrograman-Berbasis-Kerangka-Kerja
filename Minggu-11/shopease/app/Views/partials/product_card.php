<?php $disc = discount_percent($p); ?>
<div class="card product-card h-100">
    <a href="<?= url_to('product', $p['slug']) ?>" class="position-relative d-block">
        <img src="<?= product_image($p) ?>" class="w-100" alt="<?= esc($p['name']) ?>">
        <?php if ($disc): ?><span class="badge badge-discount position-absolute top-0 start-0 m-2">-<?= $disc ?>%</span><?php endif ?>
        <?php if ((int) $p['stock'] === 0): ?><span class="badge bg-dark position-absolute top-0 end-0 m-2">Stok habis</span><?php endif ?>
    </a>
    <div class="card-body p-3">
        <a href="<?= url_to('category', $p['category_slug']) ?>" class="small text-muted"><?= esc($p['category']) ?></a>
        <a href="<?= url_to('product', $p['slug']) ?>" class="name d-block fw-semibold mt-1"><?= esc($p['name']) ?></a>
        <div class="mt-2">
            <span class="fw-bold fs-6"><?= rupiah($p['price']) ?></span>
            <?php if ($disc): ?><span class="price-old ms-1"><?= rupiah($p['old_price']) ?></span><?php endif ?>
        </div>
        <div class="meta d-flex align-items-center gap-1 small text-muted mt-1">
            <?= stars($p['rating']) ?> <span><?= number_format((float) $p['rating'], 1) ?></span> · <span><?= number_format((int) $p['sold'], 0, ',', '.') ?> terjual</span>
        </div>
    </div>
</div>
