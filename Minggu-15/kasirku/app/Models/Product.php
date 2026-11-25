<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    protected $fillable = ['sku', 'name', 'category_id', 'cost_price', 'price', 'stock', 'min_stock', 'unit', 'is_active'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Laba per unit. */
    protected function margin(): Attribute
    {
        return Attribute::get(fn () => $this->price - $this->cost_price);
    }

    protected function isLowStock(): Attribute
    {
        return Attribute::get(fn () => $this->stock <= $this->min_stock);
    }

    public function scopeActive(Builder $q): Builder
    {
        return $q->where('is_active', true);
    }

    public function scopeLowStock(Builder $q): Builder
    {
        return $q->whereColumn('stock', '<=', 'min_stock');
    }

    public function scopeSearch(Builder $q, ?string $keyword): Builder
    {
        return $q->when($keyword, fn ($q) => $q->where(fn ($q) => $q->where('name', 'like', "%{$keyword}%")->orWhere('sku', 'like', "%{$keyword}%")));
    }
}
