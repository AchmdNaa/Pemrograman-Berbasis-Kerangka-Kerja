<?php if (! empty($e['photo'])): ?>
    <img src="<?= base_url('uploads/employees/' . $e['photo']) ?>" width="<?= $size ?>" height="<?= $size ?>" class="rounded-circle object-fit-cover" alt="">
<?php else: ?>
    <span class="rounded-circle bg-warning-subtle text-orange fw-bold d-inline-grid flex-shrink-0" style="width: <?= $size ?>px; height: <?= $size ?>px; place-items: center; font-size: <?= round($size / 2.6) ?>px">
        <?= esc(implode('', array_map(static fn ($w) => mb_substr($w, 0, 1), array_slice(explode(' ', $e['name']), 0, 2)))) ?>
    </span>
<?php endif ?>
