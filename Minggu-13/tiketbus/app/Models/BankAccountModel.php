<?php

namespace App\Models;

use CodeIgniter\Model;

class BankAccountModel extends Model
{
    protected $table           = 'bank_accounts';
    protected $returnType      = 'array';
    protected $useTimestamps   = true;
    protected $allowedFields   = ['bank', 'account_no', 'account_name', 'is_active'];
    protected $validationRules = [
        'bank'         => ['label' => 'Bank', 'rules' => 'required|max_length[40]'],
        'account_no'   => ['label' => 'Nomor rekening', 'rules' => 'required|numeric|min_length[8]|max_length[30]'],
        'account_name' => ['label' => 'Atas nama', 'rules' => 'required|min_length[3]|max_length[100]'],
    ];
}
