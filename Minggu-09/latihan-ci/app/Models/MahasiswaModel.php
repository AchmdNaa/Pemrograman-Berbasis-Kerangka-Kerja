<?php

namespace App\Models;

use CodeIgniter\Model;

class MahasiswaModel extends Model
{
    public const PRODI = [
        'Teknik Informatika',
        'Sistem Informasi',
        'Teknologi Informasi',
        'Rekayasa Perangkat Lunak',
        'Sains Data',
    ];

    protected $table         = 'mahasiswa';
    protected $allowedFields = ['nim', 'nama', 'prodi', 'angkatan', 'email', 'foto'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    /** Aturan validasi; {id} diganti otomatis saat update agar NIM sendiri tidak dianggap duplikat. */
    protected $validationRules = [
        'id'       => 'permit_empty|is_natural_no_zero',
        'nim'      => ['label' => 'NIM', 'rules' => 'required|regex_match[/^\d{10}$/]|is_unique[mahasiswa.nim,id,{id}]'],
        'nama'     => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'prodi'    => ['label' => 'Program studi', 'rules' => 'required|in_list[Teknik Informatika,Sistem Informasi,Teknologi Informasi,Rekayasa Perangkat Lunak,Sains Data]'],
        'angkatan' => ['label' => 'Angkatan', 'rules' => 'required|integer|greater_than_equal_to[2000]|less_than_equal_to[2100]'],
        'email'    => ['label' => 'Email', 'rules' => 'permit_empty|valid_email|max_length[100]'],
    ];
    protected $validationMessages = [
        'nim' => ['regex_match' => 'NIM harus terdiri dari 10 digit angka.'],
    ];

    /** Pencarian berdasarkan NIM / nama dan filter prodi. Mengembalikan builder agar bisa dipaginasi. */
    public function search(?string $keyword, ?string $prodi): self
    {
        if ($keyword) {
            $this->groupStart()->like('nim', $keyword)->orLike('nama', $keyword)->groupEnd();
        }
        if ($prodi) {
            $this->where('prodi', $prodi);
        }

        return $this->orderBy('nim', 'ASC');
    }
}
