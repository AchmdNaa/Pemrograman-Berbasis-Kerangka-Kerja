<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model pengguna.
 *
 * - Password tidak pernah disimpan apa adanya: event beforeInsert/beforeUpdate
 *   mengubah field "password" menjadi "password_hash" dengan password_hash().
 * - Kolom role & is_active tidak bisa diisi dari form registrasi karena controller
 *   hanya meneruskan field yang diizinkan (lihat Auth::register).
 */
class UserModel extends Model
{
    public const ROLES = ['customer', 'operator', 'admin'];

    protected $table         = 'users';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['name', 'email', 'phone', 'password', 'password_hash', 'role', 'is_active', 'last_login_at'];

    protected $beforeInsert = ['hashPassword'];
    protected $beforeUpdate = ['hashPassword'];

    /** Aturan validasi registrasi (dipakai controller lewat $this->validate). */
    public const REGISTER_RULES = [
        'name' => [
            'label' => 'Nama lengkap',
            'rules' => 'required|min_length[3]|max_length[100]|regex_match[/^[\p{L} .\'-]+$/u]',
            'errors' => ['regex_match' => 'Nama hanya boleh berisi huruf, spasi, titik, atau tanda petik.'],
        ],
        'email' => [
            'label' => 'Email',
            'rules' => 'required|valid_email|max_length[120]|is_unique[users.email]',
            'errors' => ['is_unique' => 'Email ini sudah terdaftar. Silakan login.'],
        ],
        'phone' => [
            'label' => 'Nomor HP',
            'rules' => 'permit_empty|regex_match[/^(08|\+628)\d{7,11}$/]',
            'errors' => ['regex_match' => 'Nomor HP harus diawali 08 atau +628 dan berisi 10–14 digit.'],
        ],
        'password' => [
            'label' => 'Password',
            'rules' => 'required|min_length[8]|max_length[72]|regex_match[/^(?=.*[A-Za-z])(?=.*\d).+$/]',
            'errors' => ['regex_match' => 'Password harus mengandung huruf dan angka.'],
        ],
        'password_confirm' => [
            'label' => 'Konfirmasi password',
            'rules' => 'required|matches[password]',
            'errors' => ['matches' => 'Konfirmasi password tidak sama.'],
        ],
        'terms' => [
            'label' => 'Syarat & ketentuan',
            'rules' => 'required',
            'errors' => ['required' => 'Anda harus menyetujui syarat & ketentuan.'],
        ],
    ];

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['password'])) {
            $data['data']['password_hash'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
            unset($data['data']['password']);
        }

        return $data;
    }

    public function findByEmail(string $email): ?array
    {
        return $this->where('email', strtolower(trim($email)))->first();
    }

    /** Statistik jumlah pengguna per peran untuk dashboard admin. */
    public function countByRole(): array
    {
        $rows = $this->select('role, COUNT(*) AS total')->groupBy('role')->findAll();

        return array_column($rows, 'total', 'role') + array_fill_keys(self::ROLES, 0);
    }
}
