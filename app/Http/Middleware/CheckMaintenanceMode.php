<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckMaintenanceMode
{
    public function handle(Request $request, Closure $next): Response
    {
        // Always allow: admin panel, login, assets
        if (
            $request->is('admin*') ||
            $request->is('login*') ||
            $request->is('register*') ||
            $request->is('api*') ||
            $request->is('up')
        ) {
            return $next($request);
        }

        // Check DB setting (cached in memory for this request)
        try {
            $isOn = Setting::get('maintenance_mode', '0');
        } catch (\Throwable) {
            return $next($request); // DB not ready yet
        }

        if ($isOn === '1') {
            $type    = Setting::get('maintenance_type', 'maintenance');
            $title   = Setting::get('maintenance_title', '');
            $message = Setting::get('maintenance_message', '');
            $launch  = Setting::get('maintenance_launch_date', '');
            $wa      = Setting::get('maintenance_whatsapp', '');

            return response()->view('maintenance', compact('type','title','message','launch','wa'), 503);
        }

        return $next($request);
    }
}
