<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

/**
 * CRUD produk (khusus admin).
 */
class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::with('category')
            ->search($request->query('q'))
            ->when($request->query('kategori'), fn ($q, $c) => $q->where('category_id', $c))
            ->when($request->query('stok') === 'menipis', fn ($q) => $q->lowStock())
            ->orderBy('name')->paginate(12)->withQueryString();

        return view('products.index', ['products' => $products, 'categories' => Category::orderBy('name')->get()]);
    }

    public function create(): View
    {
        return view('products.form', ['product' => new Product(['unit' => 'pcs', 'min_stock' => 5, 'is_active' => true]), 'categories' => Category::orderBy('name')->get()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $product = Product::create($this->validated($request));

        return redirect()->route('products.index')->with('success', "Produk {$product->name} ditambahkan.");
    }

    public function edit(Product $product): View
    {
        return view('products.form', ['product' => $product, 'categories' => Category::orderBy('name')->get()]);
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $product->update($this->validated($request, $product));

        return redirect()->route('products.index')->with('success', "Produk {$product->name} diperbarui.");
    }

    public function destroy(Product $product): RedirectResponse
    {
        // Produk yang pernah terjual tetap tercatat di riwayat (product_id menjadi NULL, nama & harga tersalin).
        $product->delete();

        return redirect()->route('products.index')->with('success', "Produk {$product->name} dihapus.");
    }

    private function validated(Request $request, ?Product $product = null): array
    {
        // Kode barang diseragamkan huruf besar SEBELUM validasi, agar cek unique tidak bisa dilewati dengan huruf kecil.
        $request->merge(['sku' => strtoupper(trim((string) $request->input('sku')))]);

        $data = $request->validate([
            'sku'         => ['required', 'alpha_dash', 'max:20', Rule::unique('products', 'sku')->ignore($product?->id)],
            'name'        => ['required', 'string', 'min:3', 'max:120'],
            'category_id' => ['required', 'exists:categories,id'],
            'cost_price'  => ['required', 'integer', 'min:0'],
            'price'       => ['required', 'integer', 'min:1', 'gte:cost_price'],
            'stock'       => ['required', 'integer', 'min:0', 'max:100000'],
            'min_stock'   => ['required', 'integer', 'min:0', 'max:1000'],
            'unit'        => ['required', Rule::in(['pcs', 'botol', 'bungkus', 'kg', 'liter', 'dus', 'sachet'])],
        ], [
            'price.gte' => 'Harga jual tidak boleh lebih kecil dari harga beli.',
        ], [
            'sku' => 'kode barang', 'name' => 'nama produk', 'category_id' => 'kategori', 'cost_price' => 'harga beli',
            'price' => 'harga jual', 'stock' => 'stok', 'min_stock' => 'stok minimum', 'unit' => 'satuan',
        ]);
        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
