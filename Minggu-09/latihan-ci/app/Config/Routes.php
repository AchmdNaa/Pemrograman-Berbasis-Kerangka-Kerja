<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 *
 * Latihan 1.2 — Router & Controller: setiap URL dipetakan secara eksplisit ke Controller::method.
 */

// ---------------- Publik ----------------
$routes->get('/', 'Home::index');

$routes->get('toko', 'Toko::index');
$routes->get('toko/(:segment)', 'Toko::detail/$1');

$routes->get('keranjang', 'Keranjang::index');
$routes->post('keranjang/tambah', 'Keranjang::add');
$routes->post('keranjang/update', 'Keranjang::update');
$routes->post('keranjang/hapus/(:num)', 'Keranjang::remove/$1');
$routes->post('keranjang/kosongkan', 'Keranjang::clear');
$routes->get('checkout', 'Keranjang::checkout');
$routes->post('checkout', 'Keranjang::placeOrder');
$routes->get('pesanan/(:segment)', 'Keranjang::sukses/$1');

// ---------------- Autentikasi ----------------
$routes->get('login', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->post('logout', 'Auth::logout');

// ---------------- Panel admin (wajib login) ----------------
$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('/', 'Dashboard::index');

    // SIAKAD — mahasiswa
    $routes->get('mahasiswa', 'Mahasiswa::index');
    $routes->get('mahasiswa/new', 'Mahasiswa::new');
    $routes->post('mahasiswa', 'Mahasiswa::create');
    $routes->get('mahasiswa/(:num)', 'Mahasiswa::show/$1');
    $routes->get('mahasiswa/(:num)/edit', 'Mahasiswa::edit/$1');
    $routes->post('mahasiswa/(:num)', 'Mahasiswa::update/$1');
    $routes->post('mahasiswa/(:num)/nilai', 'Nilai::store/$1');
    $routes->post('mahasiswa/(:num)/nilai/(:num)/delete', 'Nilai::delete/$1/$2');

    // SIAKAD — mata kuliah
    $routes->get('matakuliah', 'Matakuliah::index');
    $routes->post('matakuliah', 'Matakuliah::save');

    // Toko — produk & pesanan
    $routes->get('produk', 'Produk::index');
    $routes->get('produk/new', 'Produk::new');
    $routes->post('produk', 'Produk::create');
    $routes->get('produk/(:num)/edit', 'Produk::edit/$1');
    $routes->post('produk/(:num)', 'Produk::update/$1');
    $routes->get('pesanan', 'Pesanan::index');
    $routes->get('pesanan/(:num)', 'Pesanan::show/$1');
    $routes->post('pesanan/(:num)/status', 'Pesanan::status/$1');

    // Hanya admin: menghapus data & mengelola akun
    $routes->group('', ['filter' => 'role:admin'], static function ($routes) {
        $routes->post('mahasiswa/(:num)/delete', 'Mahasiswa::delete/$1');
        $routes->post('matakuliah/(:num)/delete', 'Matakuliah::delete/$1');
        $routes->post('produk/(:num)/delete', 'Produk::delete/$1');
        $routes->get('users', 'Users::index');
        $routes->post('users', 'Users::create');
        $routes->post('users/(:num)/delete', 'Users::delete/$1');
    });
});
