<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id', 'label', 'delivery_zone_id', 'city', 'area',
        'address_line', 'building', 'phone', 'notes', 'is_default',
    ];

    protected $casts = ['is_default' => 'boolean'];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function deliveryZone(): BelongsTo { return $this->belongsTo(DeliveryZone::class); }
}
