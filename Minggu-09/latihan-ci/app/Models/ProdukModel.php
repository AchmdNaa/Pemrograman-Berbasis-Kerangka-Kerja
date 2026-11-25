<?php

namespace App\Models;

use CodeIgniter\Model;

class ProdukModel extends Model
{
    protected $table           = 'produk';
    protected $allowedFields   = ['kategori_id', 'nama', 'slug', 'deskripsi', 'harga', 'stok', 'gambar'];
    protected $useTimestamps   = true;
    protected $returnType      = 'array';
    protected $validationRules = [
        'id'          => 'permit_empty|is_natural_no_zero',
        'kategori_id' => ['label' => 'Kategori', 'rules' => 'required|is_not_unique[kategori.id]'],
        'nama'        => ['label' => 'Nama produk', 'rules' => 'required|min_length[3]|max_length[120]'],
        'slug'        => ['label' => 'Slug', 'rules' => 'required|is_unique[produk.slug,id,{id}]'],
        'harga'       => ['label' => 'Harga', 'rules' => 'required|is_natural_no_zero'],
        'stok'        => ['label' => 'Stok', 'rules' => 'required|is_natural'],
    ];
    protected $validationMessages = [
        'slug' => ['is_unique' => 'Nama produk sudah dipakai produk lain.'],
    ];

    /** Produk + nama kategori, dengan pencarian dan filter kategori (slug). */
    public function katalog(?string $keyword = null, ?string $kategoriSlug = null): self
    {
        $this->select('produk.*, kategori.nama AS kategori, kategori.slug AS kategori_slug')
            ->join('kategori', 'kategori.id = produk.kategori_id');

        if ($keyword) {
            $this->like('produk.nama', $keyword);
        }
        if ($kategoriSlug) {
            $this->where('kategori.slug', $kategoriSlug);
        }

        return $this->orderBy('produk.nama');
    }
}
