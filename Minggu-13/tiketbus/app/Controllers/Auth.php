<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

class Auth extends BaseController
{
    public function loginForm(): string
    {
        return view('auth/login', ['title' => 'Masuk']);
    }

    public function login(): RedirectResponse
    {
        if (! $this->validate(['email' => 'required|valid_email', 'password' => 'required'])) {
            return redirect()->back()->withInput()->with('error', 'Email dan password wajib diisi dengan benar.');
        }
        $email = strtolower(trim((string) $this->request->getPost('email')));
        $key = 'login-' . md5($this->request->getIPAddress() . $email);
        if (service('throttler')->check($key, 5, MINUTE * 5) === false) {
            return redirect()->back()->withInput()->with('error', 'Terlalu banyak percobaan. Coba lagi beberapa menit lagi.');
        }

        $user = (new UserModel())->where('email', $email)->first();
        if (! $user || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Email atau password salah.');
        }

        service('throttler')->remove($key);
        $this->startSession($user);
        $to = session('intended_url') ?: home_url();
        session()->remove('intended_url');

        return redirect()->to($to)->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    public function registerForm(): string
    {
        return view('auth/register', ['title' => 'Daftar']);
    }

    public function register(): RedirectResponse
    {
        $data = $this->request->getPost(['name', 'email', 'phone', 'password', 'password_confirm']);
        $data['email'] = strtolower(trim((string) $data['email']));
        if (! $this->validateData($data, UserModel::RULES)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $id = $model->insert([
            'name' => trim($data['name']), 'email' => $data['email'], 'phone' => trim($data['phone']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT), 'role' => 'pelanggan',
        ]);
        $this->startSession($model->find($id));
        $to = session('intended_url') ?: url_to('home');
        session()->remove('intended_url');

        return redirect()->to($to)->with('success', 'Akun berhasil dibuat. Silakan cari dan pesan tiket Anda.');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to(url_to('home'))->with('success', 'Anda telah keluar.');
    }

    private function startSession(array $u): void
    {
        session()->regenerate(true);
        session()->set(['user_id' => (int) $u['id'], 'user_name' => $u['name'], 'user_email' => $u['email'], 'user_role' => $u['role']]);
    }
}
