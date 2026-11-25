<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Registrasi, login, dan logout.
 */
class Auth extends BaseController
{
    /** Maksimal percobaan login gagal per IP+email dalam 5 menit. */
    private const MAX_ATTEMPTS = 5;

    // ------------------------------------------------------------- Registrasi

    public function registerForm(): string
    {
        return view('auth/register', ['title' => 'Daftar Akun']);
    }

    public function register(): RedirectResponse
    {
        $post = $this->request->getPost(['name', 'email', 'phone', 'password', 'password_confirm', 'terms']);
        $post['email'] = strtolower(trim((string) $post['email']));
        $post['name'] = trim((string) $post['name']);

        if (! $this->validateData($post, UserModel::REGISTER_RULES)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        // Hanya field yang aman yang diteruskan. Role selalu "customer"; pengguna tidak bisa mendaftar sebagai admin.
        $id = $model->insert([
            'name'      => $post['name'],
            'email'     => $post['email'],
            'phone'     => trim((string) $post['phone']) ?: null,
            'password'  => $post['password'], // di-hash oleh UserModel::hashPassword()
            'role'      => 'customer',
            'is_active' => 1,
        ]);

        // Langsung login setelah berhasil daftar.
        $this->startSession($model->find($id));

        return redirect()->to(url_to('account'))->with('success', "Selamat datang di ShopEase, {$post['name']}! Akun Anda berhasil dibuat.");
    }

    // ------------------------------------------------------------- Login

    public function loginForm(): string
    {
        return view('auth/login', ['title' => 'Masuk']);
    }

    public function login(): RedirectResponse
    {
        $rules = [
            'email'    => ['label' => 'Email', 'rules' => 'required|valid_email'],
            'password' => ['label' => 'Password', 'rules' => 'required'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $email = strtolower(trim((string) $this->request->getPost('email')));
        $password = (string) $this->request->getPost('password');

        // Pembatasan percobaan login (brute force) memakai Throttler bawaan CodeIgniter.
        $throttleKey = 'login-' . md5($this->request->getIPAddress() . '|' . $email);
        $throttler = service('throttler');
        if ($throttler->check($throttleKey, self::MAX_ATTEMPTS, MINUTE * 5) === false) {
            $wait = max(1, (int) ceil($throttler->getTokenTime() / 60));

            return redirect()->back()->withInput()
                ->with('error', "Terlalu banyak percobaan login gagal. Coba lagi dalam {$wait} menit.");
        }

        $model = new UserModel();
        $user = $model->findByEmail($email);

        // Pesan error sengaja dibuat sama untuk email tidak terdaftar maupun password salah,
        // agar penyerang tidak bisa menebak email mana yang terdaftar.
        if ($user === null || ! password_verify($password, $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        if (! (int) $user['is_active']) {
            return redirect()->back()->withInput()->with('error', 'Akun Anda dinonaktifkan. Hubungi admin ShopEase.');
        }

        // Perbarui hash bila algoritma default PHP berubah.
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $model->update($user['id'], ['password' => $password]);
        }

        $throttler->remove($throttleKey);
        $model->update($user['id'], ['last_login_at' => date('Y-m-d H:i:s')]);
        $this->startSession($user);

        $intended = session('intended_url');
        session()->remove('intended_url');

        return redirect()->to($intended ?: dashboard_url($user['role']))
            ->with('success', 'Halo, ' . $user['name'] . '! Anda berhasil masuk.');
    }

    // ------------------------------------------------------------- Logout

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(url_to('login'))->with('success', 'Anda telah keluar. Sampai jumpa lagi!');
    }

    /**
     * Menyimpan identitas pengguna di session.
     * regenerate(true) membuat ID session baru untuk mencegah session fixation.
     */
    private function startSession(array $user): void
    {
        session()->regenerate(true);
        session()->set([
            'user_id'    => (int) $user['id'],
            'user_name'  => $user['name'],
            'user_email' => $user['email'],
            'user_role'  => $user['role'],
        ]);
    }
}
