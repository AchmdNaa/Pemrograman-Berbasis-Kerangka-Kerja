<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Reports extends BaseController
{
    /** Laporan penjualan tiket (berdasarkan tanggal pembayaran lunas). */
    public function sales(): string
    {
        $from = (string) $this->request->getGet('dari') ?: date('Y-m-01');
        $to = (string) $this->request->getGet('sampai') ?: date('Y-m-d');
        // Hanya format tanggal yang diterima (nilai ini juga dipakai di subquery).
        $isDate = static fn (string $d) => (bool) preg_match('/^\d{4}-\d{2}-\d{2}$/', $d) && strtotime($d) !== false;
        if (! $isDate($from)) {
            $from = date('Y-m-01');
        }
        if (! $isDate($to)) {
            $to = date('Y-m-d');
        }
        $db = db_connect();

        $base = static fn () => $db->table('bookings b')
            ->join('schedules s', 's.id = b.schedule_id')
            ->join('routes r', 'r.id = s.route_id')
            ->where('b.status', 'lunas')
            ->where('b.paid_at >=', "{$from} 00:00:00")
            ->where('b.paid_at <=', "{$to} 23:59:59");

        $perRoute = $base()->select("r.origin, r.destination, COUNT(DISTINCT b.id) AS orders,
                (SELECT COUNT(*) FROM booking_passengers bp JOIN bookings b2 ON b2.id = bp.booking_id JOIN schedules s2 ON s2.id = b2.schedule_id
                  WHERE s2.route_id = r.id AND b2.status = 'lunas' AND b2.paid_at BETWEEN '{$from} 00:00:00' AND '{$to} 23:59:59') AS tickets,
                SUM(b.total) AS revenue", false)
            ->groupBy('r.id')->orderBy('revenue', 'DESC')->get()->getResultArray();

        $list = $base()->select('b.code, b.total, b.paid_at, r.origin, r.destination, s.depart_at')
            ->orderBy('b.paid_at', 'DESC')->get()->getResultArray();

        return view('admin/reports', [
            'title'    => 'Laporan Penjualan',
            'from'     => $from,
            'to'       => $to,
            'perRoute' => $perRoute,
            'list'     => $list,
            'revenue'  => array_sum(array_column($perRoute, 'revenue')),
            'tickets'  => array_sum(array_column($perRoute, 'tickets')),
        ]);
    }

    public function customers(): string
    {
        $rows = (new UserModel())->select('users.*, COUNT(bookings.id) AS orders, SUM(CASE WHEN bookings.status = \'lunas\' THEN bookings.total ELSE 0 END) AS spent', false)
            ->join('bookings', 'bookings.user_id = users.id', 'left')
            ->where('users.role', 'pelanggan')->groupBy('users.id')->orderBy('users.created_at', 'DESC')->findAll();

        return view('admin/customers', ['title' => 'Pelanggan', 'rows' => $rows]);
    }
}
