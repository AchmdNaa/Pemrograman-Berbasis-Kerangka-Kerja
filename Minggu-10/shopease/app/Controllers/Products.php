<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Katalog, detail produk, dan halaman kategori.
 */
class Products extends BaseController
{
    /** GET /produk?q=&kategori=&urut= */
    public function index(): string
    {
        return $this->listing(
            keyword: trim((string) $this->request->getGet('q')),
            category: null,
            heading: 'Semua Produk'
        );
    }

    /** GET /kategori/{slug} — parameter route (:segment) masuk sebagai argumen method. */
    public function category(string $slug): string
    {
        $category = (new CategoryModel())->findBySlug($slug)
            ?? throw PageNotFoundException::forPageNotFound("Kategori \"{$slug}\" tidak ditemukan.");

        return $this->listing(
            keyword: trim((string) $this->request->getGet('q')),
            category: $category,
            heading: $category['name']
        );
    }

    /** GET /produk/{slug} */
    public function show(string $slug): string
    {
        $model = new ProductModel();
        $product = $model->findBySlug($slug)
            ?? throw PageNotFoundException::forPageNotFound('Produk tidak ditemukan.');

        return view('products/show', [
            'title'   => $product['name'],
            'product' => $product,
            'related' => (new ProductModel())->related($product),
        ]);
    }

    private function listing(string $keyword, ?array $category, string $heading): string
    {
        $sort = (string) ($this->request->getGet('urut') ?? 'terbaru');
        if (! array_key_exists($sort, ProductModel::SORTS)) {
            $sort = 'terbaru';
        }

        $model = new ProductModel();
        $products = $model->filter($keyword, $category['slug'] ?? null, $sort)->paginate(8);

        return view('products/index', [
            'title'      => $heading,
            'heading'    => $heading,
            'products'   => $products,
            'pager'      => $model->pager,
            'total'      => $model->pager->getTotal(),
            'categories' => (new CategoryModel())->withProductCount(),
            'active'     => $category,
            'keyword'    => $keyword,
            'sort'       => $sort,
        ]);
    }
}
