<?php

namespace App\Services;

/**
 * Minimal user-agent parser tuned for our needs:
 *   - detects our mobile app via a custom UA token "AlfaredApp"
 *   - bots, mobile/tablet/desktop, top browsers, top OS.
 *
 * Returns ['device_type', 'browser', 'os'].
 */
class UserAgentParser
{
    /** Custom UA token sent by the mobile app — set this in the app's HTTP client. */
    public const APP_UA_TOKEN = 'AlfaredApp';

    public static function parse(?string $ua, ?string $appHeader = null): array
    {
        $ua = $ua ?: '';

        // 1) Explicit app indicators (token or X-App-Source header)
        if (str_contains($ua, self::APP_UA_TOKEN)
            || str_contains($ua, 'AlfaredMobile')
            || in_array(strtolower((string)$appHeader), ['app', 'mobile-app', 'flutter', 'reactnative'], true)
        ) {
            return ['app', self::detectAppPlatform($ua), self::detectOS($ua)];
        }

        // 2) Bots
        if (self::isBot($ua)) {
            return ['bot', self::detectBot($ua), 'Bot'];
        }

        // 3) Tablet / Mobile / Desktop
        $lower = strtolower($ua);
        $isTablet = str_contains($lower, 'ipad')
            || (str_contains($lower, 'android') && ! str_contains($lower, 'mobile'))
            || str_contains($lower, 'tablet');
        $isMobile = ! $isTablet
            && (str_contains($lower, 'iphone')
                || str_contains($lower, 'android')
                || str_contains($lower, 'mobile')
                || str_contains($lower, 'opera mini'));

        $deviceType = $isTablet ? 'tablet' : ($isMobile ? 'mobile' : 'desktop');

        return [$deviceType, self::detectBrowser($ua), self::detectOS($ua)];
    }

    public static function generateVisitorId(string $sessionId, string $ip): string
    {
        // Hash so we don't store raw session IDs in DB
        return substr(hash('sha256', $sessionId . '|' . $ip), 0, 32);
    }

    protected static function isBot(string $ua): bool
    {
        $patterns = [
            'bot', 'spider', 'crawler', 'slurp', 'mediapartners',
            'facebook', 'twitterbot', 'whatsapp', 'telegram',
            'discord', 'embedly', 'pinterest', 'yandex', 'baidu',
            'duckduckgo', 'bingbot', 'googlebot', 'applebot',
        ];
        $low = strtolower($ua);
        foreach ($patterns as $p) {
            if (str_contains($low, $p)) return true;
        }
        return false;
    }

    protected static function detectBot(string $ua): string
    {
        $known = ['Googlebot','Bingbot','DuckDuckBot','YandexBot','Baiduspider','facebookexternalhit','Twitterbot','WhatsApp','TelegramBot','AppleBot'];
        foreach ($known as $b) {
            if (str_contains($ua, $b)) return $b;
        }
        return 'Bot';
    }

    protected static function detectBrowser(string $ua): string
    {
        // order matters - more specific first
        if (str_contains($ua, 'Edg/'))          return 'Edge';
        if (str_contains($ua, 'OPR/') || str_contains($ua, 'Opera')) return 'Opera';
        if (str_contains($ua, 'SamsungBrowser')) return 'Samsung Browser';
        if (str_contains($ua, 'UCBrowser'))     return 'UC Browser';
        if (str_contains($ua, 'Firefox'))       return 'Firefox';
        if (str_contains($ua, 'CriOS') || str_contains($ua, 'Chrome')) return 'Chrome';
        if (str_contains($ua, 'Safari'))        return 'Safari';
        if (str_contains($ua, 'MSIE') || str_contains($ua, 'Trident')) return 'Internet Explorer';
        return 'Other';
    }

    protected static function detectOS(string $ua): string
    {
        if (str_contains($ua, 'Windows NT 10')) return 'Windows 10/11';
        if (str_contains($ua, 'Windows'))       return 'Windows';
        if (str_contains($ua, 'Mac OS X') || str_contains($ua, 'Macintosh')) return 'macOS';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') || str_contains($ua, 'iPod')) return 'iOS';
        if (str_contains($ua, 'Android'))       return 'Android';
        if (str_contains($ua, 'Linux'))         return 'Linux';
        if (str_contains($ua, 'CrOS'))          return 'ChromeOS';
        return 'Other';
    }

    protected static function detectAppPlatform(string $ua): string
    {
        if (str_contains(strtolower($ua), 'ios') || str_contains($ua, 'iPhone')) return 'iOS App';
        if (str_contains(strtolower($ua), 'android')) return 'Android App';
        return 'Mobile App';
    }
}
