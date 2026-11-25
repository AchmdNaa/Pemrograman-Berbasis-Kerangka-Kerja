<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class EmployeeSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $rows = [
            ['SE2023001', 'Rina Wulandari', 'Store Manager', 'Manajemen', 'rina@shopease.test', '081211110001', '2023-02-01', 9500000, 'aktif'],
            ['SE2023002', 'Bagus Prakoso', 'Staf Gudang', 'Gudang', 'bagus@shopease.test', '081211110002', '2023-03-15', 4800000, 'aktif'],
            ['SE2023003', 'Laila Rahmah', 'Customer Service', 'Layanan', 'laila@shopease.test', '081211110003', '2023-06-01', 5200000, 'aktif'],
            ['SE2024001', 'Yoga Pratama', 'Kurir', 'Pengiriman', 'yoga@shopease.test', '081211110004', '2024-01-08', 4300000, 'cuti'],
            ['SE2024002', 'Intan Permata', 'Admin Keuangan', 'Keuangan', 'intan@shopease.test', '081211110005', '2024-04-22', 6100000, 'aktif'],
            ['SE2024003', 'Dimas Saputra', 'Staf Gudang', 'Gudang', 'dimas@shopease.test', null, '2024-09-02', 4600000, 'aktif'],
            ['SE2025001', 'Nur Aisyah', 'Content Creator', 'Pemasaran', 'aisyah@shopease.test', '081211110007', '2025-02-10', 5500000, 'aktif'],
            ['SE2025002', 'Fikri Hidayat', 'Kurir', 'Pengiriman', 'fikri@shopease.test', '081211110008', '2025-07-01', 4300000, 'nonaktif'],
        ];
        foreach ($rows as [$nip, $name, $pos, $dept, $email, $phone, $hire, $salary, $status]) {
            $this->db->table('employees')->insert([
                'nip' => $nip, 'name' => $name, 'position' => $pos, 'department' => $dept, 'email' => $email,
                'phone' => $phone, 'hire_date' => $hire, 'salary' => $salary, 'status' => $status,
                'created_at' => $now, 'updated_at' => $now,
            ]);
        }
    }
}
