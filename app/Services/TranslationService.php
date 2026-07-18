<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Machine translation using Google Translate (unofficial endpoint, no key needed)
 * as primary, with MyMemory as fallback.
 */
class TranslationService
{
    // Unofficial Google Translate endpoint — no API key, no daily limit
    protected string $googleEndpoint = 'https://translate.googleapis.com/translate_a/single';

    // MyMemory fallback
    protected string $myMemoryEndpoint = 'https://api.mymemory.translated.net/get';

    public function translate(string $text, string $from = 'ar', string $to = 'en'): string
    {
        $text = trim($text);
        if ($text === '') return '';

        if (mb_strlen($text) > 4000) {
            return $this->translateChunked($text, $from, $to);
        }

        return $this->callGoogle($text, $from, $to)
            ?: $this->callMyMemory($text, $from, $to);
    }

    /**
     * Translates many short strings in one request by sending them newline
     * separated — thousands of place names one-by-one would take far too long.
     * Falls back to per-item translation if the reply doesn't line up, so a
     * mangled batch can never shift names onto the wrong rows.
     *
     * @param  array<int,string>  $texts
     * @return array<int,string>  same order as $texts
     */
    public function translateBatch(array $texts, string $from = 'ar', string $to = 'en'): array
    {
        if (empty($texts)) return [];

        $clean = array_map(fn ($t) => trim(str_replace("\n", ' ', (string) $t)), $texts);

        $result = $this->callGoogle(implode("\n", $clean), $from, $to);

        if ($result !== '') {
            $lines = array_map('trim', explode("\n", trim($result)));
            if (count($lines) === count($clean)) {
                return $lines;
            }
        }

        return array_map(fn ($t) => $this->translate($t, $from, $to), $clean);
    }

    public function translateHtml(string $html, string $from = 'ar', string $to = 'en'): string
    {
        $plain = trim(strip_tags($html));
        if ($plain === '') return '';
        $translated = $this->translate($plain, $from, $to);
        return $translated !== '' ? '<p>' . htmlspecialchars($translated, ENT_QUOTES, 'UTF-8') . '</p>' : '';
    }

    // ── Google Translate (unofficial) ────────────────────────────────────────

    protected function callGoogle(string $text, string $from, string $to): string
    {
        try {
            $response = Http::timeout(15)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (compatible; alfared-translate/1.0)',
                ])
                ->get($this->googleEndpoint, [
                    'client' => 'gtx',
                    'sl'     => $from,
                    'tl'     => $to,
                    'dt'     => 't',
                    'q'      => $text,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                // Response: [[["translated","original",...],...],...]
                if (isset($data[0]) && is_array($data[0])) {
                    $parts = array_map(fn($s) => $s[0] ?? '', $data[0]);
                    $result = implode('', array_filter($parts));
                    if ($result !== '') return $result;
                }
            }
        } catch (\Throwable $e) {
            Log::warning("Google Translate ({$from}→{$to}): " . $e->getMessage());
        }

        return '';
    }

    // ── MyMemory fallback ────────────────────────────────────────────────────

    protected function callMyMemory(string $text, string $from, string $to): string
    {
        // MyMemory max ~500 chars per call
        if (mb_strlen($text) > 450) return '';

        try {
            $params = [
                'q'        => $text,
                'langpair' => "{$from}|{$to}",
            ];
            $email = env('MYMEMORY_EMAIL');
            if ($email) $params['de'] = $email;

            $response = Http::timeout(12)->get($this->myMemoryEndpoint, $params);

            if ($response->successful()) {
                $data   = $response->json();
                $status = $data['responseStatus'] ?? 0;
                if ($status === 200 || $status === '200') {
                    return $data['responseData']['translatedText'] ?? '';
                }
            }
        } catch (\Throwable $e) {
            Log::warning("MyMemory ({$from}→{$to}): " . $e->getMessage());
        }

        return '';
    }

    // ── Chunked for long text ────────────────────────────────────────────────

    protected function translateChunked(string $text, string $from, string $to): string
    {
        $sentences = preg_split('/(?<=[.!?\n])\s*/u', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chunks    = [];
        $current   = '';

        foreach ($sentences as $sentence) {
            if (mb_strlen($current) + mb_strlen($sentence) < 4000) {
                $current .= ($current ? ' ' : '') . $sentence;
            } else {
                if ($current !== '') $chunks[] = $current;
                $current = $sentence;
            }
        }
        if ($current !== '') $chunks[] = $current;

        $translated = [];
        foreach ($chunks as $chunk) {
            $result = $this->callGoogle($chunk, $from, $to)
                ?: $this->callMyMemory($chunk, $from, $to);
            if ($result === '') return '';
            $translated[] = $result;
            usleep(200_000); // 0.2s between chunks
        }

        return implode(' ', $translated);
    }
}
