<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\PaymentModel;
use App\Models\ScheduleModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        (new BookingModel())->expireStale();
        $monthStart = date('Y-m-01 00:00:00');

        return view('admin/dashboard', [
            'title'      => 'Dashboard Admin',
            'revenue'    => (int) ((new BookingModel())->selectSum('total')->where('status', 'lunas')->where('paid_at >=', $monthStart)->first()['total'] ?? 0),
            'sold'       => (int) db_connect()->table('booking_passengers bp')->join('bookings b', 'b.id = bp.booking_id')
                ->where('b.status', 'lunas')->where('b.paid_at >=', $monthStart)->countAllResults(),
            'waiting'    => (new PaymentModel())->where('status', 'menunggu')->countAllResults(),
            'customers'  => (new UserModel())->where('role', 'pelanggan')->countAllResults(),
            'pending'    => (new BookingModel())->withDetails()->where('bookings.status', 'menunggu_verifikasi')->orderBy('bookings.updated_at')->findAll(5),
            'today'      => (new ScheduleModel())->withDetails()->where('schedules.depart_at >=', date('Y-m-d 00:00:00'))
                ->where('schedules.depart_at <=', date('Y-m-d 23:59:59'))->orderBy('schedules.depart_at')->findAll(),
        ]);
    }
}
