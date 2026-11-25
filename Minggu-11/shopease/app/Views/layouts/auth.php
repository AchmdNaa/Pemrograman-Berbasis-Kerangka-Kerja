<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= esc($title ?? 'Akun') ?> · ShopEase</title>
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/vendor/bootstrap-icons/bootstrap-icons.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/shop.css') ?>">
</head>
<body class="auth-page">
<div class="container-fluid">
    <div class="row min-vh-100">
        <div class="col-lg-5 d-none d-lg-flex flex-column justify-content-between p-5 auth-side text-white">
            <a href="<?= url_to('home') ?>" class="fs-3 fw-bold text-white"><i class="bi bi-bag-heart-fill text-orange"></i> ShopEase</a>
            <div>
                <h2 class="display-6 fw-bold">Belanja lebih tenang dengan akun ShopEase.</h2>
                <ul class="list-unstyled mt-4 opacity-75">
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-orange me-2"></i>Simpan alamat dan riwayat pesanan</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-orange me-2"></i>Pantau status pembayaran &amp; pengiriman</li>
                    <li class="mb-2"><i class="bi bi-check-circle-fill text-orange me-2"></i>Password terenkripsi &amp; sesi yang aman</li>
                </ul>
            </div>
            <div class="small opacity-50">PBKK D · Achmad Najwa · 5025231265</div>
        </div>
        <div class="col-lg-7 d-flex align-items-center justify-content-center p-4">
            <div class="w-100" style="max-width: 440px">
                <a href="<?= url_to('home') ?>" class="d-lg-none fs-4 fw-bold text-dark d-block mb-4"><i class="bi bi-bag-heart-fill text-orange"></i> ShopEase</a>
                <?= $this->include('partials/flash') ?>
                <?= $this->renderSection('content') ?>
            </div>
        </div>
    </div>
</div>
<script src="<?= base_url('assets/vendor/bootstrap/bootstrap.bundle.min.js') ?>"></script>
<script>
    // Tombol tampilkan/sembunyikan password
    document.querySelectorAll('[data-toggle-password]').forEach(btn => btn.addEventListener('click', () => {
        const input = document.getElementById(btn.dataset.togglePassword);
        input.type = input.type === 'password' ? 'text' : 'password';
        btn.querySelector('i').className = input.type === 'password' ? 'bi bi-eye' : 'bi bi-eye-slash';
    }));
</script>
</body>
</html>
