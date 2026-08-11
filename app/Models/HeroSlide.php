<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * One slide in the homepage hero slider, editable from the admin panel.
 * Text fields carry ar/he/en; the accessors pick the active locale and fall
 * back to Arabic so a half-translated slide still renders.
 */
class HeroSlide extends Model
{
    protected $guarded = [];

    protected $casts = [
        'is_active'  => 'boolean',
        'sort_order' => 'integer',
    ];

    public function scopeActive($q)
    {
        return $q->where('is_active', true)->orderBy('sort_order')->orderBy('id');
    }

    /** Localised value of a field ("title" → title_ar/he/en). */
    public function text(string $field): string
    {
        $locale = app()->getLocale();
        return (string) (
            $this->{"{$field}_{$locale}"}
            ?: $this->{"{$field}_ar"}
            ?: ''
        );
    }

    /** Public URL of the background image, with a sensible fallback. */
    public function imageUrl(): string
    {
        if (! $this->image) {
            return asset('images/banner.png');
        }
        return str_starts_with($this->image, 'http')
            ? $this->image
            : asset('storage/' . $this->image);
    }
}
