<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

/**
 * Otorisasi berbasis peran. Pemakaian di Routes: ['filter' => 'role:admin,operator'].
 * Dipasang setelah filter "auth" sehingga session pasti sudah terisi.
 */
class RoleFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! in_array(session('user_role'), (array) $arguments, true)) {
            return redirect()->to(dashboard_url())->with('error', 'Anda tidak memiliki akses ke halaman tersebut.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
