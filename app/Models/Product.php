<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name_ar', 'name_he', 'name_en', 'slug', 'description_ar', 'description_he', 'description_en',
        'short_description', 'category_id', 'brand_id', 'price', 'compare_price',
        'cost_price', 'sku', 'stock_quantity', 'low_stock_alert', 'main_image',
        'weight', 'is_active', 'is_featured', 'is_new', 'is_published', 'track_quantity',
        'allow_backorder', 'views_count', 'sales_count', 'rating_avg',
        'rating_count', 'reviews_count', 'meta_title', 'meta_description',
    ];

    protected $casts = [
        'price'          => 'decimal:2',
        'compare_price'  => 'decimal:2',
        'cost_price'     => 'decimal:2',
        'is_active'      => 'boolean',
        'is_featured'    => 'boolean',
        'is_new'         => 'boolean',
        'track_quantity' => 'boolean',
        'allow_backorder'=> 'boolean',
    ];

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->slug ??= Str::slug($m->name_ar));
    }

    /* ── Relations ── */
    public function category(): BelongsTo  { return $this->belongsTo(Category::class); }
    public function brand(): BelongsTo     { return $this->belongsTo(Brand::class); }
    public function images(): HasMany      { return $this->hasMany(ProductImage::class)->orderBy('sort_order'); }
    public function variants(): HasMany    { return $this->hasMany(ProductVariant::class)->orderBy('sort_order'); }
    public function reviews(): HasMany     { return $this->hasMany(Review::class)->where('is_approved', true); }
    public function orderItems(): HasMany  { return $this->hasMany(OrderItem::class); }

    /* ── Accessors ── */
    public function getNameAttribute(): string
    {
        $locale = app()->getLocale();
        return match($locale) {
            'he'    => $this->name_he ?: $this->name_ar,
            'en'    => $this->name_en ?: $this->name_ar,
            default => $this->name_ar,
        };
    }

    public function getDiscountPercentAttribute(): ?int
    {
        if ($this->compare_price && $this->compare_price > $this->price) {
            return round((1 - $this->price / $this->compare_price) * 100);
        }
        return null;
    }

    public function getIsOnSaleAttribute(): bool
    {
        return $this->compare_price && $this->compare_price > $this->price;
    }

    public function getIsInStockAttribute(): bool
    {
        if (!$this->track_quantity) return true;
        return $this->stock_quantity > 0 || $this->allow_backorder;
    }

    public function getIsLowStockAttribute(): bool
    {
        return $this->track_quantity
            && $this->stock_quantity > 0
            && $this->stock_quantity <= $this->low_stock_alert;
    }

    /* ── Scopes ── */
    public function scopeActive($q)   { return $q->where('is_active', true); }
    public function scopeFeatured($q) { return $q->where('is_featured', true); }
    public function scopeInStock($q)  { return $q->where('stock_quantity', '>', 0); }
}
