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

// URL lama tetap berfungsi (redirect permanen)
$routes->addRedirect('shop', 'produk');

// Halaman 404 kustom
$routes->set404Override('App\Controllers\Errors::notFound');
