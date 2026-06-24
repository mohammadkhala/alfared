<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Free machine translation via MyMemory API
 * Limit: ~10,000 chars / day without registration
 * Docs: https://mymemory.translated.net/doc/spec.php
 */
class TranslationService
{
    protected string $endpoint = 'https://api.mymemory.translated.net/get';

    /**
     * Translate plain text.
     *
     * @param  string  $text  Plain text (no HTML)
     * @param  string  $from  e.g. 'ar'
     * @param  string  $to    e.g. 'en' | 'he'
     */
    public function translate(string $text, string $from = 'ar', string $to = 'en'): string
    {
        $text = trim($text);
        if ($text === '') return '';

        // MyMemory handles up to ~500 chars per request reliably
        // For longer text, split into chunks
        if (mb_strlen($text) > 450) {
            return $this->translateChunked($text, $from, $to);
        }

        return $this->callApi($text, $from, $to);
    }

    /**
     * Translate HTML content: strip tags → translate → return <p> wrapped plain text.
     */
    public function translateHtml(string $html, string $from = 'ar', string $to = 'en'): string
    {
        $plain = trim(strip_tags($html));
        if ($plain === '') return '';
        $translated = $this->translate($plain, $from, $to);
        return $translated !== '' ? '<p>' . htmlspecialchars($translated, ENT_QUOTES, 'UTF-8') . '</p>' : '';
    }

    // ── internals ─────────────────────────────────────────────────────────────

    protected function callApi(string $text, string $from, string $to): string
    {
        try {
            $params = [
                'q'        => $text,
                'langpair' => "{$from}|{$to}",
            ];
            // Registered email raises daily limit from 10k to 50k chars
            $email = config('services.mymemory.email', env('MYMEMORY_EMAIL'));
            if ($email) $params['de'] = $email;

            $response = Http::timeout(15)->get($this->endpoint, $params);

            if ($response->successful()) {
                $data   = $response->json();
                $status = $data['responseStatus'] ?? 0;
                if ($status === 200 || $status === '200') {
                    return $data['responseData']['translatedText'] ?? '';
                }
            }
        } catch (\Throwable $e) {
            Log::warning("TranslationService ({$from}→{$to}) error: " . $e->getMessage());
        }

        return '';
    }

    protected function translateChunked(string $text, string $from, string $to): string
    {
        // Split on sentence endings (. ! ? \n) keeping delimiter
        $sentences = preg_split('/(?<=[.!?\n])\s*/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chunks    = [];
        $current   = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($current) + mb_strlen($sentence) < 450) {
                $current .= ($current ? ' ' : '') . $sentence;
            } else {
                if ($current !== '') $chunks[] = $current;
                $current = $sentence;
            }
        }
        if ($current !== '') $chunks[] = $current;

        $translated = [];
        foreach ($chunks as $chunk) {
            $result = $this->callApi($chunk, $from, $to);
            if ($result === '') return '';   // abort on error
            $translated[] = $result;
            usleep(300_000); // 0.3 s between chunks to respect rate limit
        }

        return implode(' ', $translated);
    }
}
