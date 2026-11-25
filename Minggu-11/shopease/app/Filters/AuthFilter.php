<?php

namespace App\Filters;

use App\Models\UserModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Halaman yang membutuhkan login.
 * Juga memastikan akun masih aktif: bila admin menonaktifkan akun, sesi pengguna tersebut langsung berakhir.
 */
class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $userId = session('user_id');

        if (! $userId) {
            // Simpan tujuan agar setelah login pengguna dikembalikan ke halaman ini.
            if ($request->getMethod() === 'GET') {
                session()->set('intended_url', current_url());
            }

            return redirect()->to(url_to('login'))->with('error', 'Silakan masuk terlebih dahulu untuk membuka halaman tersebut.');
        }

        $user = (new UserModel())->find($userId);
        if ($user === null || ! (int) $user['is_active']) {
            session()->destroy();

            return redirect()->to(url_to('login'))->with('error', 'Akun Anda tidak aktif. Hubungi admin ShopEase.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
