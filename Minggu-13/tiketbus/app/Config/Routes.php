<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// ---------------- Publik
$routes->get('/', 'Home::index', ['as' => 'home']);
$routes->get('cari', 'Home::search', ['as' => 'search']);
$routes->get('jadwal/(:num)', 'Home::schedule/$1', ['as' => 'schedule']);
$routes->get('cek-tiket', 'Home::check', ['as' => 'check']);

// ---------------- Autentikasi
$routes->group('', ['filter' => 'guest'], static function ($routes) {
    $routes->get('masuk', 'Auth::loginForm', ['as' => 'login']);
    $routes->post('masuk', 'Auth::login');
    $routes->get('daftar', 'Auth::registerForm', ['as' => 'register']);
    $routes->post('daftar', 'Auth::register');
});
$routes->post('keluar', 'Auth::logout', ['as' => 'logout', 'filter' => 'auth']);

// ---------------- Panel pelanggan
$routes->group('', ['filter' => ['auth', 'role:pelanggan']], static function ($routes) {
    $routes->post('jadwal/(:num)/pesan', 'Bookings::create/$1', ['as' => 'booking.create']);
    $routes->get('pesanan', 'Bookings::index', ['as' => 'my.bookings']);
    $routes->get('pesanan/(:segment)', 'Bookings::show/$1', ['as' => 'booking.show']);
    $routes->post('pesanan/(:segment)/bayar', 'Bookings::pay/$1', ['as' => 'booking.pay']);
    $routes->post('pesanan/(:segment)/batal', 'Bookings::cancel/$1', ['as' => 'booking.cancel']);
    $routes->get('profil', 'Bookings::profile', ['as' => 'profile']);
    $routes->post('profil', 'Bookings::updateProfile');
    $routes->post('profil/password', 'Bookings::changePassword', ['as' => 'profile.password']);
});

// E-tiket & bukti bayar: pemilik pesanan atau admin (dicek di controller)
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('tiket/(:segment)', 'Bookings::ticket/$1', ['as' => 'booking.ticket']);
    $routes->get('bukti/(:num)', 'Bookings::proof/$1', ['as' => 'payment.proof']);
});

// ---------------- Panel admin
$routes->group('admin', ['filter' => ['auth', 'role:admin'], 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index', ['as' => 'admin.dashboard']);

    foreach (['bus' => ['Buses', 'admin.buses'], 'rute' => ['Routes', 'admin.routes'], 'rekening' => ['Banks', 'admin.banks'], 'jadwal' => ['Schedules', 'admin.schedules']] as $path => [$ctl, $name]) {
        $routes->get($path, "{$ctl}::index", ['as' => $name]);
        $routes->post($path, "{$ctl}::save");
        $routes->post("{$path}/(:num)/hapus", "{$ctl}::delete/\$1", ['as' => "{$name}.delete"]);
    }

    $routes->get('pemesanan', 'Bookings::index', ['as' => 'admin.bookings']);
    $routes->get('pemesanan/(:num)', 'Bookings::show/$1', ['as' => 'admin.bookings.show']);
    $routes->post('pembayaran/(:num)/verifikasi', 'Bookings::verify/$1', ['as' => 'admin.payments.verify']);

    $routes->get('laporan', 'Reports::sales', ['as' => 'admin.reports']);
    $routes->get('pelanggan', 'Reports::customers', ['as' => 'admin.customers']);
});
