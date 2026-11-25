<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Manajemen pengguna oleh admin: daftar, ubah peran, aktif/nonaktifkan, dan buat akun staf.
 */
class Users extends BaseController
{
    public function index(): string
    {
        $role = (string) $this->request->getGet('role');
        $q = trim((string) $this->request->getGet('q'));
        $model = new UserModel();

        if (in_array($role, UserModel::ROLES, true)) {
            $model->where('role', $role);
        }
        if ($q !== '') {
            $model->groupStart()->like('name', $q)->orLike('email', $q)->groupEnd();
        }

        return view('admin/users', [
            'title' => 'Manajemen Pengguna',
            'users' => $model->orderBy('created_at', 'DESC')->paginate(10),
            'pager' => $model->pager,
            'role'  => $role,
            'q'     => $q,
        ]);
    }

    public function create(): RedirectResponse
    {
        $rules = UserModel::REGISTER_RULES;
        unset($rules['terms'], $rules['password_confirm']);
        $rules['role'] = ['label' => 'Peran', 'rules' => 'required|in_list[customer,operator,admin]'];

        $data = $this->request->getPost(['name', 'email', 'phone', 'password', 'role']);
        $data['email'] = strtolower(trim((string) $data['email']));
        if (! $this->validateData($data, $rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        (new UserModel())->insert($data + ['is_active' => 1]);

        return redirect()->to(url_to('admin.users'))->with('success', "Akun {$data['email']} ({$data['role']}) berhasil dibuat.");
    }

    public function update(int $id): RedirectResponse
    {
        $model = new UserModel();
        $user = $model->find($id) ?? throw PageNotFoundException::forPageNotFound();

        if ($id === (int) auth_user('id')) {
            return redirect()->back()->with('error', 'Anda tidak dapat mengubah peran atau status akun sendiri.');
        }

        $role = (string) $this->request->getPost('role');
        $active = $this->request->getPost('is_active') === '1' ? 1 : 0;
        if (! in_array($role, UserModel::ROLES, true)) {
            return redirect()->back()->with('error', 'Peran tidak valid.');
        }

        $model->update($id, ['role' => $role, 'is_active' => $active]);

        return redirect()->back()->with('success', "Akun {$user['name']} diperbarui: {$role}, " . ($active ? 'aktif' : 'nonaktif') . '.');
    }
}
