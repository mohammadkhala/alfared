<?php

namespace App\Support;

use Illuminate\Support\Facades\RateLimiter;

/**
 * Per-phone-number limit on outgoing WhatsApp OTPs.
 *
 * Keyed by the destination phone (not the client IP) so switching networks
 * cannot bypass it, and cache-backed so it survives the `otp_codes` cleanup
 * that each send performs. This protects the sender WhatsApp number from
 * being flagged as spam and banned.
 */
class OtpThrottle
{
    /** Max OTPs per phone inside WINDOW. */
    public const MAX_SENDS = 2;

    /** Rolling window, in seconds. */
    public const WINDOW = 600; // 10 minutes

    /** Minimum gap between two OTPs to the same phone, in seconds. */
    public const COOLDOWN = 60;

    /**
     * Seconds the caller must wait before another OTP may be sent.
     * Returns 0 when sending is allowed.
     */
    public static function retryAfter(string $phone): int
    {
        if (RateLimiter::tooManyAttempts(self::cooldownKey($phone), 1)) {
            return RateLimiter::availableIn(self::cooldownKey($phone));
        }

        if (RateLimiter::tooManyAttempts(self::windowKey($phone), self::MAX_SENDS)) {
            return RateLimiter::availableIn(self::windowKey($phone));
        }

        return 0;
    }

    /**
     * Record an OTP send. Call this right before handing the message to
     * WhatsApp — a failed delivery still consumed a send on the provider.
     */
    public static function record(string $phone): void
    {
        RateLimiter::hit(self::windowKey($phone), self::WINDOW);
        RateLimiter::hit(self::cooldownKey($phone), self::COOLDOWN);
    }

    /** Reset the limits for a phone (e.g. after a successful verification). */
    public static function clear(string $phone): void
    {
        RateLimiter::clear(self::windowKey($phone));
        RateLimiter::clear(self::cooldownKey($phone));
    }

    /** Human-readable wait message in Arabic. */
    public static function message(int $seconds): string
    {
        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);
            return "تم إرسال رمزين مؤخراً لهذا الرقم. يرجى الانتظار {$minutes} دقيقة ثم المحاولة مجدداً.";
        }

        return "يرجى الانتظار {$seconds} ثانية قبل طلب رمز جديد.";
    }

    private static function windowKey(string $phone): string
    {
        return 'otp:win:'.self::normalize($phone);
    }

    private static function cooldownKey(string $phone): string
    {
        return 'otp:cool:'.self::normalize($phone);
    }

    /** Digits only, so "+970 59..." and "97059..." share one bucket. */
    private static function normalize(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?: 'unknown';
    }
}
