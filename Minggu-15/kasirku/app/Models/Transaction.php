<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaction extends Model
{
    public const METHODS = ['tunai' => 'Tunai', 'qris' => 'QRIS', 'debit' => 'Kartu Debit'];

    protected $fillable = ['invoice_no', 'user_id', 'subtotal', 'discount', 'total', 'payment_method', 'paid', 'change', 'note'];

    public function items(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function cashier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /** Nomor nota: INV-YYMMDD-0001 (berurutan per hari). */
    public static function nextInvoiceNo(): string
    {
        $prefix = 'INV-' . now()->format('ymd') . '-';
        $last = static::where('invoice_no', 'like', $prefix . '%')->orderByDesc('invoice_no')->value('invoice_no');
        $next = $last ? ((int) substr($last, -4)) + 1 : 1;

        return $prefix . str_pad((string) $next, 4, '0', STR_PAD_LEFT);
    }
}
