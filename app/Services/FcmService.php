<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Firebase Cloud Messaging — HTTP v1 API.
 *
 * Setup (one-time):
 *  1. Firebase Console → Project Settings → Service Accounts → Generate new private key
 *  2. Upload the downloaded JSON to storage/app/firebase-service-account.json
 *  3. Add to .env:
 *       FCM_PROJECT_ID=your-firebase-project-id
 *       FCM_SERVICE_ACCOUNT_PATH=/full/path/to/storage/app/firebase-service-account.json
 *
 * The class auto-generates & caches OAuth2 tokens (55-min TTL).
 */
class FcmService
{
    // ── Config helpers ────────────────────────────────────────────────────────

    protected static function serviceAccountPath(): ?string
    {
        // 1. Explicit config/env (works before config:cache)
        $path = config('services.fcm.service_account');
        if ($path && file_exists($path)) return $path;

        // 2. Default location — always checked regardless of config
        $default = storage_path('app/firebase-service-account.json');
        return file_exists($default) ? $default : null;
    }

    /** Reads project_id from the service account JSON — no config needed. */
    protected static function projectId(): ?string
    {
        // 1. Explicit config value
        $fromConfig = config('services.fcm.project_id');
        if ($fromConfig) return $fromConfig;

        // 2. Read directly from the service account JSON (field: project_id)
        $path = self::serviceAccountPath();
        if (! $path) return null;
        try {
            $sa = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
            return $sa['project_id'] ?? null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // ── OAuth2 token (cached 55 min) ──────────────────────────────────────────

    protected static function getAccessToken(): ?string
    {
        return Cache::remember('fcm_v1_access_token', 55 * 60, function () {
            return self::fetchAccessToken();
        });
    }

    protected static function fetchAccessToken(): ?string
    {
        $path = self::serviceAccountPath();
        if (! $path) {
            Log::warning('FCM: service account JSON not found.');
            return null;
        }

        try {
            $sa = json_decode(file_get_contents($path), true, 512, JSON_THROW_ON_ERROR);
        } catch (\Throwable $e) {
            Log::warning('FCM: invalid service account JSON — ' . $e->getMessage());
            return null;
        }

        // Build signed JWT
        $now    = time();
        $header = self::b64u(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $claims = self::b64u(json_encode([
            'iss'   => $sa['client_email'],
            'scope' => 'https://www.googleapis.com/auth/firebase.messaging',
            'aud'   => 'https://oauth2.googleapis.com/token',
            'iat'   => $now,
            'exp'   => $now + 3600,
        ]));

        $input      = "$header.$claims";
        $privateKey = openssl_pkey_get_private($sa['private_key']);
        openssl_sign($input, $signature, $privateKey, OPENSSL_ALGO_SHA256);
        $jwt = "$input." . self::b64u($signature);

        // Exchange JWT for access token
        $resp = Http::asForm()->post('https://oauth2.googleapis.com/token', [
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ]);

        if (! $resp->successful()) {
            Log::warning('FCM: token exchange failed — ' . $resp->body());
            return null;
        }

        return $resp->json('access_token');
    }

    protected static function b64u(string $data): string
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // ── Public API ────────────────────────────────────────────────────────────

    /**
     * Send a notification to a single user via their stored FCM token.
     */
    public static function sendToUser(User $user, string $title, string $body, array $data = []): bool
    {
        if (! $user->fcm_token) return false;
        return self::sendToTokens([$user->fcm_token], $title, $body, $data);
    }

    /**
     * Send to an array of FCM device tokens (v1 API — one request per token).
     */
    public static function sendToTokens(array $tokens, string $title, string $body, array $data = []): bool
    {
        $projectId = self::projectId();
        if (! $projectId) {
            Log::info('FCM: FCM_PROJECT_ID not configured — notification skipped.');
            return false;
        }

        $token = self::getAccessToken();
        if (! $token) {
            Log::warning('FCM: could not obtain OAuth2 access token.');
            return false;
        }

        $tokens = array_values(array_filter($tokens));
        if (empty($tokens)) return false;

        $endpoint = "https://fcm.googleapis.com/v1/projects/{$projectId}/messages:send";
        $success  = false;

        foreach ($tokens as $deviceToken) {
            try {
                $payload = [
                    'message' => [
                        'token'        => $deviceToken,
                        'notification' => [
                            'title' => $title,
                            'body'  => $body,
                        ],
                        // data fields MUST all be strings
                        'data' => array_map('strval', array_merge(
                            ['title' => $title, 'body' => $body],
                            $data
                        )),
                        'android' => [
                            'priority'     => 'high',
                            'notification' => [
                                'sound'      => 'default',
                                'channel_id' => 'orders_channel',
                            ],
                        ],
                        'apns' => [
                            'headers' => ['apns-priority' => '10'],
                            'payload' => [
                                'aps' => ['sound' => 'default', 'badge' => 1],
                            ],
                        ],
                    ],
                ];

                $resp = Http::withToken($token)
                    ->timeout(10)
                    ->post($endpoint, $payload);

                if ($resp->successful()) {
                    $success = true;
                } else {
                    Log::warning("FCM v1 failed for token …{$deviceToken}: " . $resp->body());
                    // Auto-clean stale/unregistered tokens from the database
                    $errorCode = $resp->json('error.details.0.errorCode') ?? '';
                    if (in_array($errorCode, ['UNREGISTERED', 'INVALID_ARGUMENT'])) {
                        \App\Models\User::where('fcm_token', $deviceToken)
                            ->update(['fcm_token' => null, 'device_type' => null]);
                        Log::info("FCM: cleared stale token for errorCode={$errorCode}");
                    }
                }
            } catch (\Throwable $e) {
                Log::warning('FCM v1 exception: ' . $e->getMessage());
            }
        }

        return $success;
    }
}
