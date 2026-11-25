<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        helper('url');
        $now = date('Y-m-d H:i:s');

        $this->db->table('categories')->insertBatch([
            ['name' => 'Elektronik', 'slug' => 'elektronik', 'icon' => 'bi-laptop', 'description' => 'Gadget dan aksesori komputer'],
            ['name' => 'Fashion', 'slug' => 'fashion', 'icon' => 'bi-bag', 'description' => 'Pakaian, tas, dan sepatu'],
            ['name' => 'Rumah Tangga', 'slug' => 'rumah-tangga', 'icon' => 'bi-house-heart', 'description' => 'Perlengkapan rumah dan dapur'],
            ['name' => 'Olahraga', 'slug' => 'olahraga', 'icon' => 'bi-bicycle', 'description' => 'Peralatan olahraga dan outdoor'],
            ['name' => 'Buku & Alat Tulis', 'slug' => 'buku-alat-tulis', 'icon' => 'bi-book', 'description' => 'Buku, catatan, dan alat tulis'],
        ]);

        // [kategori, nama, harga, harga coret, stok, terjual, rating, unggulan, deskripsi]
        $products = [
            [1, 'Earbuds Bluetooth ANC Pro', 349000, 499000, 35, 820, 4.8, 1, 'Earbuds nirkabel dengan active noise cancelling, baterai hingga 30 jam dengan case, dan tahan cipratan air (IPX4).'],
            [1, 'Keyboard Mekanikal 75% Hot-swap', 589000, 699000, 18, 410, 4.9, 1, 'Keyboard mekanikal layout 75% dengan switch hot-swap, lampu RGB, dan koneksi kabel, Bluetooth, atau 2.4 GHz.'],
            [1, 'Powerbank 20.000 mAh 22.5W', 229000, null, 60, 1530, 4.7, 0, 'Powerbank berkapasitas besar dengan fast charging 22.5W dan dua port USB-A serta satu USB-C.'],
            [1, 'Webcam Full HD 1080p', 275000, 320000, 22, 260, 4.6, 0, 'Webcam 1080p 30fps dengan mikrofon ganda, cocok untuk kuliah daring dan rapat online.'],
            [2, 'Kemeja Flanel Kotak Unisex', 159000, 199000, 48, 690, 4.7, 1, 'Kemeja flanel katun lembut dengan motif kotak klasik, tersedia ukuran S sampai XXL.'],
            [2, 'Sneakers Kanvas Low Putih', 249000, null, 30, 940, 4.8, 0, 'Sneakers kanvas klasik dengan sol karet anti-slip, nyaman dipakai seharian.'],
            [2, 'Tas Ransel Laptop 15 inci', 299000, 379000, 25, 510, 4.8, 1, 'Ransel anti air dengan kompartemen laptop berbusa, port USB, dan banyak kantong organizer.'],
            [3, 'Air Fryer Digital 4 Liter', 649000, 799000, 12, 330, 4.7, 1, 'Air fryer digital dengan 8 menu preset, kapasitas 4 liter, dan wadah antilengket.'],
            [3, 'Set Wadah Kaca Meal Prep (5 pcs)', 189000, null, 40, 280, 4.6, 0, 'Wadah kaca borosilikat tahan panas dengan tutup kedap udara, aman untuk microwave.'],
            [3, 'Lampu Meja LED Lipat', 135000, 165000, 55, 720, 4.5, 0, 'Lampu belajar LED dengan 3 mode warna, tingkat kecerahan bertingkat, dan port pengisian USB.'],
            [4, 'Matras Yoga TPE 6 mm', 179000, 229000, 33, 450, 4.7, 0, 'Matras yoga anti-slip dua sisi berbahan TPE ramah lingkungan, ringan, dan mudah dibawa.'],
            [4, 'Botol Minum Olahraga 1 Liter', 79000, null, 90, 1210, 4.6, 0, 'Botol BPA-free dengan penanda waktu minum dan tutup flip yang anti tumpah.'],
            [4, 'Raket Badminton Carbon', 389000, 450000, 0, 175, 4.8, 0, 'Raket full carbon ringan (84 g) dengan keseimbangan head-light untuk pukulan cepat.'],
            [5, 'Planner Mingguan 2027', 89000, null, 70, 380, 4.9, 0, 'Planner hardcover 52 minggu dengan halaman target bulanan dan habit tracker.'],
            [5, 'Set Pulpen Gel Warna (12 pcs)', 59000, 75000, 120, 860, 4.6, 0, 'Pulpen gel 0.5 mm dengan 12 warna cerah untuk catatan dan jurnal.'],
            [5, 'Buku "Belajar Pemrograman Web Modern"', 125000, null, 15, 140, 4.8, 1, 'Panduan praktis HTML, CSS, JavaScript, dan framework PHP untuk pemula hingga menengah.'],
        ];

        foreach ($products as $i => [$cat, $name, $price, $old, $stock, $sold, $rating, $featured, $desc]) {
            $this->db->table('products')->insert([
                'category_id' => $cat,
                'name'        => $name,
                'slug'        => url_title($name, '-', true),
                'description' => $desc,
                'price'       => $price,
                'old_price'   => $old,
                'stock'       => $stock,
                'sold'        => $sold,
                'rating'      => $rating,
                'is_featured' => $featured,
                'is_active'   => 1,
                // selisih waktu agar urutan "terbaru" bervariasi
                'created_at'  => date('Y-m-d H:i:s', strtotime("-{$i} days")),
                'updated_at'  => $now,
            ]);
        }
    }
}
