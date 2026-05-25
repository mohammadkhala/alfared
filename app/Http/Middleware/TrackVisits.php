<?php

namespace App\Http\Middleware;

use App\Models\SiteVisit;
use App\Services\UserAgentParser;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Logs visits to the storefront — but runs the DB insert in `terminate()`,
 * which fires AFTER the response has been sent to the user. So tracking adds
 * effectively zero latency to the user's perceived page load.
 *
 * De-duplicates same-path hits per visitor within a 60-second window.
 */
class TrackVisits
{
    public function handle(Request $request, Closure $next)
    {
        return $next($request);
    }

    /**
     * Runs after the response is sent. The user has already received the HTML,
     * so any DB writes here happen "for free" from the user's perspective.
     */
    public function terminate(Request $request, $response): void
    {
        try {
            if (! $this->shouldTrack($request, $response)) {
                return;
            }

            $ua        = $request->userAgent() ?? '';
            $appHeader = $request->header('X-App-Source');
            [$device, $browser, $os] = UserAgentParser::parse($ua, $appHeader);

            $sessionId = $request->hasSession()
                ? $request->session()->getId()
                : ($request->ip() . '|' . $ua);
            $visitorId = UserAgentParser::generateVisitorId($sessionId, (string) $request->ip());

            // De-dup: same visitor + same path within 60s
            $cacheKey = 'visit:' . $visitorId . ':' . md5($request->path());
            if (Cache::has($cacheKey)) {
                return;
            }
            Cache::put($cacheKey, 1, 60);

            SiteVisit::create([
                'visitor_id' => $visitorId,
                'user_id'    => auth()->id(),
                'ip'         => $request->ip(),
                'device_type'=> $device,
                'browser'    => $browser,
                'os'         => $os,
                'url'        => mb_substr($request->fullUrl(), 0, 500),
                'path'       => mb_substr('/' . ltrim($request->path(), '/'), 0, 255),
                'referer'    => mb_substr((string) $request->headers->get('referer'), 0, 500) ?: null,
                'language'   => $request->getPreferredLanguage(['ar', 'he', 'en']) ?: app()->getLocale(),
                'user_agent' => $ua,
                'visited_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Never let tracking break anything — silently log only
            report($e);
        }
    }

    protected function shouldTrack(Request $request, $response): bool
    {
        if (! $request->isMethod('GET')) return false;
        if ($request->ajax() || $request->wantsJson()) return false;

        $status = method_exists($response, 'getStatusCode') ? $response->getStatusCode() : 200;
        if ($status < 200 || $status >= 300) return false;

        $path = ltrim($request->path(), '/');
        if ($path === 'admin' || str_starts_with($path, 'admin/')) return false;
        if (str_starts_with($path, 'admin-api') || str_starts_with($path, 'admin-tools')) return false;
        if (str_starts_with($path, 'livewire/') || $path === 'livewire') return false;

        $skipExact = ['storage', 'build', 'vendor', '_ignition',
                      'css', 'js', 'images', 'fonts', 'sounds',
                      'favicon.ico', 'robots.txt'];
        foreach ($skipExact as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) return false;
        }

        return true;
    }
}
