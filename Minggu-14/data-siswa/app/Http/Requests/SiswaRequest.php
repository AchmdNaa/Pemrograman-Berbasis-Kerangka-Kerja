<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

/**
 * Form Request: validasi data siswa dipisahkan dari controller.
 * Dipakai untuk store (tambah) dan update (edit).
 */
class SiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        // Saat edit, NIS & email milik siswa itu sendiri tidak dianggap duplikat.
        $id = $this->route('siswa')?->id;

        return [
            'nis'           => ['required', 'digits:10', Rule::unique('siswa', 'nis')->ignore($id)],
            'nama'          => ['required', 'string', 'min:3', 'max:100'],
            'jenis_kelamin' => ['required', Rule::in(['L', 'P'])],
            'tempat_lahir'  => ['required', 'string', 'max:60'],
            'tanggal_lahir' => ['required', 'date', 'before:-10 years', 'after:-25 years'],
            'kelas_id'      => ['required', 'exists:kelas,id'],
            'email'         => ['nullable', 'email', 'max:120', Rule::unique('siswa', 'email')->ignore($id)],
            'no_hp'         => ['nullable', 'regex:/^(08|\+628)\d{7,11}$/'],
            'alamat'        => ['required', 'string', 'min:10', 'max:500'],
            'foto'          => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:1024'],
        ];
    }

    public function attributes(): array
    {
        return [
            'nis' => 'NIS', 'nama' => 'nama', 'jenis_kelamin' => 'jenis kelamin', 'tempat_lahir' => 'tempat lahir',
            'tanggal_lahir' => 'tanggal lahir', 'kelas_id' => 'kelas', 'no_hp' => 'nomor HP', 'alamat' => 'alamat', 'foto' => 'foto',
        ];
    }

    public function messages(): array
    {
        return [
            'tanggal_lahir.before' => 'Siswa minimal berusia 10 tahun.',
            'tanggal_lahir.after'  => 'Siswa maksimal berusia 25 tahun.',
            'no_hp.regex'          => 'Nomor HP harus diawali 08 atau +628.',
        ];
    }
}
