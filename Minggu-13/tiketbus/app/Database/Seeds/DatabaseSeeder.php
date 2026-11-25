<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * Data awal: akun, armada, rute, jadwal 7 hari ke depan, rekening, dan contoh pemesanan.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $now = date('Y-m-d H:i:s');
        $ts = ['created_at' => $now, 'updated_at' => $now];

        $this->db->table('users')->insertBatch([
            ['name' => 'Admin TiketBus', 'email' => 'admin@tiketbus.test', 'phone' => '081200000001', 'password_hash' => password_hash('Admin#123', PASSWORD_DEFAULT), 'role' => 'admin'] + $ts,
            ['name' => 'Achmad Najwa', 'email' => 'najwa@student.its.ac.id', 'phone' => '081234567801', 'password_hash' => password_hash('Pelanggan#123', PASSWORD_DEFAULT), 'role' => 'pelanggan'] + $ts,
            ['name' => 'Siti Rahmawati', 'email' => 'siti@example.com', 'phone' => '081298765432', 'password_hash' => password_hash('Pelanggan#123', PASSWORD_DEFAULT), 'role' => 'pelanggan'] + $ts,
        ]);

        $this->db->table('buses')->insertBatch([
            ['code' => 'L 7001 UA', 'operator' => 'PO Surya Kencana', 'class' => 'Eksekutif', 'seat_rows' => 8, 'seat_cols' => 4, 'facilities' => 'AC, Toilet, Reclining seat, USB charger, Snack'] + $ts,
            ['code' => 'L 7002 UB', 'operator' => 'PO Surya Kencana', 'class' => 'Ekonomi', 'seat_rows' => 10, 'seat_cols' => 4, 'facilities' => 'AC, Reclining seat'] + $ts,
            ['code' => 'W 1888 AK', 'operator' => 'PO Nusantara Jaya', 'class' => 'Sleeper', 'seat_rows' => 7, 'seat_cols' => 3, 'facilities' => 'AC, Toilet, Kursi tidur, Selimut, Makan malam, Wi-Fi'] + $ts,
            ['code' => 'N 7311 BC', 'operator' => 'PO Nusantara Jaya', 'class' => 'Eksekutif', 'seat_rows' => 8, 'seat_cols' => 4, 'facilities' => 'AC, Toilet, Reclining seat, USB charger'] + $ts,
        ]);

        // [asal, tujuan, km, menit]
        $routes = [
            ['Surabaya', 'Malang', 95, 150], ['Surabaya', 'Yogyakarta', 330, 480], ['Surabaya', 'Semarang', 350, 420],
            ['Surabaya', 'Jakarta', 780, 720], ['Malang', 'Surabaya', 95, 150], ['Yogyakarta', 'Surabaya', 330, 480],
        ];
        foreach ($routes as [$o, $d, $km, $min]) {
            $this->db->table('routes')->insert(['origin' => $o, 'destination' => $d, 'distance_km' => $km, 'duration_min' => $min] + $ts);
        }

        // Jadwal: [rute, bus, jam, harga]
        $plan = [
            [1, 2, '06:00', 45000], [1, 1, '09:30', 75000], [1, 2, '15:00', 45000],
            [2, 1, '07:00', 165000], [2, 3, '20:00', 285000],
            [3, 4, '08:00', 170000], [4, 3, '17:00', 450000], [4, 4, '19:00', 380000],
            [5, 2, '07:00', 45000], [5, 1, '16:00', 75000], [6, 4, '08:30', 165000],
        ];
        for ($day = 0; $day < 7; $day++) {
            $date = date('Y-m-d', strtotime("+{$day} day"));
            foreach ($plan as [$route, $bus, $time, $price]) {
                $duration = $routes[$route - 1][3];
                $depart = "{$date} {$time}:00";
                $this->db->table('schedules')->insert([
                    'bus_id' => $bus, 'route_id' => $route, 'depart_at' => $depart,
                    'arrive_at' => date('Y-m-d H:i:s', strtotime($depart) + $duration * 60),
                    'price' => $price, 'status' => 'aktif',
                ] + $ts);
            }
        }

        $this->db->table('bank_accounts')->insertBatch([
            ['bank' => 'BCA', 'account_no' => '1234567890', 'account_name' => 'PT TiketBus Indonesia', 'is_active' => 1] + $ts,
            ['bank' => 'Mandiri', 'account_no' => '1410012345678', 'account_name' => 'PT TiketBus Indonesia', 'is_active' => 1] + $ts,
            ['bank' => 'BRI', 'account_no' => '002301000123567', 'account_name' => 'PT TiketBus Indonesia', 'is_active' => 1] + $ts,
        ]);

        // Contoh pemesanan lunas milik Siti pada jadwal Surabaya→Malang 09:30 besok (schedule id = 11 + 2)
        $scheduleId = count($plan) + 2;
        $this->db->table('bookings')->insert([
            'code' => 'TB-DEMO01', 'user_id' => 3, 'schedule_id' => $scheduleId, 'bank_account_id' => 1,
            'total' => 150000, 'status' => 'lunas', 'expires_at' => date('Y-m-d H:i:s', strtotime('+2 hours')), 'paid_at' => $now,
        ] + $ts);
        $this->db->table('booking_passengers')->insertBatch([
            ['booking_id' => 1, 'seat_no' => '1A', 'name' => 'Siti Rahmawati', 'id_number' => '3578014501050001', 'phone' => '081298765432'],
            ['booking_id' => 1, 'seat_no' => '1B', 'name' => 'Dewi Lestari', 'id_number' => '3578014911040002', 'phone' => null],
        ]);
    }
}
