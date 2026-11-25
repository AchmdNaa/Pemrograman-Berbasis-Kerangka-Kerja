<?php

namespace App\Controllers\Admin;

use App\Models\RouteModel;
use App\Models\ScheduleModel;
use CodeIgniter\Model;

class Routes extends Crud
{
    protected function model(): Model { return new RouteModel(); }
    protected function view(): string { return 'admin/routes'; }
    protected function title(): string { return 'Rute'; }
    protected function route(): string { return 'admin.routes'; }

    protected function input(): array
    {
        return [
            'origin'       => ucwords(strtolower(trim((string) $this->request->getPost('origin')))),
            'destination'  => ucwords(strtolower(trim((string) $this->request->getPost('destination')))),
            'distance_km'  => (string) $this->request->getPost('distance_km'),
            'duration_min' => (string) $this->request->getPost('duration_min'),
        ];
    }

    protected function blockDelete(int $id): ?string
    {
        $n = (new ScheduleModel())->where('route_id', $id)->countAllResults();

        return $n ? "Rute masih dipakai {$n} jadwal." : null;
    }
}
