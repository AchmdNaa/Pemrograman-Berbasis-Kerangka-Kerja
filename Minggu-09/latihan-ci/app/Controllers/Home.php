<?php

namespace App\Controllers;

use App\Models\MahasiswaModel;
use App\Models\ProdukModel;

/**
 * Halaman depan: menghubungkan modul SIAKAD dan Toko Online.
 */
class Home extends BaseController
{
    public function index(): string
    {
        return view('home', [
            'title'          => 'Latihan CodeIgniter 4',
            'jumlahMhs'      => (new MahasiswaModel())->countAllResults(),
            'produkTerbaru'  => (new ProdukModel())->katalog()->where('produk.stok >', 0)->orderBy('produk.id', 'DESC')->findAll(4),
        ]);
    }
}
