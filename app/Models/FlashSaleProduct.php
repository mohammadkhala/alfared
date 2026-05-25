<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashSaleProduct extends Model
{
    protected $fillable = ['flash_sale_id', 'product_id', 'sale_price', 'quantity_limit', 'sold_count'];

    protected $casts = [
        'sale_price'     => 'decimal:2',
        'quantity_limit' => 'integer',
        'sold_count'     => 'integer',
    ];

    public function flashSale(): BelongsTo
    {
        return $this->belongsTo(FlashSale::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getRemainingAttribute(): ?int
    {
        if ($this->quantity_limit === null) return null;
        return max(0, $this->quantity_limit - $this->sold_count);
    }
}
