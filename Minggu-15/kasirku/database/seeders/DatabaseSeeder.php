<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create(['name' => 'Achmad Najwa', 'email' => 'admin@kasirku.test', 'password' => 'Admin1234', 'role' => 'admin']);
        $kasir1 = User::create(['name' => 'Rina Kasir', 'email' => 'kasir@kasirku.test', 'password' => 'Kasir1234', 'role' => 'kasir']);
        $kasir2 = User::create(['name' => 'Bagus Kasir', 'email' => 'bagus@kasirku.test', 'password' => 'Kasir1234', 'role' => 'kasir']);

        $cats = collect([
            'Minuman' => '#0ea5e9', 'Makanan Ringan' => '#f59e0b', 'Mi & Bahan Pokok' => '#ef4444',
            'Kebutuhan Rumah' => '#10b981', 'Perawatan Diri' => '#8b5cf6', 'Alat Tulis' => '#64748b',
        ])->map(fn ($color, $name) => Category::create(['name' => $name, 'color' => $color]));

        // [kategori, sku, nama, harga beli, harga jual, stok, satuan]
        $products = [
            ['Minuman', 'MNM001', 'Air Mineral 600 ml', 2500, 4000, 120, 'botol'],
            ['Minuman', 'MNM002', 'Teh Botol 450 ml', 4200, 6000, 80, 'botol'],
            ['Minuman', 'MNM003', 'Kopi Susu Kaleng', 6500, 9000, 48, 'pcs'],
            ['Minuman', 'MNM004', 'Susu UHT Cokelat 250 ml', 4800, 6500, 60, 'pcs'],
            ['Minuman', 'MNM005', 'Minuman Isotonik 500 ml', 5500, 7500, 4, 'botol'],
            ['Makanan Ringan', 'SNK001', 'Keripik Kentang 68 g', 8500, 11500, 40, 'bungkus'],
            ['Makanan Ringan', 'SNK002', 'Biskuit Cokelat', 6000, 8500, 36, 'bungkus'],
            ['Makanan Ringan', 'SNK003', 'Wafer Krim Vanila', 3200, 5000, 50, 'bungkus'],
            ['Makanan Ringan', 'SNK004', 'Kacang Atom 100 g', 5500, 7500, 3, 'bungkus'],
            ['Makanan Ringan', 'SNK005', 'Roti Sobek Cokelat', 11000, 14500, 15, 'pcs'],
            ['Mi & Bahan Pokok', 'POK001', 'Mi Instan Goreng', 2700, 3500, 200, 'pcs'],
            ['Mi & Bahan Pokok', 'POK002', 'Mi Instan Kuah Soto', 2600, 3500, 180, 'pcs'],
            ['Mi & Bahan Pokok', 'POK003', 'Beras Premium 5 kg', 68000, 78000, 20, 'pcs'],
            ['Mi & Bahan Pokok', 'POK004', 'Minyak Goreng 1 L', 15500, 18500, 30, 'liter'],
            ['Mi & Bahan Pokok', 'POK005', 'Gula Pasir 1 kg', 15000, 17500, 25, 'kg'],
            ['Mi & Bahan Pokok', 'POK006', 'Telur Ayam 1 kg', 26000, 30000, 0, 'kg'],
            ['Kebutuhan Rumah', 'RMH001', 'Sabun Cuci Piring 780 ml', 12500, 16000, 24, 'botol'],
            ['Kebutuhan Rumah', 'RMH002', 'Deterjen Bubuk 800 g', 17000, 21500, 18, 'bungkus'],
            ['Kebutuhan Rumah', 'RMH003', 'Tisu Wajah 250 lembar', 11000, 14000, 30, 'pcs'],
            ['Perawatan Diri', 'PRW001', 'Sabun Mandi Batang', 3500, 5000, 60, 'pcs'],
            ['Perawatan Diri', 'PRW002', 'Pasta Gigi 190 g', 11500, 15000, 28, 'pcs'],
            ['Perawatan Diri', 'PRW003', 'Sampo Sachet', 800, 1000, 150, 'sachet'],
            ['Alat Tulis', 'ATK001', 'Pulpen Gel Hitam', 2500, 4000, 70, 'pcs'],
            ['Alat Tulis', 'ATK002', 'Buku Tulis 38 Lembar', 3000, 4500, 90, 'pcs'],
        ];
        $models = collect();
        foreach ($products as [$cat, $sku, $name, $cost, $price, $stock, $unit]) {
            $models->push(Product::create([
                'category_id' => $cats[$cat]->id, 'sku' => $sku, 'name' => $name, 'cost_price' => $cost,
                'price' => $price, 'stock' => $stock + 40, 'min_stock' => 5, 'unit' => $unit,
            ]));
        }

        // Riwayat transaksi 7 hari terakhir (deterministik agar hasil seeder selalu sama)
        mt_srand(2026);
        $methods = ['tunai', 'tunai', 'tunai', 'qris', 'debit'];
        $realNow = Carbon::now();          // disimpan dulu karena setTestNow() di bawah mengubah "sekarang"
        $today = Carbon::today();
        foreach (range(6, 0) as $daysAgo) {
            $times = collect(range(1, mt_rand(5, 11)))
                ->map(fn () => $today->copy()->subDays($daysAgo)->setTime(mt_rand(8, 20), mt_rand(0, 59)))
                ->sort()->values();
            foreach ($times as $time) {
                if ($time->gt($realNow)) {
                    continue;
                }
                $picked = $models->filter(fn ($p) => $p->stock > 45)->random(mt_rand(1, 4));
                $lines = $picked->map(fn ($p) => ['p' => $p, 'qty' => mt_rand(1, 3)]);
                $subtotal = $lines->sum(fn ($l) => $l['p']->price * $l['qty']);
                $method = $methods[mt_rand(0, 4)];
                $paid = $method === 'tunai' ? (int) (ceil($subtotal / 5000) * 5000) : $subtotal;

                Carbon::setTestNow($time);   // agar nomor nota & created_at sesuai tanggal transaksi
                $trx = Transaction::create([
                    'invoice_no' => Transaction::nextInvoiceNo(), 'user_id' => [$admin, $kasir1, $kasir2][mt_rand(0, 2)]->id,
                    'subtotal' => $subtotal, 'discount' => 0, 'total' => $subtotal, 'payment_method' => $method,
                    'paid' => $paid, 'change' => $paid - $subtotal,
                ]);
                foreach ($lines as $l) {
                    $trx->items()->create(['product_id' => $l['p']->id, 'product_name' => $l['p']->name, 'price' => $l['p']->price,
                        'cost_price' => $l['p']->cost_price, 'qty' => $l['qty'], 'subtotal' => $l['p']->price * $l['qty']]);
                    $l['p']->decrement('stock', $l['qty']);
                }
            }
        }
        Carbon::setTestNow();

        // Kembalikan stok awal sesuai daftar (stok di atas ditambah 40 agar transaksi contoh tidak menghabiskan stok)
        foreach ($products as $i => $row) {
            $models[$i]->update(['stock' => $row[5]]);
        }
    }
}
