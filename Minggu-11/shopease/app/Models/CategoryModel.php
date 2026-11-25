<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $allowedFields = ['name', 'slug', 'icon', 'description'];
    protected $returnType    = 'array';

    /** Kategori beserta jumlah produk aktif di dalamnya. */
    public function withProductCount(): array
    {
        return $this->select('categories.*, COUNT(products.id) AS total')
            ->join('products', 'products.category_id = categories.id AND products.is_active = 1', 'left')
            ->groupBy('categories.id')
            ->orderBy('categories.name')
            ->findAll();
    }

    public function findBySlug(string $slug): ?array
    {
        return $this->where('slug', $slug)->first();
    }
}
