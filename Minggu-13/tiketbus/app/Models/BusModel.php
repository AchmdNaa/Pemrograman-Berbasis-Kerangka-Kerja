<?php

namespace App\Models;

use CodeIgniter\Model;

class BusModel extends Model
{
    public const CLASSES = ['Ekonomi', 'Eksekutif', 'Sleeper'];

    protected $table           = 'buses';
    protected $returnType      = 'array';
    protected $useTimestamps   = true;
    protected $allowedFields   = ['code', 'operator', 'class', 'seat_rows', 'seat_cols', 'facilities'];
    protected $validationRules = [
        'id'        => 'permit_empty|is_natural_no_zero',
        'code'      => ['label' => 'Nomor polisi', 'rules' => 'required|max_length[15]|is_unique[buses.code,id,{id}]'],
        'operator'  => ['label' => 'Nama PO', 'rules' => 'required|min_length[3]|max_length[60]'],
        'class'     => ['label' => 'Kelas', 'rules' => 'required|in_list[Ekonomi,Eksekutif,Sleeper]'],
        'seat_rows' => ['label' => 'Jumlah baris kursi', 'rules' => 'required|integer|greater_than_equal_to[2]|less_than_equal_to[15]'],
        'seat_cols' => ['label' => 'Kursi per baris', 'rules' => 'required|in_list[3,4]'],
    ];
}
