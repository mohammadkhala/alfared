<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A RoadFN neighbourhood inside one of our delivery cities.
 *
 * Populated by `roadfn:sync-zones` so a customer can pick the exact area,
 * which RoadFN needs when the shipment is created.
 */
class RoadFnArea extends Model
{
    // Eloquent would guess `road_fn_areas` from the class name; the migration
    // created `roadfn_areas`.
    protected $table = 'roadfn_areas';

    protected $fillable = [
        'delivery_zone_id',
        'roadfn_area_id',
        'name_ar',
        'sort_order',
    ];

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('name_ar');
    }
}
