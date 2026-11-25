<?php

namespace App\Controllers;

use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Katalog publik toko online.
 */
class Toko extends BaseController
{
    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $kategori = (string) $this->request->getGet('kategori');
        $model = new ProdukModel();

        return view('toko/index', [
            'title'    => 'Toko Online',
            'produk'   => $model->katalog($q, $kategori)->paginate(8),
            'pager'    => $model->pager,
            'kategori' => (new KategoriModel())->orderBy('nama')->findAll(),
            'q'        => $q,
            'aktif'    => $kategori,
        ]);
    }

    public function detail(string $slug): string
    {
        $produk = (new ProdukModel())->katalog()->where('produk.slug', $slug)->first()
            ?? throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');

        $terkait = (new ProdukModel())->katalog()
            ->where('produk.kategori_id', $produk['kategori_id'])
            ->where('produk.id !=', $produk['id'])
            ->findAll(4);

        return view('toko/detail', [
            'title'   => $produk['nama'],
            'produk'  => $produk,
            'terkait' => $terkait,
        ]);
    }
}
