<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\BookingModel;
use App\Models\BusModel;
use App\Models\RouteModel;
use App\Models\ScheduleModel;
use CodeIgniter\HTTP\RedirectResponse;

class Schedules extends BaseController
{
    public function index(): string
    {
        $date = (string) $this->request->getGet('tanggal');
        $route = (int) $this->request->getGet('rute');
        $model = (new ScheduleModel())->withDetails();
        if ($date) {
            $model->where('schedules.depart_at >=', "{$date} 00:00:00")->where('schedules.depart_at <=', "{$date} 23:59:59");
        } else {
            $model->where('schedules.depart_at >=', date('Y-m-d 00:00:00'));
        }
        if ($route) {
            $model->where('schedules.route_id', $route);
        }
        $edit = (int) $this->request->getGet('edit');

        return view('admin/schedules', [
            'title'     => 'Jadwal Keberangkatan',
            'schedules' => $model->orderBy('schedules.depart_at')->paginate(15),
            'pager'     => $model->pager,
            'buses'     => (new BusModel())->orderBy('operator')->findAll(),
            'routes'    => (new RouteModel())->orderBy('origin')->findAll(),
            'edit'      => $edit ? (new ScheduleModel())->find($edit) : null,
            'date'      => $date,
            'route'     => $route,
        ]);
    }

    public function save(): RedirectResponse
    {
        $routeRow = (new RouteModel())->find((int) $this->request->getPost('route_id'));
        $depart = str_replace('T', ' ', (string) $this->request->getPost('depart_at'));
        if (strlen($depart) === 16) {
            $depart .= ':00';
        }
        $data = [
            'bus_id'    => (string) $this->request->getPost('bus_id'),
            'route_id'  => (string) $this->request->getPost('route_id'),
            'depart_at' => $depart,
            'arrive_at' => $routeRow && strtotime($depart) ? date('Y-m-d H:i:s', strtotime($depart) + $routeRow['duration_min'] * 60) : null,
            'price'     => (string) $this->request->getPost('price'),
            'status'    => (string) $this->request->getPost('status') ?: 'aktif',
        ];
        $id = (int) $this->request->getPost('id');
        if ($id) {
            $data['id'] = $id;
        }

        $model = new ScheduleModel();
        if (! $model->save($data)) {
            return redirect()->back()->withInput()->with('errors', $model->errors());
        }

        return redirect()->to(url_to('admin.schedules'))->with('success', 'Jadwal tersimpan.');
    }

    public function delete(int $id): RedirectResponse
    {
        if ((new BookingModel())->where('schedule_id', $id)->countAllResults() > 0) {
            return redirect()->back()->with('error', 'Jadwal sudah memiliki pemesanan. Ubah statusnya menjadi "batal" sebagai gantinya.');
        }
        (new ScheduleModel())->delete($id);

        return redirect()->to(url_to('admin.schedules'))->with('success', 'Jadwal dihapus.');
    }
}
