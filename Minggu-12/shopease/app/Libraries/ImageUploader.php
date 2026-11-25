<?php

namespace App\Libraries;

use CodeIgniter\HTTP\IncomingRequest;

/**
 * Upload gambar yang dipakai ulang oleh Product Management dan data karyawan.
 * File disimpan di public/uploads/{folder} dengan nama acak.
 */
class ImageUploader
{
    private string $dir;
    private ?string $error = null;

    public function __construct(private string $folder, private int $maxKb = 2048)
    {
        $this->dir = FCPATH . 'uploads/' . $folder . '/';
    }

    /**
     * Memvalidasi lalu memindahkan file. Mengembalikan nama file baru,
     * null bila tidak ada file diunggah, atau false bila file tidak valid (lihat error()).
     */
    public function store(IncomingRequest $request, string $field): string|false|null
    {
        $file = $request->getFile($field);
        if ($file === null || $file->getError() === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        $validation = service('validation');
        $validation->setRules([
            $field => [
                'label' => 'Gambar',
                'rules' => "uploaded[{$field}]|is_image[{$field}]|max_size[{$field},{$this->maxKb}]|ext_in[{$field},jpg,jpeg,png,webp]",
            ],
        ]);
        if (! $validation->withRequest($request)->run()) {
            $this->error = $validation->getError($field);

            return false;
        }

        $name = $file->getRandomName();
        $file->move($this->dir, $name);

        return $name;
    }

    public function delete(?string $name): void
    {
        if ($name && is_file($this->dir . $name)) {
            unlink($this->dir . $name);
        }
    }

    public function error(): ?string
    {
        return $this->error;
    }
}
