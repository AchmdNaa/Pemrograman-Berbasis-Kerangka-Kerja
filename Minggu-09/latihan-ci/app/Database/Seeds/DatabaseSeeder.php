<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data awal: dua akun (admin & staff), data SIAKAD, dan katalog toko.
 * Jalankan: php spark db:seed DatabaseSeeder
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        helper("url");
        $now = date("Y-m-d H:i:s");

        // ---------- Akun ----------
        $this->db->table('users')->insertBatch([
            ['name' => 'Administrator', 'username' => 'admin', 'password_hash' => password_hash('admin123', PASSWORD_DEFAULT), 'role' => 'admin', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Staf Akademik', 'username' => 'staff', 'password_hash' => password_hash('staff123', PASSWORD_DEFAULT), 'role' => 'staff', 'created_at' => $now, 'updated_at' => $now],
        ]);

        // ---------- SIAKAD ----------
        $mahasiswa = [
            ['5025231265', 'Achmad Najwa Maruf', 'Teknik Informatika', 2023, 'najwa@student.its.ac.id'],
            ['5025231288', 'Leyan Harits R W', 'Teknik Informatika', 2023, 'leyan@student.its.ac.id'],
            ['5025241175', 'Derryell Josua Pandiangan', 'Teknik Informatika', 2024, 'derryell@student.its.ac.id'],
            ['5026231010', 'Siti Rahmawati', 'Sistem Informasi', 2023, 'siti@student.its.ac.id'],
            ['5026231022', 'Dewi Lestari', 'Sistem Informasi', 2023, null],
            ['5027241020', 'Budi Santoso', 'Teknologi Informasi', 2024, 'budi@student.its.ac.id'],
            ['5027241031', 'Rina Kartika', 'Teknologi Informasi', 2024, 'rina@student.its.ac.id'],
            ['5028241005', 'Fajar Nugroho', 'Rekayasa Perangkat Lunak', 2024, 'fajar@student.its.ac.id'],
            ['5029241012', 'Nadia Putri', 'Sains Data', 2024, 'nadia@student.its.ac.id'],
            ['5029241019', 'Rizky Pratama', 'Sains Data', 2024, null],
            ['5025221050', 'Andi Wijaya', 'Teknik Informatika', 2022, 'andi@student.its.ac.id'],
            ['5026221077', 'Maya Sari', 'Sistem Informasi', 2022, 'maya@student.its.ac.id'],
        ];
        foreach ($mahasiswa as [$nim, $nama, $prodi, $angkatan, $email]) {
            $this->db->table('mahasiswa')->insert(compact('nim', 'nama', 'prodi', 'angkatan', 'email') + ['created_at' => $now, 'updated_at' => $now]);
        }

        $this->db->table('matakuliah')->insertBatch([
            ['kode' => 'EF234301', 'nama' => 'Pemrograman Berbasis Kerangka Kerja', 'sks' => 3, 'semester' => 5],
            ['kode' => 'EF234302', 'nama' => 'Basis Data', 'sks' => 4, 'semester' => 3],
            ['kode' => 'EF234303', 'nama' => 'Pemrograman Web', 'sks' => 3, 'semester' => 3],
            ['kode' => 'EF234304', 'nama' => 'Rekayasa Perangkat Lunak', 'sks' => 3, 'semester' => 4],
            ['kode' => 'EF234305', 'nama' => 'Pemrograman Perangkat Bergerak', 'sks' => 3, 'semester' => 6],
            ['kode' => 'EF234306', 'nama' => 'Jaringan Komputer', 'sks' => 4, 'semester' => 4],
        ]);

        $nilai = ['A', 'AB', 'B', 'A', 'BC', 'AB'];
        for ($m = 1; $m <= 12; $m++) {
            $jumlahMk = 3 + ($m % 4);
            for ($k = 1; $k <= $jumlahMk; $k++) {
                $this->db->table('nilai')->insert([
                    'mahasiswa_id'  => $m,
                    'matakuliah_id' => $k,
                    'nilai_huruf'   => $nilai[($m + $k) % count($nilai)],
                    'created_at'    => $now,
                ]);
            }
        }

        // ---------- Toko Online ----------
        $this->db->table('kategori')->insertBatch([
            ['nama' => 'Alat Tulis', 'slug' => 'alat-tulis'],
            ['nama' => 'Elektronik', 'slug' => 'elektronik'],
            ['nama' => 'Merchandise Kampus', 'slug' => 'merchandise-kampus'],
            ['nama' => 'Buku', 'slug' => 'buku'],
        ]);

        $produk = [
            [1, 'Buku Catatan A5 Dot Grid', 25000, 40, 'Buku catatan 120 halaman kertas 100 gsm, cocok untuk catatan kuliah dan bullet journal.'],
            [1, 'Pulpen Gel 0.5 mm (isi 3)', 18000, 75, 'Pulpen gel tinta hitam, biru, dan merah yang cepat kering dan tidak mudah luntur.'],
            [1, 'Stabilo Pastel Set 6 Warna', 42000, 25, 'Highlighter warna pastel untuk menandai materi penting.'],
            [2, 'Flashdisk 64 GB USB 3.2', 79000, 30, 'Flashdisk berkecepatan tinggi untuk menyimpan tugas dan file presentasi.'],
            [2, 'Mouse Wireless Silent', 95000, 18, 'Mouse nirkabel dengan klik senyap, baterai tahan hingga 12 bulan.'],
            [2, 'Kalkulator Scientific', 155000, 12, 'Kalkulator ilmiah 417 fungsi untuk mata kuliah kalkulus dan statistika.'],
            [3, 'Kaos Informatika Angkatan', 110000, 50, 'Kaos katun combed 30s dengan sablon logo jurusan.'],
            [3, 'Tumbler Stainless 500 ml', 85000, 20, 'Tumbler tahan panas dan dingin hingga 12 jam.'],
            [3, 'Lanyard & ID Card Holder', 20000, 100, 'Lanyard kampus lengkap dengan holder kartu mahasiswa.'],
            [4, 'Clean Code (Terjemahan)', 135000, 8, 'Panduan menulis kode yang mudah dibaca dan dipelihara.'],
            [4, 'Belajar CodeIgniter 4', 98000, 15, 'Buku praktis membangun aplikasi web dengan CodeIgniter 4 dan pola MVC.'],
            [4, 'Algoritma dan Struktur Data', 120000, 0, 'Konsep algoritma dasar hingga lanjut dengan contoh kode.'],
        ];
        foreach ($produk as [$kategori, $nama, $harga, $stok, $deskripsi]) {
            $this->db->table('produk')->insert([
                'kategori_id' => $kategori,
                'nama'        => $nama,
                'slug'        => url_title($nama, '-', true),
                'deskripsi'   => $deskripsi,
                'harga'       => $harga,
                'stok'        => $stok,
                'created_at'  => $now,
                'updated_at'  => $now,
            ]);
        }
    }
}
