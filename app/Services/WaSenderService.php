<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WaSenderService
{
    private string $apiKey;
    private string $baseUrl = 'https://www.wasenderapi.com/api';

    public function __construct()
    {
        $this->apiKey = config('services.wasender.api_key', '');
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

    public function sendOtp(string $phone, string $code): bool
    {
        if (empty($this->apiKey)) {
            Log::error('WaSender: API key not configured');
            return false;
        }

        $message = "🔐 *رمز التحقق الخاص بك*\n\n" .
                   "تطبيق أبناء الفريد التجارية\n\n" .
                   "رمزك هو: *{$code}*\n\n" .
                   "⏱ صالح لمدة 5 دقائق فقط.\n" .
                   "لا تشارك هذا الرمز مع أي شخص.";

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
}
