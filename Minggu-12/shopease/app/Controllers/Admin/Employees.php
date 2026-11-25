<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Libraries\ImageUploader;
use App\Models\EmployeeModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Latihan CRUD data karyawan (memakai EmployeeModel dengan soft delete).
 */
class Employees extends BaseController
{
    private EmployeeModel $model;
    private ImageUploader $uploader;

    public function __construct()
    {
        $this->model = new EmployeeModel();
        $this->uploader = new ImageUploader('employees', 1024);
    }

    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $dept = (string) $this->request->getGet('departemen');
        $status = (string) $this->request->getGet('status');
        $trash = $this->request->getGet('arsip') === '1';

        $builder = $trash ? $this->model->onlyDeleted() : $this->model;

        return view('admin/employees/index', [
            'title'       => 'Data Karyawan',
            'employees'   => $builder->search($q, $dept, $status)->paginate(10),
            'pager'       => $this->model->pager,
            'summary'     => (new EmployeeModel())->summary(),
            'archived'    => (new EmployeeModel())->onlyDeleted()->countAllResults(),
            'departments' => EmployeeModel::DEPARTMENTS,
            'statuses'    => EmployeeModel::STATUSES,
            'q'           => $q,
            'dept'        => $dept,
            'status'      => $status,
            'trash'       => $trash,
        ]);
    }

    public function show(int $id): string
    {
        return view('admin/employees/show', ['title' => 'Detail Karyawan', 'e' => $this->findOr404($id)]);
    }

    public function new(): string
    {
        return view('admin/employees/form', $this->formData('Tambah Karyawan', null));
    }

    public function create(): RedirectResponse
    {
        return $this->persist(null);
    }

    public function edit(int $id): string
    {
        return view('admin/employees/form', $this->formData('Edit Karyawan', $this->findOr404($id)));
    }

    public function update(int $id): RedirectResponse
    {
        return $this->persist($this->findOr404($id));
    }

    /** Soft delete: data dipindah ke arsip (deleted_at terisi). */
    public function delete(int $id): RedirectResponse
    {
        $e = $this->findOr404($id);
        $this->model->delete($id);

        return redirect()->to(url_to('admin.employees'))->with('success', "{$e['name']} dipindahkan ke arsip.");
    }

    public function restore(int $id): RedirectResponse
    {
        $e = $this->model->onlyDeleted()->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $this->model->builder()->where('id', $id)->update(['deleted_at' => null]);

        return redirect()->to(url_to('admin.employees') . '?arsip=1')->with('success', "{$e['name']} dipulihkan dari arsip.");
    }

    /** Hapus permanen (purge) beserta fotonya. */
    public function purge(int $id): RedirectResponse
    {
        $e = $this->model->onlyDeleted()->find($id) ?? throw PageNotFoundException::forPageNotFound();
        $this->model->delete($id, true);
        $this->uploader->delete($e['photo']);

        return redirect()->to(url_to('admin.employees') . '?arsip=1')->with('success', "Data {$e['name']} dihapus permanen.");
    }

    // -----------------------------------------------------------------

    private function persist(?array $old): RedirectResponse
    {
        $data = [
            'nip'        => strtoupper(trim((string) $this->request->getPost('nip'))),
            'name'       => trim((string) $this->request->getPost('name')),
            'position'   => trim((string) $this->request->getPost('position')),
            'department' => (string) $this->request->getPost('department'),
            'email'      => strtolower(trim((string) $this->request->getPost('email'))),
            'phone'      => trim((string) $this->request->getPost('phone')) ?: null,
            'hire_date'  => (string) $this->request->getPost('hire_date'),
            'salary'     => (string) $this->request->getPost('salary'),
            'status'     => (string) $this->request->getPost('status'),
        ];
        if ($old !== null) {
            $data['id'] = $old['id'];
        }

        $photo = $this->uploader->store($this->request, 'photo');
        if ($photo === false) {
            return redirect()->back()->withInput()->with('errors', ['photo' => $this->uploader->error()]);
        }
        if ($photo !== null) {
            $data['photo'] = $photo;
        }

        if (! $this->model->save($data)) {
            $this->uploader->delete($photo ?: null);

            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }
        if ($old !== null && $photo !== null) {
            $this->uploader->delete($old['photo']);
        }

        return redirect()->to(url_to('admin.employees'))->with('success', "Data {$data['name']} berhasil " . ($old ? 'diperbarui.' : 'ditambahkan.'));
    }

    private function formData(string $title, ?array $e): array
    {
        return ['title' => $title, 'e' => $e, 'departments' => EmployeeModel::DEPARTMENTS, 'statuses' => EmployeeModel::STATUSES];
    }

    private function findOr404(int $id): array
    {
        return $this->model->find($id) ?? throw PageNotFoundException::forPageNotFound('Karyawan tidak ditemukan.');
    }
}
