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
    protected $allowedFields = ['category_id', 'name', 'slug', 'description', 'price', 'old_price', 'stock', 'sold', 'rating', 'is_featured', 'is_active'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

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
