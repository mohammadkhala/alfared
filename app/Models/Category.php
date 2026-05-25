<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Category extends Model
{
    protected $fillable = [
        'name_ar', 'name_he', 'name_en', 'slug', 'description_ar',
        'image', 'icon', 'color', 'parent_id', 'sort_order',
        'is_active', 'show_in_menu',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'show_in_menu' => 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->slug ??= Str::slug($m->name_ar));
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'he'    => $this->name_he ?: $this->name_ar,
            'en'    => $this->name_en ?: $this->name_ar,
            default => $this->name_ar,
        };
    }
}
