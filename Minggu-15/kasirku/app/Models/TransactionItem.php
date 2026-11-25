<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionItem extends Model
{
    public $timestamps = false;

    protected $fillable = ['transaction_id', 'product_id', 'product_name', 'price', 'cost_price', 'qty', 'subtotal'];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
