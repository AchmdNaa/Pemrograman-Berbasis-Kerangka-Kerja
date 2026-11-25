<?php

namespace App\Controllers;

/**
 * Halaman 404 dengan tampilan ShopEase (dipasang lewat set404Override di Routes).
 */
class Errors extends BaseController
{
    public function notFound(?string $message = null): string
    {
        $this->response->setStatusCode(404);

        return view('errors/not_found', [
            'title'   => 'Halaman Tidak Ditemukan',
            'message' => $message,
        ]);
    }
}
