<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananItemModel extends Model
{
    protected $table         = 'pesanan_item';
    protected $allowedFields = ['pesanan_id', 'produk_id', 'nama_produk', 'harga', 'qty', 'subtotal'];
    protected $returnType    = 'array';
}
