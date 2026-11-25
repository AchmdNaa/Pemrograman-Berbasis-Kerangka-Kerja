<?php

namespace App\Controllers;

use App\Models\BookingModel;
use App\Models\BusModel;
use App\Models\RouteModel;
use App\Models\ScheduleModel;
use CodeIgniter\Exceptions\PageNotFoundException;

/**
 * Halaman publik: beranda + pencarian jadwal, hasil pencarian, detail jadwal (denah kursi), cek tiket.
 */
class Home extends BaseController
{
    public function index(): string
    {
        return view('home', [
            'title'    => 'Pesan Tiket Bus Online',
            'cities'   => (new RouteModel())->cities(),
            'routes'   => (new RouteModel())->orderBy('origin')->findAll(),
            'upcoming' => (new ScheduleModel())->withDetails()->where('schedules.depart_at >', date('Y-m-d H:i:s'))
                ->where('schedules.status', 'aktif')->orderBy('schedules.depart_at')->findAll(6),
        ]);
    }

    public function search(): string
    {
        $origin = (string) $this->request->getGet('asal');
        $destination = (string) $this->request->getGet('tujuan');
        $date = (string) $this->request->getGet('tanggal') ?: date('Y-m-d');
        $class = (string) $this->request->getGet('kelas');

        $errors = [];
        if ($origin === '' || $destination === '') {
            $errors[] = 'Pilih kota asal dan tujuan.';
        } elseif ($origin === $destination) {
            $errors[] = 'Kota asal dan tujuan tidak boleh sama.';
        }
        if (! preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || $date < date('Y-m-d')) {
            $errors[] = 'Tanggal keberangkatan tidak valid atau sudah lewat.';
            $date = date('Y-m-d');
        }

        (new BookingModel())->expireStale();

        return view('schedules/search', [
            'title'     => 'Hasil Pencarian',
            'cities'    => (new RouteModel())->cities(),
            'classes'   => BusModel::CLASSES,
            'schedules' => $errors ? [] : (new ScheduleModel())->search($origin, $destination, $date, $class ?: null),
            'errors'    => $errors,
            'q'         => compact('origin', 'destination', 'date', 'class'),
        ]);
    }

    public function schedule(int $id): string
    {
        (new BookingModel())->expireStale();
        $s = (new ScheduleModel())->findDetail($id) ?? throw PageNotFoundException::forPageNotFound('Jadwal tidak ditemukan.');

        return view('schedules/show', [
            'title'   => "{$s['origin']} → {$s['destination']}",
            's'       => $s,
            'layout'  => seat_layout((int) $s['seat_rows'], (int) $s['seat_cols']),
            'booked'  => (new BookingModel())->bookedSeats($id),
            'banks'   => (new \App\Models\BankAccountModel())->where('is_active', 1)->findAll(),
            'closed'  => $s['status'] !== 'aktif' || strtotime($s['depart_at']) <= time(),
        ]);
    }

    /** Cek status tiket publik berdasarkan kode booking (data penumpang disamarkan). */
    public function check(): string
    {
        $code = trim((string) $this->request->getGet('kode'));
        $booking = $code !== '' ? (new BookingModel())->findByCode($code) : null;
        $passengers = $booking ? (new \App\Models\PassengerModel())->where('booking_id', $booking['id'])->findAll() : [];

        return view('check', ['title' => 'Cek Tiket', 'code' => $code, 'b' => $booking, 'passengers' => $passengers]);
    }
}
