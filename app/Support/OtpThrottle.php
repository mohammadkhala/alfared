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
    /**
     * Limits per delivery channel.
     *
     * WhatsApp is strict because every send risks getting the sender number
     * banned. E-mail carries no such cost, so it only needs enough of a limit
     * to stop abuse.
     */
    private const LIMITS = [
        'whatsapp' => ['max' => 2,  'window' => 600, 'cooldown' => 60],
        'email'    => ['max' => 6,  'window' => 600, 'cooldown' => 20],
    ];

    /** Kept for backwards compatibility with existing call sites. */
    public const MAX_SENDS = 2;
    public const WINDOW    = 600;
    public const COOLDOWN  = 60;

    /**
     * Seconds the caller must wait before another OTP may be sent.
     * Returns 0 when sending is allowed.
     */
    public static function retryAfter(string $phone, string $channel = 'whatsapp'): int
    {
        $limits = self::limits($channel);

        if (RateLimiter::tooManyAttempts(self::cooldownKey($phone, $channel), 1)) {
            return RateLimiter::availableIn(self::cooldownKey($phone, $channel));
        }

        if (RateLimiter::tooManyAttempts(self::windowKey($phone, $channel), $limits['max'])) {
            return RateLimiter::availableIn(self::windowKey($phone, $channel));
        }

        return 0;
    }

    /**
     * Record an OTP send. Call this right before handing the message to the
     * provider — a failed delivery still consumed a send.
     */
    public static function record(string $phone, string $channel = 'whatsapp'): void
    {
        $limits = self::limits($channel);
        RateLimiter::hit(self::windowKey($phone, $channel), $limits['window']);
        RateLimiter::hit(self::cooldownKey($phone, $channel), $limits['cooldown']);
    }

    /** Reset the limits for a phone (e.g. after a successful verification). */
    public static function clear(string $phone): void
    {
        foreach (array_keys(self::LIMITS) as $channel) {
            RateLimiter::clear(self::windowKey($phone, $channel));
            RateLimiter::clear(self::cooldownKey($phone, $channel));
        }
    }

    private static function limits(string $channel): array
    {
        return self::LIMITS[$channel] ?? self::LIMITS['whatsapp'];
    }

    /** Human-readable wait message in Arabic. */
    public static function message(int $seconds): string
    {
        if ($seconds >= 60) {
            $minutes = (int) ceil($seconds / 60);
            return "تم إرسال عدة رموز مؤخراً. يرجى الانتظار {$minutes} دقيقة ثم المحاولة مجدداً.";
        }

        return "يرجى الانتظار {$seconds} ثانية قبل طلب رمز جديد.";
    }

    private static function windowKey(string $phone, string $channel = 'whatsapp'): string
    {
        return "otp:win:{$channel}:".self::normalize($phone);
    }

    private static function cooldownKey(string $phone, string $channel = 'whatsapp'): string
    {
        return "otp:cool:{$channel}:".self::normalize($phone);
    }

    /** Digits only, so "+970 59..." and "97059..." share one bucket. */
    private static function normalize(string $phone): string
    {
        return preg_replace('/\D/', '', $phone) ?: 'unknown';
    }
}
