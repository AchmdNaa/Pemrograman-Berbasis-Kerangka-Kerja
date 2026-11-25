<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Latihan CRUD: Model untuk menyimpan data karyawan.
 *
 * Fitur Model CodeIgniter 4 yang dipakai:
 * - $allowedFields  : hanya kolom ini yang boleh diisi (mencegah mass assignment)
 * - $useTimestamps  : created_at / updated_at terisi otomatis
 * - $useSoftDeletes : delete() hanya mengisi deleted_at, data bisa dipulihkan
 * - $validationRules: validasi otomatis saat insert/update/save
 */
class EmployeeModel extends Model
{
    public const DEPARTMENTS = ['Manajemen', 'Gudang', 'Layanan', 'Pengiriman', 'Keuangan', 'Pemasaran'];
    public const STATUSES    = ['aktif', 'cuti', 'nonaktif'];

    protected $table          = 'employees';
    protected $returnType     = 'array';
    protected $useTimestamps  = true;
    protected $useSoftDeletes = true;
    protected $allowedFields  = ['nip', 'name', 'position', 'department', 'email', 'phone', 'hire_date', 'salary', 'status', 'photo'];

    protected $validationRules = [
        'id'         => 'permit_empty|is_natural_no_zero',
        'nip'        => ['label' => 'NIP', 'rules' => 'required|regex_match[/^SE\d{7}$/]|is_unique[employees.nip,id,{id}]', 'errors' => ['regex_match' => 'NIP harus berformat SE + 7 angka, contoh SE2026001.', 'is_unique' => 'NIP sudah dipakai karyawan lain.']],
        'name'       => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'position'   => ['label' => 'Jabatan', 'rules' => 'required|min_length[3]|max_length[60]'],
        'department' => ['label' => 'Departemen', 'rules' => 'required|in_list[Manajemen,Gudang,Layanan,Pengiriman,Keuangan,Pemasaran]'],
        'email'      => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[120]|is_unique[employees.email,id,{id}]', 'errors' => ['is_unique' => 'Email sudah dipakai karyawan lain.']],
        'phone'      => ['label' => 'Nomor HP', 'rules' => 'permit_empty|regex_match[/^(08|\+628)\d{7,11}$/]', 'errors' => ['regex_match' => 'Nomor HP harus diawali 08 atau +628.']],
        'hire_date'  => ['label' => 'Tanggal masuk', 'rules' => 'required|valid_date[Y-m-d]'],
        'salary'     => ['label' => 'Gaji', 'rules' => 'required|is_natural_no_zero|greater_than_equal_to[1000000]|less_than_equal_to[100000000]'],
        'status'     => ['label' => 'Status', 'rules' => 'required|in_list[aktif,cuti,nonaktif]'],
    ];

    public function search(?string $keyword, ?string $department, ?string $status): self
    {
        if ($keyword) {
            $this->groupStart()->like('name', $keyword)->orLike('nip', $keyword)->orLike('position', $keyword)->groupEnd();
        }
        if ($department) {
            $this->where('department', $department);
        }
        if ($status) {
            $this->where('status', $status);
        }

        return $this->orderBy('nip');
    }

    /** Ringkasan untuk kartu statistik. */
    public function summary(): array
    {
        $rows = $this->select('status, COUNT(*) AS total, SUM(salary) AS payroll')->groupBy('status')->findAll();
        $out = ['total' => 0, 'aktif' => 0, 'cuti' => 0, 'nonaktif' => 0, 'payroll' => 0];
        foreach ($rows as $r) {
            $out[$r['status']] = (int) $r['total'];
            $out['total'] += (int) $r['total'];
            if ($r['status'] !== 'nonaktif') {
                $out['payroll'] += (int) $r['payroll'];
            }
        }

        return $out;
    }
}
