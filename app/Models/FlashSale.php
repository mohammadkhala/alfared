<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class FlashSale extends Model
{
    protected $fillable = ['name_ar', 'starts_at', 'ends_at', 'is_active'];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at'   => 'datetime',
        'is_active' => 'boolean',
    ];

    public function saleProducts(): HasMany
    {
        return $this->hasMany(FlashSaleProduct::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'flash_sale_products')
            ->withPivot('sale_price', 'quantity_limit', 'sold_count')
            ->withTimestamps();
    }

    public function getIsRunningAttribute(): bool
    {
        $now = now();
        return $this->is_active
            && ($this->starts_at === null || $this->starts_at <= $now)
            && ($this->ends_at   === null || $this->ends_at   >= $now);
    }
}
