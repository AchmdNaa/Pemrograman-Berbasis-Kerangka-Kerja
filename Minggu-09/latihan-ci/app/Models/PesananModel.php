<?php

namespace App\Models;

use CodeIgniter\Model;

class PesananModel extends Model
{
    public const STATUS = ['baru', 'diproses', 'dikirim', 'selesai', 'batal'];

    protected $table         = 'pesanan';
    protected $allowedFields = ['kode', 'nama_pembeli', 'telepon', 'alamat', 'total', 'status'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';
}
