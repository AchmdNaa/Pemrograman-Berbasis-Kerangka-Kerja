<?php

namespace App\Controllers;

use App\Models\ContactMessageModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Halaman statis (Tentang) dan formulir Kontak.
 */
class Pages extends BaseController
{
    public function about(): string
    {
        return view('pages/about', ['title' => 'Tentang Kami']);
    }

    public function contact(): string
    {
        return view('pages/contact', ['title' => 'Hubungi Kami']);
    }

    /** POST /kontak — validasi di model, simpan, lalu redirect (pola Post/Redirect/Get). */
    public function sendMessage(): RedirectResponse
    {
        $model = new ContactMessageModel();
        $data = [
            'name'    => trim((string) $this->request->getPost('name')),
            'email'   => trim((string) $this->request->getPost('email')),
            'subject' => trim((string) $this->request->getPost('subject')),
            'message' => trim((string) $this->request->getPost('message')),
        ];

        if (! $model->insert($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/kontak')->with('success', "Terima kasih, {$data['name']}! Pesan Anda sudah kami terima.");
    }
}
