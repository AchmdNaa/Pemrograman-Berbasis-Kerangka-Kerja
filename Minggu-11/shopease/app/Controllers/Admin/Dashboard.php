<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ContactMessageModel;
use App\Models\ProductModel;
use App\Models\UserModel;

class Dashboard extends BaseController
{
    public function index(): string
    {
        return view('admin/dashboard', [
            'title'      => 'Dashboard Admin',
            'roles'      => (new UserModel())->countByRole(),
            'products'   => (new ProductModel())->where('is_active', 1)->countAllResults(),
            'lowStock'   => (new ProductModel())->where('stock <', 10)->orderBy('stock')->findAll(5),
            'messages'   => (new ContactMessageModel())->orderBy('id', 'DESC')->findAll(5),
            'newUsers'   => (new UserModel())->orderBy('id', 'DESC')->findAll(5),
        ]);
    }
}
