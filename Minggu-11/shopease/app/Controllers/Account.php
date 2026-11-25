<?php

namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Area akun untuk semua pengguna yang login (dashboard customer, profil, ganti password).
 */
class Account extends BaseController
{
    public function index(): string
    {
        return view('account/index', [
            'title' => 'Akun Saya',
            'user'  => (new UserModel())->find(auth_user('id')),
        ]);
    }

    public function profile(): string
    {
        return view('account/profile', [
            'title' => 'Profil & Keamanan',
            'user'  => (new UserModel())->find(auth_user('id')),
        ]);
    }

    public function updateProfile(): RedirectResponse
    {
        $id = (int) auth_user('id');
        $rules = [
            'name'  => UserModel::REGISTER_RULES['name'],
            'email' => [
                'label'  => 'Email',
                'rules'  => "required|valid_email|max_length[120]|is_unique[users.email,id,{$id}]",
                'errors' => ['is_unique' => 'Email ini sudah dipakai akun lain.'],
            ],
            'phone' => UserModel::REGISTER_RULES['phone'],
        ];
        $data = [
            'name'  => trim((string) $this->request->getPost('name')),
            'email' => strtolower(trim((string) $this->request->getPost('email'))),
            'phone' => trim((string) $this->request->getPost('phone')),
        ];

        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new UserModel())->update($id, ['name' => $data['name'], 'email' => $data['email'], 'phone' => $data['phone'] ?: null]);
        session()->set(['user_name' => $data['name'], 'user_email' => $data['email']]);

        return redirect()->to(url_to('account.profile'))->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword(): RedirectResponse
    {
        $model = new UserModel();
        $user = $model->find(auth_user('id'));

        $data = $this->request->getPost(['current_password', 'password', 'password_confirm']);
        $rules = [
            'current_password' => ['label' => 'Password lama', 'rules' => 'required'],
            'password'         => UserModel::REGISTER_RULES['password'],
            'password_confirm' => UserModel::REGISTER_RULES['password_confirm'],
        ];
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
        if (! password_verify((string) $data['current_password'], $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password lama tidak sesuai.');
        }
        if (password_verify((string) $data['password'], $user['password_hash'])) {
            return redirect()->back()->with('error', 'Password baru harus berbeda dari password lama.');
        }

        $model->update($user['id'], ['password' => $data['password']]);
        session()->regenerate(true);

        return redirect()->to(url_to('account.profile'))->with('success', 'Password berhasil diganti.');
    }
}
