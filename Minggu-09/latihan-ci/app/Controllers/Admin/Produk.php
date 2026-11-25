<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KategoriModel;
use App\Models\ProdukModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Pengelolaan produk toko online + upload gambar (Latihan 2b).
 */
class Produk extends BaseController
{
    private const GAMBAR_DIR = FCPATH . 'uploads/produk/';

    private ProdukModel $model;

    public function __construct()
    {
        $this->model = new ProdukModel();
    }

    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));

        return view('admin/produk/index', [
            'title'  => 'Produk',
            'produk' => $this->model->katalog($q)->paginate(10),
            'pager'  => $this->model->pager,
            'q'      => $q,
        ]);
    }

    public function new(): string
    {
        return view('admin/produk/form', [
            'title'    => 'Tambah Produk',
            'produk'   => null,
            'kategori' => (new KategoriModel())->orderBy('nama')->findAll(),
        ]);
    }

    public function create(): RedirectResponse
    {
        return $this->persist(null);
    }

    public function edit(int $id): string
    {
        return view('admin/produk/form', [
            'title'    => 'Edit Produk',
            'produk'   => $this->findOr404($id),
            'kategori' => (new KategoriModel())->orderBy('nama')->findAll(),
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        return $this->persist($this->findOr404($id));
    }

    public function delete(int $id): RedirectResponse
    {
        $produk = $this->findOr404($id);
        $this->model->delete($id);
        if ($produk['gambar'] && is_file(self::GAMBAR_DIR . $produk['gambar'])) {
            unlink(self::GAMBAR_DIR . $produk['gambar']);
        }

        return redirect()->to('/admin/produk')->with('success', "Produk {$produk['nama']} dihapus.");
    }

    private function persist(?array $lama): RedirectResponse
    {
        // Gambar opsional: validasi hanya bila ada file yang diunggah.
        $file = $this->request->getFile('gambar');
        $adaFile = $file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE;
        $ok = ! $adaFile || $this->validate([
            'gambar' => ['label' => 'Gambar', 'rules' => 'uploaded[gambar]|is_image[gambar]|max_size[gambar,2048]|ext_in[gambar,jpg,jpeg,png,webp]'],
        ]);
        if (! $ok) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $nama = trim((string) $this->request->getPost('nama'));
        $data = [
            'kategori_id' => (string) $this->request->getPost('kategori_id'),
            'nama'        => $nama,
            'slug'        => url_title($nama, '-', true),
            'deskripsi'   => trim((string) $this->request->getPost('deskripsi')),
            'harga'       => (string) $this->request->getPost('harga'),
            'stok'        => (string) $this->request->getPost('stok'),
        ];
        if ($lama !== null) {
            $data['id'] = $lama['id'];
        }

        $baru = null;
        if ($file !== null && $file->isValid() && ! $file->hasMoved()) {
            $baru = $file->getRandomName();
            $file->move(self::GAMBAR_DIR, $baru);
            $data['gambar'] = $baru;
        }

        if (! $this->model->save($data)) {
            if ($baru) {
                unlink(self::GAMBAR_DIR . $baru);
            }

            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        if ($baru && $lama && $lama['gambar'] && is_file(self::GAMBAR_DIR . $lama['gambar'])) {
            unlink(self::GAMBAR_DIR . $lama['gambar']);
        }

        return redirect()->to('/admin/produk')->with('success', "Produk {$nama} tersimpan.");
    }

    private function findOr404(int $id): array
    {
        return $this->model->find($id) ?? throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
    }
}
