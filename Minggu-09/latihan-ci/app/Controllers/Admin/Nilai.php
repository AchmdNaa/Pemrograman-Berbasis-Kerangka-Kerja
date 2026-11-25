<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\NilaiModel;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * Input dan hapus nilai mata kuliah pada transkrip mahasiswa.
 */
class Nilai extends BaseController
{
    public function store(int $mahasiswaId): RedirectResponse
    {
        $rules = [
            'matakuliah_id' => ['label' => 'Mata kuliah', 'rules' => 'required|is_not_unique[matakuliah.id]'],
            'nilai_huruf'   => ['label' => 'Nilai', 'rules' => 'required|in_list[' . implode(',', array_keys(NilaiModel::BOBOT)) . ']'],
        ];
        if (! $this->validate($rules)) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }

        $model = new NilaiModel();
        $mkId = (int) $this->request->getPost('matakuliah_id');

        if ($model->where(['mahasiswa_id' => $mahasiswaId, 'matakuliah_id' => $mkId])->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Mata kuliah tersebut sudah ada di transkrip.');
        }

        $model->insert([
            'mahasiswa_id'  => $mahasiswaId,
            'matakuliah_id' => $mkId,
            'nilai_huruf'   => $this->request->getPost('nilai_huruf'),
        ]);

        return redirect()->to("/admin/mahasiswa/{$mahasiswaId}")->with('success', 'Nilai ditambahkan.');
    }

    public function delete(int $mahasiswaId, int $nilaiId): RedirectResponse
    {
        (new NilaiModel())->where('mahasiswa_id', $mahasiswaId)->delete($nilaiId);

        return redirect()->to("/admin/mahasiswa/{$mahasiswaId}")->with('success', 'Nilai dihapus.');
    }
}
