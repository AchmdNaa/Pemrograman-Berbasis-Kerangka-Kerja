<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MatakuliahModel;
use App\Models\NilaiModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD mata kuliah dalam satu halaman (form tambah/edit di samping tabel).
 */
class Matakuliah extends BaseController
{
    public function index(): string
    {
        $model = new MatakuliahModel();
        $editId = (int) $this->request->getGet('edit');

        return view('admin/matakuliah/index', [
            'title'      => 'Mata Kuliah',
            'matakuliah' => $model->orderBy('semester')->orderBy('kode')->findAll(),
            'edit'       => $editId ? $model->find($editId) : null,
        ]);
    }

    public function save(): RedirectResponse
    {
        $model = new MatakuliahModel();
        $id = (int) $this->request->getPost('id');
        $data = [
            'kode'     => strtoupper(trim((string) $this->request->getPost('kode'))),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'sks'      => (string) $this->request->getPost('sks'),
            'semester' => (string) $this->request->getPost('semester'),
        ];
        if ($id > 0) {
            $data['id'] = $id;
        }

        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to('/admin/matakuliah')->with('success', "Mata kuliah {$data['nama']} tersimpan.");
    }

    public function delete(int $id): RedirectResponse
    {
        // Dicek di aplikasi (selain foreign key RESTRICT di database) agar pesannya jelas.
        if ((new NilaiModel())->where('matakuliah_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Mata kuliah tidak bisa dihapus karena sudah memiliki nilai mahasiswa.');
        }
        (new MatakuliahModel())->delete($id);

        return redirect()->to('/admin/matakuliah')->with('success', 'Mata kuliah dihapus.');
    }
}
