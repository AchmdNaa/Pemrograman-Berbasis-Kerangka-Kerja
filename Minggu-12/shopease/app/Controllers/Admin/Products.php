<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Product Management ShopEase: CRUD produk lengkap dengan upload gambar.
 */
class Products extends BaseController
{
    private ProductModel $model;
    private ImageUploader $uploader;

    public function __construct()
    {
        $this->model = new ProductModel();
        $this->uploader = new ImageUploader('products');
    }

    /** READ — daftar produk dengan pencarian & filter. */
    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $cat = (int) $this->request->getGet('kategori') ?: null;
        $status = (string) $this->request->getGet('status');

        return view('admin/products/index', [
            'title'      => 'Manajemen Produk',
            'products'   => $this->model->adminList($q, $cat, $status)->paginate(10),
            'pager'      => $this->model->pager,
            'categories' => (new CategoryModel())->orderBy('name')->findAll(),
            'q'          => $q,
            'cat'        => $cat,
            'status'     => $status,
        ]);
    }

    /** CREATE — form tambah. */
    public function new(): string
    {
        return view('admin/products/form', [
            'title'      => 'Tambah Produk',
            'product'    => null,
            'categories' => (new CategoryModel())->orderBy('name')->findAll(),
        ]);
    }

    /** CREATE — simpan. */
    public function create(): RedirectResponse
    {
        return $this->persist(null);
    }

    /** UPDATE — form edit. */
    public function edit(int $id): string
    {
        return view('admin/products/form', [
            'title'      => 'Edit Produk',
            'product'    => $this->findOr404($id),
            'categories' => (new CategoryModel())->orderBy('name')->findAll(),
        ]);
    }

    /** UPDATE — simpan perubahan. */
    public function update(int $id): RedirectResponse
    {
        return $this->persist($this->findOr404($id));
    }

    /** DELETE */
    public function delete(int $id): RedirectResponse
    {
        $product = $this->findOr404($id);
        $this->model->delete($id);
        $this->uploader->delete($product['image']);

        return redirect()->to(url_to('admin.products'))->with('success', "Produk \"{$product['name']}\" dihapus.");
    }

    /** Aktif/nonaktifkan produk tanpa menghapusnya (produk nonaktif tidak tampil di katalog). */
    public function toggle(int $id): RedirectResponse
    {
        $product = $this->findOr404($id);
        $this->model->update($id, ['is_active' => $product['is_active'] ? 0 : 1]);

        return redirect()->back()->with('success', "Produk \"{$product['name']}\" " . ($product['is_active'] ? 'dinonaktifkan.' : 'diaktifkan.'));
    }

    // -----------------------------------------------------------------

    private function persist(?array $old): RedirectResponse
    {
        $name = trim((string) $this->request->getPost('name'));
        $slugInput = trim((string) $this->request->getPost('slug'));
        $data = [
            'category_id' => (string) $this->request->getPost('category_id'),
            'name'        => $name,
            'slug'        => url_title($slugInput !== '' ? $slugInput : $name, '-', true),
            'description' => trim((string) $this->request->getPost('description')),
            'price'       => (string) $this->request->getPost('price'),
            'old_price'   => (string) $this->request->getPost('old_price') ?: null,
            'stock'       => (string) $this->request->getPost('stock'),
            'is_featured' => $this->request->getPost('is_featured') ? 1 : 0,
            'is_active'   => $this->request->getPost('is_active') ? 1 : 0,
        ];
        if ($old !== null) {
            $data['id'] = $old['id'];
        }

        $image = $this->uploader->store($this->request, 'image');
        if ($image === false) {
            return redirect()->back()->withInput()->with('errors', ['image' => $this->uploader->error()]);
        }
        if ($image !== null) {
            $data['image'] = $image;
        }
        if ($old !== null && $this->request->getPost('remove_image') && $image === null) {
            $data['image'] = null;
        }

        if (! $this->model->save($data)) {
            $this->uploader->delete($image ?: null);

            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        // Hapus file lama bila diganti atau dihapus.
        if ($old !== null && array_key_exists('image', $data) && $old['image'] !== $data['image']) {
            $this->uploader->delete($old['image']);
        }

        return redirect()->to(url_to('admin.products'))->with('success', "Produk \"{$name}\" berhasil " . ($old ? 'diperbarui.' : 'ditambahkan.'));
    }

    private function findOr404(int $id): array
    {
        return $this->model->find($id) ?? throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');
    }
}
