<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table         = 'users';
    protected $allowedFields = ['name', 'username', 'password_hash', 'role'];
    protected $useTimestamps = true;
    protected $returnType    = 'array';

    public function findByUsername(string $username): ?array
    {
        return $this->where('username', $username)->first();
    }
}
