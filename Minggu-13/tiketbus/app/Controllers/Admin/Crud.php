<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\RedirectResponse;
use CodeIgniter\Model;

/**
 * Kerangka CRUD satu halaman (tabel + form) untuk data master: bus, rute, rekening.
 * Kelas turunan cukup menentukan model, view, judul, dan cara membaca input form.
 */
abstract class Crud extends BaseController
{
    abstract protected function model(): Model;
    abstract protected function view(): string;
    abstract protected function title(): string;
    abstract protected function route(): string;
    abstract protected function input(): array;

    /** Pesan bila data tidak boleh dihapus (mis. masih dipakai jadwal). null = boleh. */
    protected function blockDelete(int $id): ?string
    {
        return null;
    }

    protected function extra(): array
    {
        return [];
    }

    public function index(): string
    {
        $edit = (int) $this->request->getGet('edit');

        return view($this->view(), [
            'title' => $this->title(),
            'rows'  => $this->model()->orderBy('id', 'DESC')->findAll(),
            'edit'  => $edit ? $this->model()->find($edit) : null,
        ] + $this->extra());
    }

    public function save(): RedirectResponse
    {
        $data = $this->input();
        $id = (int) $this->request->getPost('id');
        if ($id > 0) {
            $data['id'] = $id;
        }
        $model = $this->model();
        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(url_to($this->route()))->with('success', 'Data berhasil disimpan.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ($msg = $this->blockDelete($id)) {
            return redirect()->back()->with('error', $msg);
        }
        $this->model()->delete($id);

        return redirect()->to(url_to($this->route()))->with('success', 'Data dihapus.');
    }
}
