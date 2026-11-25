<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'email', 'phone', 'password_hash', 'role'];

    public const RULES = [
        'name'     => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'email'    => ['label' => 'Email', 'rules' => 'required|valid_email|is_unique[users.email]', 'errors' => ['is_unique' => 'Email sudah terdaftar.']],
        'phone'    => ['label' => 'Nomor HP', 'rules' => 'required|regex_match[/^(08|\+628)\d{7,11}$/]', 'errors' => ['regex_match' => 'Nomor HP harus diawali 08 atau +628.']],
        'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]', 'errors' => ['regex_match' => 'Password harus mengandung huruf dan angka.']],
        'password_confirm' => ['label' => 'Konfirmasi password', 'rules' => 'required|matches[password]', 'errors' => ['matches' => 'Konfirmasi password tidak sama.']],
    ];
}
