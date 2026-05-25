<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class FailedLogin extends Model
{
    protected $fillable = [
        'email', 'ip', 'user_agent', 'source', 'reason', 'attempted_at',
    ];

    protected $casts = [
        'attempted_at' => 'datetime',
    ];

    /** Quick helper to log an attempt. */
    public static function record(Request $request, string $email = null, string $source = 'storefront', string $reason = 'invalid_credentials'): void
    {
        try {
            static::create([
                'email'        => $email,
                'ip'           => $request->ip(),
                'user_agent'   => mb_substr((string) $request->userAgent(), 0, 500),
                'source'       => $source,
                'reason'       => $reason,
                'attempted_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // never let logging break the login flow
            report($e);
        }
    }
}
