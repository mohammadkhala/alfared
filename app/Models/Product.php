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
        'short_description', 'short_description_en', 'short_description_he', 'category_id', 'brand_id', 'price', 'compare_price',
        'cost_price', 'sku', 'stock_quantity', 'low_stock_alert', 'main_image', 'video',
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

    // ── Video ────────────────────────────────────────────────────────────
    // `video` holds either an uploaded file path or a YouTube/Vimeo URL. A
    // hosted link costs no disk space, which matters on shared hosting.

    public function hasVideo(): bool
    {
        return filled($this->video);
    }

    /** True when the value is a YouTube/Vimeo link rather than an upload. */
    public function isEmbeddedVideo(): bool
    {
        return $this->hasVideo() && (bool) $this->embedUrl();
    }

    /** Player URL for a YouTube/Vimeo link, or null for an uploaded file. */
    public function embedUrl(): ?string
    {
        $v = trim((string) $this->video);
        if ($v === '' || ! str_starts_with($v, 'http')) {
            return null;
        }

        // youtu.be/ID  |  youtube.com/watch?v=ID  |  youtube.com/shorts/ID
        if (preg_match('~(?:youtu\.be/|youtube\.com/(?:watch\?v=|embed/|shorts/))([A-Za-z0-9_-]{6,})~', $v, $m)) {
            return 'https://www.youtube.com/embed/' . $m[1];
        }

        if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $v, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }

    /** Direct URL of an uploaded video file, or null when it is an embed. */
    public function videoUrl(): ?string
    {
        if (! $this->hasVideo() || $this->embedUrl()) {
            return null;
        }

        return str_starts_with($this->video, 'http')
            ? $this->video
            : asset('storage/' . $this->video);
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
