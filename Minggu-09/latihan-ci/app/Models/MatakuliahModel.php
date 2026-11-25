<?php

namespace App\Models;

use CodeIgniter\Model;

class MatakuliahModel extends Model
{
    protected $table           = 'matakuliah';
    protected $allowedFields   = ['kode', 'nama', 'sks', 'semester'];
    protected $returnType      = 'array';
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'kode'     => ['label' => 'Kode', 'rules' => 'required|alpha_numeric|max_length[10]|is_unique[matakuliah.kode,id,{id}]'],
        'nama'     => ['label' => 'Nama mata kuliah', 'rules' => 'required|min_length[3]|max_length[100]'],
        'sks'      => ['label' => 'SKS', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[6]'],
        'semester' => ['label' => 'Semester', 'rules' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[8]'],
    ];
}
