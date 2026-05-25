<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale
{
    public function handle(Request $request, Closure $next)
    {
        $locale = session('locale', config('app.locale', 'ar'));

        // Only allow supported locales
        if (!in_array($locale, ['ar', 'he', 'en'])) {
            $locale = 'ar';
        }

        App::setLocale($locale);

        return $next($request);
    }
}
