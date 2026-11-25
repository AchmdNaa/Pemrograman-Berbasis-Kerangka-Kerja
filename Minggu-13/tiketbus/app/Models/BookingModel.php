<?php

namespace App\Models;

use CodeIgniter\Model;

class BookingModel extends Model
{
    /** Status yang masih "menguasai" kursi. */
    public const OCCUPYING = ['menunggu_pembayaran', 'menunggu_verifikasi', 'lunas'];

    /** Batas waktu pembayaran setelah memesan. */
    public const PAYMENT_WINDOW = '+2 hours';

    protected $table         = 'bookings';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['code', 'user_id', 'schedule_id', 'bank_account_id', 'total', 'status', 'expires_at', 'paid_at'];

    /**
     * Pemesanan yang melewati batas waktu bayar otomatis menjadi "kedaluwarsa"
     * sehingga kursinya kembali tersedia. Dipanggil sebelum menghitung ketersediaan kursi.
     */
    public function expireStale(): int
    {
        $this->builder()
            ->where('status', 'menunggu_pembayaran')
            ->where('expires_at <', date('Y-m-d H:i:s'))
            ->update(['status' => 'kedaluwarsa', 'updated_at' => date('Y-m-d H:i:s')]);

        return $this->db->affectedRows();
    }

    /** Kursi yang sudah terisi pada sebuah jadwal. */
    public function bookedSeats(int $scheduleId): array
    {
        $rows = $this->db->table('booking_passengers bp')
            ->select('bp.seat_no')
            ->join('bookings b', 'b.id = bp.booking_id')
            ->where('b.schedule_id', $scheduleId)
            ->whereIn('b.status', self::OCCUPYING)
            ->get()->getResultArray();

        return array_column($rows, 'seat_no');
    }

    /** Pemesanan lengkap dengan jadwal, rute, bus, dan rekening tujuan. */
    public function withDetails(): self
    {
        return $this->select('bookings.*, schedules.depart_at, schedules.arrive_at, schedules.price, schedules.status AS schedule_status,
                routes.origin, routes.destination, routes.duration_min, buses.code AS bus_code, buses.operator, buses.class,
                users.name AS customer, users.email AS customer_email, users.phone AS customer_phone,
                bank_accounts.bank, bank_accounts.account_no, bank_accounts.account_name')
            ->join('schedules', 'schedules.id = bookings.schedule_id')
            ->join('routes', 'routes.id = schedules.route_id')
            ->join('buses', 'buses.id = schedules.bus_id')
            ->join('users', 'users.id = bookings.user_id')
            ->join('bank_accounts', 'bank_accounts.id = bookings.bank_account_id', 'left');
    }

    public function findByCode(string $code): ?array
    {
        return $this->withDetails()->where('bookings.code', strtoupper(trim($code)))->first();
    }

    public static function newCode(): string
    {
        return 'TB-' . strtoupper(substr(bin2hex(random_bytes(4)), 0, 6));
    }
}
