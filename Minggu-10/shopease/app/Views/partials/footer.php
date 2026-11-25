<footer class="site-footer pt-5 pb-4 mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="fs-4 fw-bold text-white mb-2"><i class="bi bi-bag-heart-fill text-orange"></i> ShopEase</div>
                <p class="small">Toko online untuk kebutuhan sehari-hari: elektronik, fashion, rumah tangga, olahraga, dan alat tulis, dengan harga ramah mahasiswa.</p>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-white fw-semibold mb-2">Belanja</div>
                <ul class="list-unstyled small">
                    <li><a href="<?= url_to('products') ?>">Semua Produk</a></li>
                    <li><a href="<?= url_to('products') ?>?urut=terlaris">Terlaris</a></li>
                    <li><a href="<?= url_to('products') ?>?urut=termurah">Harga Termurah</a></li>
                </ul>
            </div>
            <div class="col-6 col-md-2">
                <div class="text-white fw-semibold mb-2">ShopEase</div>
                <ul class="list-unstyled small">
                    <li><a href="<?= url_to('about') ?>">Tentang Kami</a></li>
                    <li><a href="<?= url_to('contact') ?>">Hubungi Kami</a></li>
                </ul>
            </div>
            <div class="col-md-4 small">
                <div class="text-white fw-semibold mb-2">Kontak</div>
                <div><i class="bi bi-geo-alt"></i> Jl. Teknik Kimia, Keputih, Sukolilo, Surabaya</div>
                <div><i class="bi bi-envelope"></i> halo@shopease.test</div>
            </div>
        </div>
        <hr class="border-secondary">
        <div class="d-flex justify-content-between small">
            <span>&copy; <?= date('Y') ?> ShopEase — Tugas PBKK D</span>
            <span>Achmad Najwa · 5025231265</span>
        </div>
    </div>
</footer>
