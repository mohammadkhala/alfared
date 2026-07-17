<?php

namespace App\Support;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

/**
 * Resolves the store logo / favicon that the admin uploads in Site Settings,
 * falling back to the bundled default image when nothing is set.
 *
 * Cached because the layout renders these on every page request.
 */
class SiteBranding
{
    private const CACHE_KEY = 'site:branding';
    private const TTL       = 3600;

    /** URL of the store logo (navbar, social preview). */
    public static function logo(): string
    {
        return self::url(self::values()['store_logo'] ?? null);
    }

    /** URL of the favicon; falls back to the logo, then the default image. */
    public static function favicon(): string
    {
        $values = self::values();
        return self::url($values['store_favicon'] ?? $values['store_logo'] ?? null);
    }

    /** Called after Site Settings are saved so changes show immediately. */
    public static function forget(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    private static function values(): array
    {
        try {
            return Cache::remember(self::CACHE_KEY, self::TTL, fn () =>
                Setting::whereIn('key', ['store_logo', 'store_favicon'])
                    ->pluck('value', 'key')
                    ->filter()
                    ->toArray()
            );
        } catch (\Throwable) {
            // DB/cache unavailable — fall back to the bundled logo rather than
            // breaking every page (error pages use this layout too).
            return [];
        }
    }

    private static function url(?string $path): string
    {
        if (! $path) {
            return asset('images/logo.png');
        }
        if (str_starts_with($path, 'http')) {
            return $path;
        }
        return asset('storage/'.ltrim($path, '/'));
    }
}
