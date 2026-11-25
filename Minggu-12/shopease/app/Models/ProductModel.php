<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductModel extends Model
{
    /** Pilihan pengurutan yang diizinkan (kunci dari query string → kolom). */
    public const SORTS = [
        'terbaru'  => ['products.created_at', 'DESC'],
        'terlaris' => ['products.sold', 'DESC'],
        'termurah' => ['products.price', 'ASC'],
        'termahal' => ['products.price', 'DESC'],
        'rating'   => ['products.rating', 'DESC'],
    ];

    protected $table         = 'products';
    protected $allowedFields = ['category_id', 'name', 'slug', 'description', 'image', 'price', 'old_price', 'stock', 'sold', 'rating', 'is_featured', 'is_active'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /** Validasi form Product Management (admin). {id} diganti otomatis saat update. */
    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'category_id' => ['label' => 'Kategori', 'rules' => 'required|is_not_unique[categories.id]'],
        'name'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[150]'],
        'slug'        => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[170]|is_unique[products.slug,id,{id}]', 'errors' => ['is_unique' => 'Slug/nama produk sudah dipakai produk lain.']],
        'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[5000]'],
        'price'       => ['label' => 'Harga', 'rules' => 'required|is_natural_no_zero|less_than_equal_to[1000000000]'],
        'old_price'   => ['label' => 'Harga coret', 'rules' => 'permit_empty|is_natural_no_zero|greater_than[{price}]', 'errors' => ['greater_than' => 'Harga coret harus lebih besar dari harga jual.']],
        'stock'       => ['label' => 'Stok', 'rules' => 'required|is_natural|less_than_equal_to[100000]'],
        'is_featured' => 'in_list[0,1]',
        'is_active'   => 'in_list[0,1]',
    ];

    /** Daftar produk untuk admin: semua produk (aktif & nonaktif) + nama kategori. */
    public function adminList(?string $keyword, ?int $categoryId, ?string $status): self
    {
        $this->select('products.*, categories.name AS category, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id');
        if ($keyword) {
            $this->like('products.name', $keyword);
        }
        if ($categoryId) {
            $this->where('products.category_id', $categoryId);
        }
        if ($status === 'aktif') {
            $this->where('products.is_active', 1);
        } elseif ($status === 'nonaktif') {
            $this->where('products.is_active', 0);
        } elseif ($status === 'habis') {
            $this->where('products.stock', 0);
        }

        return $this->orderBy('products.updated_at', 'DESC');
    }

    /** Query dasar: hanya produk aktif, sudah di-JOIN dengan kategori. */
    public function catalog(): self
    {
        return $this->select('products.*, categories.name AS category, categories.slug AS category_slug')
            ->join('categories', 'categories.id = products.category_id')
            ->where('products.is_active', 1);
    }

    /** Filter katalog dari query string (?q=&kategori=&urut=). */
    public function filter(?string $keyword, ?string $categorySlug, string $sort = 'terbaru'): self
    {
        $this->catalog();
        if ($keyword) {
            $this->groupStart()->like('products.name', $keyword)->orLike('products.description', $keyword)->groupEnd();
        }
        if ($categorySlug) {
            $this->where('categories.slug', $categorySlug);
        }
        [$column, $dir] = self::SORTS[$sort] ?? self::SORTS['terbaru'];

        return $this->orderBy($column, $dir);
    }

    public function featured(int $limit = 4): array
    {
        return $this->catalog()->where('products.is_featured', 1)->orderBy('products.sold', 'DESC')->findAll($limit);
    }

    public function latest(int $limit = 8): array
    {
        return $this->catalog()->orderBy('products.created_at', 'DESC')->findAll($limit);
    }

    public function bestSellers(int $limit = 4): array
    {
        return $this->catalog()->orderBy('products.sold', 'DESC')->findAll($limit);
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->catalog()->where('products.slug', $slug)->first();
    }

    public function related(array $product, int $limit = 4): array
    {
        return $this->catalog()
            ->where('products.category_id', $product['category_id'])
            ->where('products.id !=', $product['id'])
            ->orderBy('products.sold', 'DESC')
            ->findAll($limit);
    }
}
