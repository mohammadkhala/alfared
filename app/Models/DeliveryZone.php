<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'name_ar', 'name_he', 'name_en',
        'base_fee', 'free_above',           // seeder column names
        'delivery_fee', 'free_shipping_above', // migration column names (alias support)
        'estimated_days', 'is_active',
    ];

    protected $casts = [
        'base_fee'            => 'decimal:2',
        'delivery_fee'        => 'decimal:2',
        'free_above'          => 'decimal:2',
        'free_shipping_above' => 'decimal:2',
        'is_active'           => 'boolean',
    ];

    public function getNameAttribute(): string { return $this->name_ar; }

    public function calculateFee(float $subtotal): float
    {
        // Support both column name variants
        $fee      = $this->base_fee ?? $this->delivery_fee ?? 0;
        $freeAbove= $this->free_above ?? $this->free_shipping_above ?? null;

        if ($freeAbove && $subtotal >= $freeAbove) {
            return 0;
        }
        return (float) $fee;
    }
}
