<?php if (auth_check()): ?>
    <div class="d-flex gap-2 align-items-center">
        <a href="#" class="btn btn-outline-secondary" title="Keranjang"><i class="bi bi-cart3"></i></a>
        <div class="dropdown">
            <button class="btn btn-light border dropdown-toggle d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                <span class="rounded-circle bg-warning-subtle text-orange fw-bold d-inline-grid" style="width: 28px; height: 28px; place-items: center"><?= esc(mb_strtoupper(mb_substr(auth_user('name'), 0, 1))) ?></span>
                <span class="d-none d-xl-inline"><?= esc(explode(' ', auth_user('name'))[0]) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                <li class="px-3 py-2 small"><div class="fw-semibold"><?= esc(auth_user('name')) ?></div><div class="text-muted"><?= esc(auth_user('email')) ?></div><div class="mt-1"><?= role_badge(auth_user('role')) ?></div></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="<?= dashboard_url() ?>"><i class="bi bi-speedometer2 me-2"></i>Dashboard</a></li>
                <li><a class="dropdown-item" href="<?= url_to('account.profile') ?>"><i class="bi bi-person-gear me-2"></i>Profil &amp; Keamanan</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form action="<?= url_to('logout') ?>" method="post" class="m-0"><?= csrf_field() ?>
                        <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
<?php else: ?>
    <div class="d-flex gap-2">
        <a href="<?= url_to('login') ?>" class="btn btn-outline-secondary" title="Keranjang"><i class="bi bi-cart3"></i></a>
        <a href="<?= url_to('login') ?>" class="btn btn-outline-primary">Masuk</a>
        <a href="<?= url_to('register') ?>" class="btn btn-primary">Daftar</a>
    </div>
<?php endif ?>
