<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

class OtpCode extends Model
{
    use Prunable;

    protected $fillable = ['phone', 'code', 'expires_at', 'used'];

    protected $casts = [
        'expires_at' => 'datetime',
        'used'       => 'boolean',
    ];

    public function isValid(): bool
    {
        return ! $this->used && $this->expires_at->isFuture();
    }

    // Auto-delete expired OTPs older than 10 minutes (runs via model:prune)
    public function prunable()
    {
        return static::where('expires_at', '<', now()->subMinutes(10));
    }
}
