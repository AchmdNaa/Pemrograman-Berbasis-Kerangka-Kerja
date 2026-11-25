<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $users = [
            ['Admin ShopEase', 'admin@shopease.test', '081200000001', 'Admin#123', 'admin'],
            ['Operator Gudang', 'operator@shopease.test', '081200000002', 'Operator#123', 'operator'],
            ['Achmad Najwa', 'najwa@student.its.ac.id', '081234567801', 'Customer#123', 'customer'],
            ['Siti Rahmawati', 'siti@example.com', '081298765432', 'Customer#123', 'customer'],
        ];

        foreach ($users as [$name, $email, $phone, $password, $role]) {
            $this->db->table('users')->insert([
                'name'          => $name,
                'email'         => $email,
                'phone'         => $phone,
                'password_hash' => password_hash($password, PASSWORD_DEFAULT),
                'role'          => $role,
                'is_active'     => 1,
                'created_at'    => $now,
                'updated_at'    => $now,
            ]);
        }
    }
}
