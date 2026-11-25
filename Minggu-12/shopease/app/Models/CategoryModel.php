<?php

namespace App\Models;

use CodeIgniter\Model;

class CategoryModel extends Model
{
    protected $table         = 'categories';
    protected $allowedFields = ['name', 'slug', 'icon', 'description'];
    protected $returnType    = 'array';

    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'name'        => ['label' => 'Nama kategori', 'rules' => 'required|min_length[3]|max_length[60]'],
        'slug'        => ['label' => 'Slug', 'rules' => 'required|alpha_dash|max_length[80]|is_unique[categories.slug,id,{id}]', 'errors' => ['is_unique' => 'Kategori dengan nama/slug ini sudah ada.']],
        'icon'        => ['label' => 'Ikon', 'rules' => 'required|regex_match[/^bi-[a-z0-9-]+$/]', 'errors' => ['regex_match' => 'Ikon harus nama Bootstrap Icons, contoh: bi-laptop.']],
        'description' => ['label' => 'Deskripsi', 'rules' => 'permit_empty|max_length[255]'],
    ];

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
