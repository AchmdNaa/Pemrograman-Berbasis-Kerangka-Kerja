<?php

namespace App\Controllers\Operator;

use App\Controllers\BaseController;
use App\Models\ProductModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Dashboard operator toko (juga bisa dibuka admin): memantau dan memperbarui stok produk.
 */
class Dashboard extends BaseController
{
    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $model = new ProductModel();
        $model->catalog();
        if ($q !== '') {
            $model->like('products.name', $q);
        }

        return view('operator/dashboard', [
            'title'    => 'Dashboard Operator',
            'products' => $model->orderBy('products.stock')->paginate(10),
            'pager'    => $model->pager,
            'q'        => $q,
            'habis'    => (new ProductModel())->where('stock', 0)->countAllResults(),
            'menipis'  => (new ProductModel())->where('stock >', 0)->where('stock <', 10)->countAllResults(),
        ]);
    }

    public function updateStock(int $id): RedirectResponse
    {
        if (! $this->validate(['stock' => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than_equal_to[100000]']])) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new ProductModel();
        $product = $model->find($id);
        if ($product === null) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $model->update($id, ['stock' => (int) $this->request->getPost('stock')]);

        return redirect()->back()->with('success', "Stok {$product['name']} diperbarui menjadi {$this->request->getPost('stock')}.");
    }
}
