<?php

namespace App\Models;

use CodeIgniter\Model;

class PassengerModel extends Model
{
    protected $table         = 'booking_passengers';
    protected $returnType    = 'array';
    protected $allowedFields = ['booking_id', 'seat_no', 'name', 'id_number', 'phone'];
}
