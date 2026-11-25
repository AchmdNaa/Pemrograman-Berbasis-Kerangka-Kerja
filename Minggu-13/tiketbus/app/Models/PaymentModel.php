<?php

namespace App\Models;

use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table         = 'payments';
    protected $returnType    = 'array';
    protected $useTimestamps = true;
    protected $allowedFields = ['booking_id', 'sender_name', 'sender_bank', 'sender_account', 'amount', 'proof', 'status', 'note', 'verified_at'];
}
