<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * When the site is behind Cloudflare, Laravel sees Cloudflare's IP instead of
 * the real visitor. Cloudflare adds the real one in `CF-Connecting-IP`.
 *
 * This middleware overrides the request's "remote address" with that value
 * (and exposes the original Cloudflare IP via a custom header for debugging).
 */
class CloudflareRealIp
{
    public function handle(Request $request, Closure $next): Response
    {
        $realIp = $request->header('CF-Connecting-IP');
        if (! empty($realIp)) {
            $request->server->set('REMOTE_ADDR', $realIp);
        }

        // Country shortcut (Cloudflare adds CF-IPCountry)
        if ($country = $request->header('CF-IPCountry')) {
            $request->attributes->set('cf_country', $country);
        }

        return $next($request);
    }
}
