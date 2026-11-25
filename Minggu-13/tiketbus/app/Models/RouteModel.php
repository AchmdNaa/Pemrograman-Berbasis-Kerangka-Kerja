<?php

namespace App\Models;

use CodeIgniter\Model;

class RouteModel extends Model
{
    protected $table           = 'routes';
    protected $returnType      = 'array';
    protected $useTimestamps   = true;
    protected $allowedFields   = ['origin', 'destination', 'distance_km', 'duration_min'];
    protected $validationRules = [
        'origin'       => ['label' => 'Kota asal', 'rules' => 'required|min_length[3]|max_length[60]'],
        'destination'  => ['label' => 'Kota tujuan', 'rules' => 'required|min_length[3]|max_length[60]|differs[origin]', 'errors' => ['differs' => 'Kota tujuan harus berbeda dengan kota asal.']],
        'distance_km'  => ['label' => 'Jarak', 'rules' => 'required|is_natural_no_zero'],
        'duration_min' => ['label' => 'Durasi', 'rules' => 'required|is_natural_no_zero'],
    ];

    /** Daftar kota unik untuk dropdown pencarian. */
    public function cities(): array
    {
        $o = array_column($this->select('origin AS city')->distinct()->findAll(), 'city');
        $d = array_column((new self())->select('destination AS city')->distinct()->findAll(), 'city');
        $all = array_unique(array_merge($o, $d));
        sort($all);

        return $all;
    }
}
