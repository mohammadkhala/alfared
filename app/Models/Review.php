<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    protected $fillable = [
        'product_id', 'user_id', 'order_id', 'reviewer_name',
        'rating', 'comment', 'is_approved', 'is_verified',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_verified' => 'boolean',
    ];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }
    public function order(): BelongsTo   { return $this->belongsTo(Order::class); }
}
