<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD kategori produk dalam satu halaman.
 */
class Categories extends BaseController
{
    public function index(): string
    {
        $model = new CategoryModel();
        $editId = (int) $this->request->getGet('edit');

        return view('admin/categories/index', [
            'title'      => 'Kategori Produk',
            'categories' => $model->select('categories.*, COUNT(products.id) AS total')
                ->join('products', 'products.category_id = categories.id', 'left')
                ->groupBy('categories.id')->orderBy('categories.name')->findAll(),
            'edit'       => $editId ? (new CategoryModel())->find($editId) : null,
        ]);
    }

    public function save(): RedirectResponse
    {
        $id = (int) $this->request->getPost('id');
        $name = trim((string) $this->request->getPost('name'));
        $data = [
            'name'        => $name,
            'slug'        => url_title($name, '-', true),
            'icon'        => trim((string) $this->request->getPost('icon')) ?: 'bi-tag',
            'description' => trim((string) $this->request->getPost('description')),
        ];
        if ($id > 0) {
            $data['id'] = $id;
        }

        $model = new CategoryModel();
        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(url_to('admin.categories'))->with('success', "Kategori \"{$name}\" tersimpan.");
    }

    public function delete(int $id): RedirectResponse
    {
        $used = (new ProductModel())->where('category_id', $id)->countAllResults();
        if ($used > 0) {
            return redirect()->back()->with('error', "Kategori masih dipakai {$used} produk. Pindahkan produknya terlebih dahulu.");
        }
        (new CategoryModel())->delete($id);

        return redirect()->to(url_to('admin.categories'))->with('success', 'Kategori dihapus.');
    }
}
