<?php

namespace App\Controllers\Admin;

use App\Models\BusModel;
use App\Models\ScheduleModel;
use CodeIgniter\Model;

class Buses extends Crud
{
    protected function model(): Model { return new BusModel(); }
    protected function view(): string { return 'admin/buses'; }
    protected function title(): string { return 'Armada Bus'; }
    protected function route(): string { return 'admin.buses'; }
    protected function extra(): array { return ['classes' => BusModel::CLASSES]; }

    protected function input(): array
    {
        return [
            'code'       => strtoupper(trim((string) $this->request->getPost('code'))),
            'operator'   => trim((string) $this->request->getPost('operator')),
            'class'      => (string) $this->request->getPost('class'),
            'seat_rows'  => (string) $this->request->getPost('seat_rows'),
            'seat_cols'  => (string) $this->request->getPost('seat_cols'),
            'facilities' => trim((string) $this->request->getPost('facilities')),
        ];
    }

    protected function blockDelete(int $id): ?string
    {
        $n = (new ScheduleModel())->where('bus_id', $id)->countAllResults();

        return $n ? "Bus masih dipakai {$n} jadwal." : null;
    }
}
