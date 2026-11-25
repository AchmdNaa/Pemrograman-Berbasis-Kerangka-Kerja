<?php

namespace App\Models;

use CodeIgniter\Model;

class ContactMessageModel extends Model
{
    protected $table           = 'contact_messages';
    protected $allowedFields   = ['name', 'email', 'subject', 'message'];
    protected $useTimestamps   = true;
    protected $updatedField    = '';
    protected $returnType      = 'array';
    protected $validationRules = [
        'name'    => ['label' => 'Nama', 'rules' => 'required|min_length[3]|max_length[100]'],
        'email'   => ['label' => 'Email', 'rules' => 'required|valid_email|max_length[120]'],
        'subject' => ['label' => 'Subjek', 'rules' => 'required|min_length[5]|max_length[150]'],
        'message' => ['label' => 'Pesan', 'rules' => 'required|min_length[20]|max_length[2000]'],
    ];
}
