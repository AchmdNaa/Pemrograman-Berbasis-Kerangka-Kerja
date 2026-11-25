<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\MahasiswaModel;
use App\Models\MatakuliahModel;
use App\Models\NilaiModel;
use CodeIgniter\Exceptions\PageNotFoundException;
use CodeIgniter\HTTP\RedirectResponse;

/**
 * CRUD mahasiswa + upload foto (Latihan 1.5 & 1.6, Latihan 2a SIAKAD).
 */
class Mahasiswa extends BaseController
{
    private const FOTO_DIR = FCPATH . 'uploads/mahasiswa/';

    private MahasiswaModel $model;

    public function __construct()
    {
        $this->model = new MahasiswaModel();
    }

    public function index(): string
    {
        $q = trim((string) $this->request->getGet('q'));
        $prodi = (string) $this->request->getGet('prodi');

        $data = $this->model->search($q, $prodi)->paginate(8);

        return view('admin/mahasiswa/index', [
            'title'     => 'Data Mahasiswa',
            'mahasiswa' => $data,
            'pager'     => $this->model->pager,
            'ipk'       => (new NilaiModel())->ipkSemua(),
            'q'         => $q,
            'prodi'     => $prodi,
            'daftarProdi' => MahasiswaModel::PRODI,
        ]);
    }

    public function show(int $id): string
    {
        $mhs = $this->findOr404($id);
        $nilaiModel = new NilaiModel();
        $transkrip = $nilaiModel->transkrip($id);

        $sudahDiambil = array_column($transkrip, 'kode');
        $mkTersedia = array_filter(
            (new MatakuliahModel())->orderBy('semester')->findAll(),
            static fn ($mk) => ! in_array($mk['kode'], $sudahDiambil, true)
        );

        return view('admin/mahasiswa/show', [
            'title'      => 'Transkrip ' . $mhs['nama'],
            'mhs'        => $mhs,
            'transkrip'  => $transkrip,
            'ringkasan'  => NilaiModel::hitungIpk($transkrip),
            'mkTersedia' => $mkTersedia,
            'bobot'      => NilaiModel::BOBOT,
        ]);
    }

    public function new(): string
    {
        return view('admin/mahasiswa/form', [
            'title'       => 'Tambah Mahasiswa',
            'mhs'         => null,
            'daftarProdi' => MahasiswaModel::PRODI,
        ]);
    }

    public function create(): RedirectResponse
    {
        if (! $this->validateFoto()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->formData();
        $foto = $this->simpanFoto();
        if ($foto !== null) {
            $data['foto'] = $foto;
        }

        if (! $this->model->insert($data)) {
            $this->hapusFoto($foto);

            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        return redirect()->to('/admin/mahasiswa')->with('success', "Mahasiswa {$data['nama']} berhasil ditambahkan.");
    }

    public function edit(int $id): string
    {
        return view('admin/mahasiswa/form', [
            'title'       => 'Edit Mahasiswa',
            'mhs'         => $this->findOr404($id),
            'daftarProdi' => MahasiswaModel::PRODI,
        ]);
    }

    public function update(int $id): RedirectResponse
    {
        $lama = $this->findOr404($id);

        if (! $this->validateFoto()) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = $this->formData() + ['id' => $id];
        $foto = $this->simpanFoto();
        if ($foto !== null) {
            $data['foto'] = $foto;
        }

        if (! $this->model->update($id, $data)) {
            $this->hapusFoto($foto);

            return redirect()->back()->withInput()->with('errors', $this->model->errors());
        }

        if ($foto !== null) {
            $this->hapusFoto($lama['foto']); // foto lama diganti
        }

        return redirect()->to('/admin/mahasiswa')->with('success', "Data {$data['nama']} berhasil diperbarui.");
    }

    public function delete(int $id): RedirectResponse
    {
        $mhs = $this->findOr404($id);
        $this->model->delete($id);
        $this->hapusFoto($mhs['foto']);

        return redirect()->to('/admin/mahasiswa')->with('success', "Data {$mhs['nama']} telah dihapus.");
    }

    // ---------------------------------------------------------------

    private function findOr404(int $id): array
    {
        return $this->model->find($id) ?? throw PageNotFoundException::forPageNotFound('Mahasiswa tidak ditemukan.');
    }

    private function formData(): array
    {
        return [
            'nim'      => trim((string) $this->request->getPost('nim')),
            'nama'     => trim((string) $this->request->getPost('nama')),
            'prodi'    => (string) $this->request->getPost('prodi'),
            'angkatan' => (string) $this->request->getPost('angkatan'),
            'email'    => trim((string) $this->request->getPost('email')) ?: null,
        ];
    }

    private function validateFoto(): bool
    {
        // Foto bersifat opsional: validasi hanya dijalankan bila ada file yang diunggah.
        $file = $this->request->getFile('foto');
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return true;
        }

        return $this->validate([
            'foto' => [
                'label' => 'Foto',
                'rules' => 'uploaded[foto]|is_image[foto]|max_size[foto,1024]|ext_in[foto,jpg,jpeg,png,webp]',
            ],
        ]);
    }

    /** Memindahkan file upload ke public/uploads/mahasiswa dengan nama acak. */
    private function simpanFoto(): ?string
    {
        $file = $this->request->getFile('foto');
        if ($file === null || ! $file->isValid() || $file->hasMoved()) {
            return null;
        }
        $nama = $file->getRandomName();
        $file->move(self::FOTO_DIR, $nama);

        return $nama;
    }

    private function hapusFoto(?string $nama): void
    {
        if ($nama && is_file(self::FOTO_DIR . $nama)) {
            unlink(self::FOTO_DIR . $nama);
        }
    }
}
