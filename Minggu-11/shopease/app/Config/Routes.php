<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * Seluruh URL ShopEase didefinisikan eksplisit di sini (auto-routing dimatikan).
 * Placeholder: (:segment) = satu bagian URL tanpa "/", (:num) = angka.
 */

$routes->get('/', 'Home::index', ['as' => 'home']);

// Katalog
$routes->get('produk', 'Products::index', ['as' => 'products']);
$routes->get('produk/(:segment)', 'Products::show/$1', ['as' => 'product']);
$routes->get('kategori/(:segment)', 'Products::category/$1', ['as' => 'category']);

// Halaman informasi
$routes->get('tentang', 'Pages::about', ['as' => 'about']);
$routes->get('kontak', 'Pages::contact', ['as' => 'contact']);
$routes->post('kontak', 'Pages::sendMessage');

// ---------------------------------------------------------------- Autentikasi (Minggu 11)
// Hanya untuk tamu: pengguna yang sudah login diarahkan ke dashboard.
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('masuk', 'Auth::loginForm', ['as' => 'login']);
    $routes->post('masuk', 'Auth::login');
    $routes->get('daftar', 'Auth::registerForm', ['as' => 'register']);
    $routes->post('daftar', 'Auth::register');
});
$routes->post('keluar', 'Auth::logout', ['as' => 'logout', 'filter' => 'auth']);

// Area akun: semua peran yang sudah login.
$routes->group('akun', ['filter' => 'auth'], static function ($routes) {
    $routes->get('/', 'Account::index', ['as' => 'account']);
    $routes->get('profil', 'Account::profile', ['as' => 'account.profile']);
    $routes->post('profil', 'Account::updateProfile');
    $routes->post('password', 'Account::changePassword', ['as' => 'account.password']);
});

// Operator toko (admin juga boleh).
$routes->group('operator', ['filter' => ['auth', 'role:operator,admin'], 'namespace' => 'App\Controllers\Operator'], static function ($routes) {
    $routes->get('/', 'Dashboard::index', ['as' => 'operator.dashboard']);
    $routes->post('produk/(:num)/stok', 'Dashboard::updateStock/$1', ['as' => 'operator.stock']);
});

// Admin saja.
$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index', ['as' => 'admin.dashboard']);
    $routes->get('pengguna', 'Users::index', ['as' => 'admin.users']);
    $routes->post('pengguna', 'Users::create');
    $routes->post('pengguna/(:num)', 'Users::update/$1', ['as' => 'admin.users.update']);
});

// URL lama tetap berfungsi (redirect permanen)
$routes->addRedirect('shop', 'produk');

// Halaman 404 kustom
$routes->set404Override('App\Controllers\Errors::notFound');
