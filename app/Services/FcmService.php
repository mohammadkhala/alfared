<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Minimal Firebase Cloud Messaging sender (legacy HTTP API with server key).
 * Set FCM_SERVER_KEY in your .env (obtained from Firebase Console →
 * Project Settings → Cloud Messaging → Server key).
 *
 * For a single device or an array of tokens.
 */
class FcmService
{
    protected static function serverKey(): ?string
    {
        return config('services.fcm.server_key') ?? env('FCM_SERVER_KEY');
    }

    /**
     * Send a notification to one user (uses their stored fcm_token).
     * Returns true on dispatched, false if no token / no key.
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (! $user->fcm_token) return false;
        return self::sendToTokens([$user->fcm_token], $title, $body, $data);
    }

    public static function sendToTokens(array $tokens, string $title, string $body, array $data = []): bool
    {
        $key = self::serverKey();
        if (! $key) {
            Log::info('FCM not configured (no FCM_SERVER_KEY) — notification skipped');
            return false;
        }
        $tokens = array_values(array_filter($tokens));
        if (empty($tokens)) return false;

        try {
            $payload = [
                'registration_ids' => $tokens,
                'priority'         => 'high',
                'notification'     => [
                    'title' => $title,
                    'body'  => $body,
                    'sound' => 'default',
                    'badge' => 1,
                ],
                'data' => array_merge(['title' => $title, 'body' => $body], $data),
            ];

            $resp = Http::withHeaders([
                'Content-Type'  => 'application/json',
                'Authorization' => 'key=' . $key,
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            return $resp->successful();
        } catch (\Throwable $e) {
            Log::warning('FCM send failed: ' . $e->getMessage());
            return false;
        }
    }
}
