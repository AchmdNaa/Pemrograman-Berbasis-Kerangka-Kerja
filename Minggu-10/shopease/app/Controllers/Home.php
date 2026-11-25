<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\ProductModel;

/**
 * Landing page ShopEase.
 */
class Home extends BaseController
{
    public function index(): string
    {
        $products = new ProductModel();

        return view('home/index', [
            'title'       => 'Belanja Mudah, Harga Ramah',
            'categories'  => (new CategoryModel())->withProductCount(),
            'featured'    => $products->featured(4),
            'latest'      => (new ProductModel())->latest(8),
            'bestSellers' => (new ProductModel())->bestSellers(4),
        ]);
    }
}
