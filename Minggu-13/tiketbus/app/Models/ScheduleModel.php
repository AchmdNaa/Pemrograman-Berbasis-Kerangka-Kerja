<?php

namespace App\Models;

use CodeIgniter\Model;

class ScheduleModel extends Model
{
    protected $table           = 'schedules';
    protected $returnType      = 'array';
    protected $useTimestamps   = true;
    protected $allowedFields   = ['bus_id', 'route_id', 'depart_at', 'arrive_at', 'price', 'status'];
    protected $validationRules = [
        'bus_id'    => ['label' => 'Bus', 'rules' => 'required|is_not_unique[buses.id]'],
        'route_id'  => ['label' => 'Rute', 'rules' => 'required|is_not_unique[routes.id]'],
        'depart_at' => ['label' => 'Waktu berangkat', 'rules' => 'required|valid_date[Y-m-d H:i:s]'],
        'price'     => ['label' => 'Harga', 'rules' => 'required|is_natural_no_zero'],
        'status'    => ['label' => 'Status', 'rules' => 'required|in_list[aktif,batal]'],
    ];

    /** Jadwal + data bus & rute + jumlah kursi terisi (subquery). */
    public function withDetails(): self
    {
        $active = "'" . implode("','", BookingModel::OCCUPYING) . "'";

        return $this->select("schedules.*, buses.code AS bus_code, buses.operator, buses.class, buses.seat_rows, buses.seat_cols, buses.facilities,
                routes.origin, routes.destination, routes.duration_min, routes.distance_km,
                (buses.seat_rows * buses.seat_cols) AS capacity,
                (SELECT COUNT(*) FROM booking_passengers bp JOIN bookings b ON b.id = bp.booking_id
                  WHERE b.schedule_id = schedules.id AND b.status IN ({$active})) AS booked", false)
            ->join('buses', 'buses.id = schedules.bus_id')
            ->join('routes', 'routes.id = schedules.route_id');
    }

    /** Pencarian jadwal oleh pelanggan: hanya jadwal aktif yang belum berangkat. */
    public function search(string $origin, string $destination, string $date, ?string $class = null): array
    {
        $this->withDetails()
            ->where('routes.origin', $origin)
            ->where('routes.destination', $destination)
            ->where('schedules.depart_at >=', $date . ' 00:00:00')
            ->where('schedules.depart_at <=', $date . ' 23:59:59')
            ->where('schedules.depart_at >', date('Y-m-d H:i:s'))
            ->where('schedules.status', 'aktif');
        if ($class) {
            $this->where('buses.class', $class);
        }

        return $this->orderBy('schedules.depart_at')->findAll();
    }

    public function findDetail(int $id): ?array
    {
        return $this->withDetails()->where('schedules.id', $id)->first();
    }
}
