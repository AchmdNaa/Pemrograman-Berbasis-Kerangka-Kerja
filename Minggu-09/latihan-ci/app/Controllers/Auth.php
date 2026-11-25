<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Login & logout pengelola (Latihan 1.7 — Mengatur Akses User).
 */
class Auth extends BaseController
{
    public function login(): string|RedirectResponse
    {
        if (session('isLoggedIn')) {
            return redirect()->to('/admin');
        }

        return view('auth/login', ['title' => 'Login']);
    }

    public function attempt(): RedirectResponse
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Username dan password wajib diisi.');
        }

        $user = (new UserModel())->findByUsername((string) $this->request->getPost('username'));

        if ($user === null || ! password_verify((string) $this->request->getPost('password'), $user['password_hash'])) {
            return redirect()->back()->withInput()->with('error', 'Username atau password salah.');
        }

        session()->regenerate(); // mencegah session fixation
        session()->set([
            'isLoggedIn' => true,
            'userId'     => $user['id'],
            'name'       => $user['name'],
            'role'       => $user['role'],
        ]);

        $tujuan = session('redirect_url') ?? site_url('admin');
        session()->remove('redirect_url');

        return redirect()->to($tujuan)->with('success', 'Selamat datang, ' . $user['name'] . '!');
    }

    public function logout(): RedirectResponse
    {
        session()->destroy();

        return redirect()->to('/login')->with('success', 'Anda telah logout.');
    }
}
