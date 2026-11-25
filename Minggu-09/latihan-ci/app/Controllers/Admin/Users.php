<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Manajemen akun pengelola — hanya untuk peran admin (lihat filter role:admin di Routes).
 */
class Users extends BaseController
{
    public function index(): string
    {
        return view('admin/users/index', [
            'title' => 'Pengguna',
            'users' => (new UserModel())->orderBy('role')->orderBy('name')->findAll(),
        ]);
    }

    public function create(): RedirectResponse
    {
        $rules = [
            'name'     => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
            'username' => ['label' => 'Username', 'rules' => 'required|alpha_dash|min_length[4]|max_length[50]|is_unique[users.username]'],
            'password' => ['label' => 'Password', 'rules' => 'required|min_length[8]'],
            'role'     => ['label' => 'Peran', 'rules' => 'required|in_list[admin,staff]'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new UserModel())->insert([
            'name'          => $this->request->getPost('name'),
            'username'      => $this->request->getPost('username'),
            'password_hash' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'          => $this->request->getPost('role'),
        ]);

        return redirect()->to('/admin/users')->with('success', 'Akun baru berhasil dibuat.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($id === (int) session('userId')) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun sendiri.');
        }
        (new UserModel())->delete($id);

        return redirect()->to('/admin/users')->with('success', 'Akun dihapus.');
    }
}
