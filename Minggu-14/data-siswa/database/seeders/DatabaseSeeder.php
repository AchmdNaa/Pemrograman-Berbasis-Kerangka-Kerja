<?php

namespace Database\Seeders;

use App\Models\Kelas;
use App\Models\Siswa;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $kelas = collect([
            ['X IPA 1', 10, 'IPA', 'Dra. Sri Wahyuni'],
            ['X IPS 1', 10, 'IPS', 'Bambang Supriyadi, S.Pd.'],
            ['XI IPA 1', 11, 'IPA', 'Dewi Kartika, M.Pd.'],
            ['XI IPS 1', 11, 'IPS', 'Agus Salim, S.Pd.'],
            ['XII IPA 1', 12, 'IPA', 'Rina Marlina, S.Si.'],
            ['XII Bahasa', 12, 'Bahasa', 'Hendra Gunawan, S.S.'],
        ])->map(fn ($k) => Kelas::create(['nama' => $k[0], 'tingkat' => $k[1], 'jurusan' => $k[2], 'wali_kelas' => $k[3], 'kapasitas' => 36]));

        // [nama, L/P, tempat lahir, tanggal lahir, indeks kelas]
        $siswa = [
            ['Achmad Najwa', 'L', 'Surabaya', '2009-05-12', 0], ['Aisyah Putri Ramadhani', 'P', 'Sidoarjo', '2009-09-21', 0],
            ['Bima Arya Saputra', 'L', 'Malang', '2009-02-03', 0], ['Citra Ayu Lestari', 'P', 'Gresik', '2009-11-14', 0],
            ['Dimas Pratama', 'L', 'Surabaya', '2009-07-30', 1], ['Eka Nur Fadilah', 'P', 'Lamongan', '2009-01-08', 1],
            ['Fajar Ramadhan', 'L', 'Mojokerto', '2009-04-17', 1], ['Gita Savitri', 'P', 'Surabaya', '2009-12-25', 1],
            ['Hafiz Alfarizi', 'L', 'Pasuruan', '2008-03-11', 2], ['Intan Permatasari', 'P', 'Surabaya', '2008-06-02', 2],
            ['Joko Susilo', 'L', 'Jombang', '2008-08-19', 2], ['Kirana Maharani', 'P', 'Malang', '2008-10-05', 2],
            ['Lukman Hakim', 'L', 'Bangkalan', '2008-01-27', 3], ['Maya Anggraini', 'P', 'Surabaya', '2008-05-15', 3],
            ['Naufal Rizky', 'L', 'Sidoarjo', '2008-09-09', 3], ['Olivia Rahma', 'P', 'Kediri', '2008-12-01', 3],
            ['Putra Wijaya', 'L', 'Surabaya', '2007-02-14', 4], ['Qonita Zahra', 'P', 'Tuban', '2007-04-22', 4],
            ['Rafi Ahmad Fauzi', 'L', 'Surabaya', '2007-07-07', 4], ['Salsabila Nur', 'P', 'Gresik', '2007-11-30', 4],
            ['Taufik Hidayat', 'L', 'Madiun', '2007-03-18', 5], ['Ulfa Khairunnisa', 'P', 'Surabaya', '2007-06-26', 5],
            ['Vino Bastian', 'L', 'Probolinggo', '2007-08-13', 5], ['Wulan Sari', 'P', 'Banyuwangi', '2007-10-10', 5],
        ];
        foreach ($siswa as $i => [$nama, $jk, $tempat, $tgl, $k]) {
            $angkatan = ['0' => '25', '1' => '25', '2' => '24', '3' => '24', '4' => '23', '5' => '23'][$k];
            Siswa::create([
                'nis'           => '20' . $angkatan . str_pad((string) ($k + 1), 2, '0', STR_PAD_LEFT) . str_pad((string) ($i + 1), 4, '0', STR_PAD_LEFT),
                'nama'          => $nama,
                'jenis_kelamin' => $jk,
                'tempat_lahir'  => $tempat,
                'tanggal_lahir' => $tgl,
                'kelas_id'      => $kelas[$k]->id,
                'email'         => $i % 3 === 2 ? null : strtolower(explode(' ', $nama)[0]) . ($i + 1) . '@siswa.sch.id',
                'no_hp'         => $i % 4 === 3 ? null : '0812' . str_pad((string) (34560000 + $i * 137), 8, '0', STR_PAD_LEFT),
                'alamat'        => 'Jl. Pendidikan No. ' . ($i + 3) . ', ' . $tempat . ', Jawa Timur',
            ]);
        }
    }
}
