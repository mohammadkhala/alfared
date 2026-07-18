<?php

namespace App\Support;

use App\Models\Setting;

/**
 * Remote switch for the WhatsApp OTP step on signup.
 *
 * Turning this off in Site Settings lets customers register immediately —
 * useful when the sender WhatsApp number gets banned, since it keeps signups
 * working on both the website and the mobile app without shipping a build.
 */
class PhoneVerification
{
    public static function enabled(): bool
    {
        try {
            return Setting::get('phone_verification_enabled', '1') === '1';
        } catch (\Throwable) {
            // Settings unavailable — fail open so registration keeps working.
            return false;
        }
    }
}
