<?php

namespace App\Controllers\Admin;

use App\Models\BankAccountModel;
use App\Models\BookingModel;
use CodeIgniter\Model;

class Banks extends Crud
{
    protected function model(): Model { return new BankAccountModel(); }
    protected function view(): string { return 'admin/banks'; }
    protected function title(): string { return 'Rekening Bank'; }
    protected function route(): string { return 'admin.banks'; }

    protected function input(): array
    {
        return [
            'bank'         => strtoupper(trim((string) $this->request->getPost('bank'))),
            'account_no'   => preg_replace('/\D/', '', (string) $this->request->getPost('account_no')),
            'account_name' => trim((string) $this->request->getPost('account_name')),
            'is_active'    => $this->request->getPost('is_active') ? 1 : 0,
        ];
    }

    protected function blockDelete(int $id): ?string
    {
        $n = (new BookingModel())->where('bank_account_id', $id)->countAllResults();

        return $n ? "Rekening sudah dipakai {$n} pemesanan. Nonaktifkan saja agar tidak muncul lagi." : null;
    }
}
