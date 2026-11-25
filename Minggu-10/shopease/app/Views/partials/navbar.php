<div class="topbar py-1">
    <div class="container d-flex justify-content-between">
        <span><i class="bi bi-truck"></i> Gratis ongkir untuk belanja di atas Rp150.000</span>
        <span class="d-none d-md-inline"><i class="bi bi-headset"></i> Bantuan: 0812-3456-7890</span>
    </div>
</div>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top py-3">
    <div class="container">
        <a class="navbar-brand fs-4" href="<?= url_to('home') ?>"><i class="bi bi-bag-heart-fill text-orange"></i> Shop<span>Ease</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="mainNav">
            <ul class="navbar-nav me-3">
                <li class="nav-item"><a class="nav-link <?= url_is('/') ? 'active' : '' ?>" href="<?= url_to('home') ?>">Beranda</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('produk*') || url_is('kategori*') ? 'active' : '' ?>" href="<?= url_to('products') ?>">Produk</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('tentang') ? 'active' : '' ?>" href="<?= url_to('about') ?>">Tentang</a></li>
                <li class="nav-item"><a class="nav-link <?= url_is('kontak') ? 'active' : '' ?>" href="<?= url_to('contact') ?>">Kontak</a></li>
            </ul>
            <form class="d-flex flex-grow-1 me-lg-3 my-2 my-lg-0" action="<?= url_to('products') ?>" method="get" role="search">
                <div class="input-group">
                    <input class="form-control" type="search" name="q" placeholder="Cari produk di ShopEase..." value="<?= esc($keyword ?? '') ?>">
                    <button class="btn btn-primary" type="submit"><i class="bi bi-search"></i></button>
                </div>
            </form>
            <?= $this->include('partials/navbar_actions') ?>
        </div>
    </div>
</nav>
