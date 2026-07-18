<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaSenderService
{
    private string $apiKey;
    private string $baseUrl = 'https://www.wasenderapi.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.wasender.api_key') ?? '';
    }

    /**
     * Send any WhatsApp message.
     */
    public function send(string $phone, string $message): bool
    {
        if (empty($this->apiKey)) {
            Log::error('WaSender: API key not configured');
            return false;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/send-message", [
                    'to'   => $phone,
                    'text' => $message,
                ]);

            if (! $response->successful()) {
                Log::warning('WaSender: Failed to send message', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('WaSender: Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Daily ceiling for OTPs from the sender number. A sudden burst is the
     * fastest way to get the account flagged, so we stop before that happens.
     */
    private const DAILY_CAP = 200;

    public function sendOtp(string $phone, string $code): bool
    {
        if (empty($this->apiKey)) {
            Log::error('WaSender: API key not configured');
            return false;
        }

        if (! $this->underDailyCap()) {
            Log::critical('WaSender: daily OTP cap reached — refusing to send', [
                'cap' => self::DAILY_CAP,
            ]);
            return false;
        }

        $message = $this->otpMessage($code);

        // Perfectly uniform timing looks automated; add a little human jitter.
        usleep(random_int(400, 1500) * 1000);

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(10)
                ->post("{$this->baseUrl}/send-message", [
                    'to'   => $phone,
                    'text' => $message,
                ]);

            if (! $response->successful()) {
                Log::warning('WaSender: Failed to send OTP', [
                    'phone'  => $phone,
                    'status' => $response->status(),
                    'body'   => $response->body(),
                ]);
                return false;
            }

            return true;

        } catch (\Throwable $e) {
            Log::error('WaSender: Exception', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Builds the OTP text.
     *
     * Every message being byte-identical apart from six digits is a strong
     * automation signal, so we rotate between natural-sounding variants and
     * keep emoji/markdown light.
     */
    private function otpMessage(string $code): string
    {
        $variants = [
            "رمز التحقق الخاص بك في متجر أبناء الفريد هو: {$code}\nصالح لمدة 5 دقائق. لا تشاركه مع أحد.",
            "أهلاً بك 👋\nرمز الدخول: {$code}\nينتهي خلال 5 دقائق، ويُرجى عدم مشاركته.",
            "متجر أبناء الفريد\nرمز التأكيد: {$code}\nالرمز صالح 5 دقائق فقط.",
            "تم طلب رمز تحقق لحسابك.\nالرمز: {$code}\nصلاحيته 5 دقائق. إذا لم تطلبه، تجاهل الرسالة.",
            "رمزك: {$code}\nاستخدمه خلال 5 دقائق لإتمام تسجيل الدخول في متجر أبناء الفريد.",
        ];

        return $variants[array_rand($variants)];
    }

    /** Increments and checks today's send counter for the sender number. */
    private function underDailyCap(): bool
    {
        $key   = 'wa:otp:sent:'.now()->toDateString();
        $count = (int) Cache::get($key, 0);

        if ($count >= self::DAILY_CAP) {
            return false;
        }

        Cache::put($key, $count + 1, now()->endOfDay());

        return true;
    }
}
