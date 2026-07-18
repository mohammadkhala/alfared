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
        'name_he',
        'name_en',
        'sort_order',
    ];

    /** Name in the active locale, falling back to Arabic (RoadFN's own). */
    public function getNameAttribute(): string
    {
        return match (app()->getLocale()) {
            'he' => $this->name_he ?: $this->name_ar,
            'en' => $this->name_en ?: $this->name_ar,
            default => $this->name_ar,
        };
    }

    public function deliveryZone(): BelongsTo
    {
        return $this->belongsTo(DeliveryZone::class);
    }

    public function scopeOrdered($q)
    {
        return $q->orderBy('sort_order')->orderBy('name_ar');
    }
}
