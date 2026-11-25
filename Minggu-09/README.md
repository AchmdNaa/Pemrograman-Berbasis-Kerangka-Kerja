# Tugas Pemrograman Berbasis Kerangka Kerja (PBKK) — Minggu 9

**Nama:** Achmad Najwa
**NIM:** 5025231265
**Kelas:** PBKK D
**Materi:** Framework CodeIgniter — Latihan 1 & 2

---

## Daftar Isi

1. [Landasan Teori: CodeIgniter 4 dan MVC](#1-landasan-teori-codeigniter-4-dan-mvc)
2. [Gambaran Aplikasi](#2-gambaran-aplikasi)
3. [Instalasi dan Konfigurasi](#3-instalasi-dan-konfigurasi)
4. [Latihan 1 — Materi CodeIgniter per Topik](#4-latihan-1--materi-codeigniter-per-topik)
5. [Latihan 2a — Sistem Informasi Akademik (SIAKAD)](#5-latihan-2a--sistem-informasi-akademik-siakad)
6. [Latihan 2b — Toko Online](#6-latihan-2b--toko-online)
7. [Struktur Proyek](#7-struktur-proyek)
8. [Dokumentasi Hasil](#8-dokumentasi-hasil)

---

## 1. Landasan Teori: CodeIgniter 4 dan MVC

**CodeIgniter 4** adalah framework PHP yang ringan dan cepat, berbasis pola **Model-View-Controller (MVC)**:

```
Browser ──request──► public/index.php ──► Router (app/Config/Routes.php)
                                              │
                                   Filter (auth, role, csrf)
                                              │
                                              ▼
                                         Controller ──► Model ──► Database
                                              │
                                              ▼
                                            View ──response (HTML)──► Browser
```

| Komponen | Tugas | Contoh di proyek |
|---|---|---|
| **Router** | Memetakan URL ke `Controller::method` | `$routes->get('toko/(:segment)', 'Toko::detail/$1');` |
| **Controller** | Menerima request, memanggil model, memilih view | `App\Controllers\Admin\Mahasiswa` |
| **Model** | Akses tabel, validasi, query | `MahasiswaModel`, `NilaiModel::hitungIpk()` |
| **View** | Tampilan HTML (layout + section) | `layouts/admin.php`, `admin/mahasiswa/index.php` |
| **Filter** | Kode yang berjalan sebelum/sesudah controller | `AuthFilter`, `RoleFilter`, CSRF |

Perbedaan penting CodeIgniter 3 (materi lama) dan CodeIgniter 4 (dipakai di sini):

| CodeIgniter 3 | CodeIgniter 4 |
|---|---|
| `$this->load->model('Mahasiswa_model')` | `new MahasiswaModel()` (namespace + autoload) |
| `$this->load->view('v_mahasiswa', $data)` | `return view('admin/mahasiswa/index', $data);` |
| `$this->input->post('nim')` | `$this->request->getPost('nim')` |
| Folder `application/`, akses `index.php/controller` | Folder `app/`, root web hanya `public/` |
| Tabel dibuat manual lewat phpMyAdmin | **Migration** dan **Seeder** (`php spark migrate`) |

## 2. Gambaran Aplikasi

Kedua latihan digabung dalam **satu aplikasi bernama KampusKu**, dengan login bersama dan panel admin bergaya SB Admin:

| Modul | URL | Pengguna |
|---|---|---|
| Beranda | `/` | Publik |
| Toko Online (katalog, keranjang, checkout) | `/toko`, `/keranjang`, `/checkout` | Publik |
| Panel admin: Dashboard, SIAKAD, Produk, Pesanan | `/admin/...` | Login (`admin` / `staff`) |
| Manajemen pengguna | `/admin/users` | Hanya `admin` |

Akun demo dari seeder:

| Username | Password | Peran |
|---|---|---|
| `admin` | `admin123` | Akses penuh, termasuk hapus data dan kelola akun |
| `staff` | `staff123` | Boleh melihat, menambah, dan mengubah, tetapi tidak boleh menghapus |

## 3. Instalasi dan Konfigurasi

Kebutuhan: PHP 8.2+ (ekstensi `intl`, `mbstring`, `mysqli`), Composer, dan MySQL/MariaDB (XAMPP atau Laragon).

```powershell
cd "D:\Semester 7\Pemrograman-Berbasis-Kerangka-Kerja\Minggu-09\latihan-ci"
composer install                      # mengunduh framework ke folder vendor/
copy .env.example .env                # lalu sesuaikan koneksi database
```

Buat database `db_latihan_ci` di phpMyAdmin (atau `CREATE DATABASE db_latihan_ci;`), lalu:

```powershell
php spark migrate                     # membuat semua tabel
php spark db:seed DatabaseSeeder      # mengisi akun, mahasiswa, mata kuliah, nilai, dan produk
php spark serve                       # buka http://localhost:8080
```

Proyek dibuat dari `composer create-project codeigniter4/appstarter latihan-ci`. Konfigurasi yang diubah:

| File | Perubahan |
|---|---|
| `app/Config/App.php` | `indexPage = ''` (URL tanpa `index.php`), zona waktu `Asia/Jakarta`, locale `id` |
| `app/Config/Filters.php` | Alias `auth` dan `role`, serta filter `csrf` global untuk semua form POST |
| `app/Config/Routes.php` | Seluruh route didefinisikan eksplisit (auto-routing tetap mati demi keamanan) |
| `app/Config/Pager.php` | Template pagination `bootstrap` |
| `app/Language/id/Validation.php` | Pesan validasi berbahasa Indonesia |

## 4. Latihan 1 — Materi CodeIgniter per Topik

### 4.1 Instalasi dan Konfigurasi CI

Lihat bagian 3. Halaman pertama yang muncul setelah instalasi adalah route `/` → `Home::index`.

### 4.2 Fungsi Router dan Controller

```php
// app/Config/Routes.php
$routes->get('/', 'Home::index');
$routes->get('toko/(:segment)', 'Toko::detail/$1');      // (:segment) diteruskan sebagai parameter

$routes->group('admin', ['filter' => 'auth', 'namespace' => 'App\Controllers\Admin'], static function ($routes) {
    $routes->get('mahasiswa', 'Mahasiswa::index');
    $routes->post('mahasiswa', 'Mahasiswa::create');
    $routes->get('mahasiswa/(:num)/edit', 'Mahasiswa::edit/$1');
    ...
});
```

- **Route group** memberi prefix `admin/` dan filter `auth` ke puluhan route sekaligus.
- Aksi yang mengubah data (simpan, hapus, logout) memakai **POST**, bukan link GET, sehingga terlindungi CSRF dan tidak terpicu oleh crawler.

### 4.3 Bootstrap untuk View

Bootstrap 5 dan Bootstrap Icons disimpan lokal di `public/assets/vendor/` (tidak bergantung CDN). View memakai **layout inheritance**:

```php
<?= $this->extend('layouts/public') ?>
<?= $this->section('content') ?>
    ... isi halaman ...
<?= $this->endSection() ?>
```

### 4.4 Template SB Admin

`layouts/admin.php` meniru struktur **SB Admin**: sidebar biru gradasi dengan *heading* per modul, topbar berisi nama pengguna dan peran, serta kartu statistik berwarna di dashboard. Menu aktif ditandai dengan helper `url_is('admin/mahasiswa*')`.

### 4.5 Model dan CRUD

Model CodeIgniter 4 sudah menyediakan `find()`, `insert()`, `update()`, `delete()`, `paginate()`, dan validasi otomatis:

```php
class MahasiswaModel extends Model
{
    protected $table         = 'mahasiswa';
    protected $allowedFields = ['nim', 'nama', 'prodi', 'angkatan', 'email', 'foto'];
    protected $useTimestamps = true;

    protected $validationRules = [
        'id'  => 'permit_empty|is_natural_no_zero',
        'nim' => ['label' => 'NIM', 'rules' => 'required|regex_match[/^\d{10}$/]|is_unique[mahasiswa.nim,id,{id}]'],
        ...
    ];
}
```

`is_unique[mahasiswa.nim,id,{id}]` membuat NIM tetap unik, tetapi mengizinkan mahasiswa menyimpan NIM miliknya sendiri saat diedit. `$allowedFields` mencegah *mass assignment* kolom lain.

### 4.6 Penanganan File Upload

```php
$file = $this->request->getFile('foto');
if ($file !== null && $file->getError() !== UPLOAD_ERR_NO_FILE) {
    $this->validate(['foto' => 'uploaded[foto]|is_image[foto]|max_size[foto,1024]|ext_in[foto,jpg,jpeg,png,webp]']);
}
$nama = $file->getRandomName();                 // nama acak, mencegah tabrakan & nama berbahaya
$file->move(FCPATH . 'uploads/mahasiswa/', $nama);
```

Foto lama dihapus saat diganti atau saat data mahasiswa dihapus. Jika penyimpanan database gagal, file yang baru diunggah ikut dihapus agar tidak menjadi sampah. Hal yang sama berlaku untuk gambar produk.

### 4.7 Mengatur Akses User

- **Login**: `password_verify()` terhadap hash `password_hash()`, lalu `session()->regenerate()` untuk mencegah *session fixation*.
- **`AuthFilter`**: menolak tamu dan mengingat URL tujuan, sehingga setelah login pengguna kembali ke halaman yang tadi diminta.
- **`RoleFilter`**: dipasang di route hapus data dan manajemen akun (`'filter' => 'role:admin'`). Staff yang mencoba mengaksesnya diarahkan kembali dengan pesan "tidak memiliki hak akses".
- Tombol hapus juga disembunyikan dari staff di view (`is_admin()`), tetapi pertahanan utamanya tetap di route filter.

## 5. Latihan 2a — Sistem Informasi Akademik (SIAKAD)

| Fitur | Keterangan |
|---|---|
| Dashboard | Jumlah mahasiswa & mata kuliah, rata-rata IPK, grafik sebaran per prodi, mahasiswa terbaru |
| Data mahasiswa | CRUD + foto, pencarian NIM/nama, filter prodi, pagination 8 data per halaman, kolom SKS & IPK |
| Mata kuliah | Tambah/edit/hapus dalam satu halaman. Mata kuliah yang sudah punya nilai tidak bisa dihapus |
| Transkrip & nilai | Input nilai huruf (A, AB, B, BC, C, D, E) per mata kuliah, hapus nilai, IPK dihitung otomatis |

Rancangan tabel (migration `CreateSiakadTables`):

```
mahasiswa (id, nim UNIQUE, nama, prodi, angkatan, email, foto, timestamps)
matakuliah (id, kode UNIQUE, nama, sks, semester)
nilai (id, mahasiswa_id FK→mahasiswa CASCADE, matakuliah_id FK→matakuliah RESTRICT,
       nilai_huruf, UNIQUE(mahasiswa_id, matakuliah_id))
```

Perhitungan IPK di model:

```php
public const BOBOT = ['A' => 4.0, 'AB' => 3.5, 'B' => 3.0, 'BC' => 2.5, 'C' => 2.0, 'D' => 1.0, 'E' => 0.0];

public static function hitungIpk(array $transkrip): array
{
    foreach ($transkrip as $row) {
        $totalSks  += (int) $row['sks'];
        $totalMutu += self::BOBOT[$row['nilai_huruf']] * (int) $row['sks'];
    }
    return ['sks' => $totalSks, 'ipk' => $totalSks > 0 ? round($totalMutu / $totalSks, 2) : 0.0];
}
```

Untuk daftar mahasiswa, IPK semua mahasiswa dihitung dari **satu query JOIN** (`ipkSemua()`), bukan satu query per baris.

## 6. Latihan 2b — Toko Online

| Fitur | Keterangan |
|---|---|
| Katalog | Pencarian nama, filter kategori, pagination, label stok habis, halaman detail dengan slug & produk terkait |
| Keranjang | Disimpan di **session** (`App\Libraries\Keranjang`): tambah, ubah jumlah, hapus, kosongkan |
| Validasi stok | Tidak bisa menambah melebihi stok. Isi keranjang disesuaikan otomatis bila stok/harga berubah atau produk dihapus |
| Checkout | Form penerima (nama, telepon `08…`, alamat), kode pesanan unik `INV-yymmdd-XXXXXX`, halaman konfirmasi |
| Admin produk | CRUD + upload gambar, slug otomatis dari nama |
| Admin pesanan | Filter status, detail item, ubah status `baru → diproses → dikirim → selesai`, atau `batal` |

Checkout dijalankan di dalam **transaksi database**. Stok dikurangi dengan query bersyarat agar tidak pernah minus walaupun dua pembeli checkout bersamaan:

```php
$db->transStart();
$pesananId = $pesananModel->insert([...]);
foreach ($detail['items'] as $item) {
    $db->table('produk')
        ->set('stok', 'stok - ' . (int) $item['qty'], false)
        ->where('id', $item['id'])
        ->where('stok >=', (int) $item['qty'])   // hanya berhasil jika stok masih cukup
        ->update();
    if ($db->affectedRows() !== 1) { $db->transRollback(); return redirect()->to('/keranjang')->with('error', ...); }
    $itemModel->insert([...]);
}
$db->transComplete();
```

Jika pesanan dibatalkan admin, stok setiap item dikembalikan. Nama dan harga produk disalin ke `pesanan_item`, sehingga riwayat pesanan tetap benar walaupun produk diubah atau dihapus.

## 7. Struktur Proyek

```text
Minggu-09/
├── README.md
├── images/                         # Screenshot hasil
└── latihan-ci/
    ├── app/
    │   ├── Config/                 # App, Filters, Routes, Pager
    │   ├── Controllers/
    │   │   ├── Home.php, Auth.php, Toko.php, Keranjang.php
    │   │   └── Admin/              # Dashboard, Mahasiswa, Matakuliah, Nilai, Produk, Pesanan, Users
    │   ├── Database/
    │   │   ├── Migrations/         # users, tabel SIAKAD, tabel toko
    │   │   └── Seeds/DatabaseSeeder.php
    │   ├── Filters/                # AuthFilter, RoleFilter
    │   ├── Helpers/app_helper.php  # rupiah(), produk_img(), foto_mhs(), is_admin()
    │   ├── Language/id/            # Pesan validasi Bahasa Indonesia
    │   ├── Libraries/Keranjang.php # Keranjang berbasis session
    │   ├── Models/                 # 8 model
    │   └── Views/                  # layouts, partials, auth, admin/*, toko/*, Pager
    ├── public/
    │   ├── assets/                 # Bootstrap, Bootstrap Icons, app.css
    │   └── uploads/                # Foto mahasiswa & gambar produk (diabaikan git)
    ├── composer.json
    └── .env.example
```

## 8. Dokumentasi Hasil

### Halaman publik & Toko Online

| Beranda | Katalog |
|---|---|
| ![](images/01-beranda.png) | ![](images/02-katalog.png) |

| Detail produk | Notifikasi tambah ke keranjang |
|---|---|
| ![](images/03-detail-produk.png) | ![](images/04-tambah-keranjang.png) |

| Keranjang | Checkout |
|---|---|
| ![](images/05-keranjang.png) | ![](images/06-checkout.png) |

| Pesanan berhasil | Halaman login |
|---|---|
| ![](images/07-pesanan-sukses.png) | ![](images/08-login.png) |

### Panel admin — SIAKAD

| Dashboard | Data mahasiswa |
|---|---|
| ![](images/09-dashboard.png) | ![](images/10-mahasiswa.png) |

| Pencarian | Validasi form |
|---|---|
| ![](images/11-cari-mahasiswa.png) | ![](images/12-validasi-mahasiswa.png) |

| Transkrip & IPK | Mata kuliah |
|---|---|
| ![](images/13-transkrip.png) | ![](images/14-matakuliah.png) |

### Panel admin — Toko & Akses

| Produk | Edit produk |
|---|---|
| ![](images/15-produk.png) | ![](images/16-edit-produk.png) |

| Daftar pesanan | Detail & ubah status pesanan |
|---|---|
| ![](images/17-pesanan.png) | ![](images/18-detail-pesanan.png) |

| Manajemen pengguna (admin) | Staff ditolak membuka menu Pengguna |
|---|---|
| ![](images/19-pengguna.png) | ![](images/20-staff-ditolak.png) |

| Staff: tombol hapus tidak tersedia |
|---|
| ![](images/21-staff-tanpa-hapus.png) |
