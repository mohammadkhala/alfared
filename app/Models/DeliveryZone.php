<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = [
        'parent_id',
        'name_ar', 'name_he', 'name_en',
        'base_fee', 'free_above',           // seeder column names
        'delivery_fee', 'free_shipping_above', // migration column names (alias support)
        'estimated_days', 'is_active', 'sort_order',
        'roadfn_city_id', 'roadfn_area_id',
    ];

    protected $casts = [
        'base_fee'            => 'decimal:2',
        'delivery_fee'        => 'decimal:2',
        'free_above'          => 'decimal:2',
        'free_shipping_above' => 'decimal:2',
        'is_active'           => 'boolean',
    ];

    /**
     * A zone must never be its own parent. One legacy row reached that state
     * and it broke pricing silently: pricingZone() returned the zone itself,
     * so it charged its own stale fee instead of the region's.
     */
    protected static function booted(): void
    {
        static::saving(function (self $zone) {
            if ($zone->parent_id !== null && (int) $zone->parent_id === (int) $zone->id) {
                $zone->parent_id = null;
            }
        });
    }

    // ── Hierarchy ────────────────────────────────────────────────────────
    // Main zones (الضفة / القدس / الداخل) hold the pricing; sub zones are the
    // governorates/cities the customer actually picks.

    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_id')->orderBy('sort_order')->orderBy('name_ar');
    }

    /** RoadFN neighbourhoods inside this city (sub zones only). */
    public function roadfnAreas()
    {
        return $this->hasMany(RoadFnArea::class)->ordered();
    }

    /** Top-level zones only. */
    public function scopeMain($q)
    {
        return $q->whereNull('parent_id');
    }

    /** Selectable sub zones only. */
    public function scopeSub($q)
    {
        return $q->whereNotNull('parent_id');
    }

    public function isMain(): bool
    {
        return $this->parent_id === null;
    }

    /** The zone whose pricing applies — a sub zone inherits from its parent. */
    public function pricingZone(): self
    {
        return $this->parent_id ? ($this->parent ?? $this) : $this;
    }

    public function getNameAttribute(): string { return $this->name_ar; }

    /** "الضفة الغربية — نابلس" for invoices and the admin list. */
    public function getFullNameAttribute(): string
    {
        return $this->parent_id && $this->parent
            ? "{$this->parent->name_ar} — {$this->name_ar}"
            : $this->name_ar;
    }

    public function calculateFee(float $subtotal): float
    {
        // Pricing always comes from the main zone, so sub zones stay
        // presentation-only and can't drift out of sync.
        $zone = $this->pricingZone();

        // Support both column name variants
        $fee       = $zone->base_fee ?? $zone->delivery_fee ?? 0;
        $freeAbove = $zone->free_above ?? $zone->free_shipping_above ?? null;

        if ($freeAbove && $subtotal >= $freeAbove) {
            return 0;
        }
        return (float) $fee;
    }
}
