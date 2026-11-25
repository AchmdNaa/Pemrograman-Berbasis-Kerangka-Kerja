<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('user_id')) {
            if ($request->getMethod() === 'GET') {
                session()->set('intended_url', current_url(true)->__toString());
            }

            return redirect()->to(url_to('login'))->with('error', 'Silakan masuk terlebih dahulu.');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }
}
