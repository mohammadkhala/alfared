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
        // Always allow these paths through
        if (
            $request->is('admin*') ||
            $request->is('admin-tools*') ||
            $request->is('admin-api*') ||
            $request->is('maintenance-preview*') ||
            $request->is('lang/*') ||
            $request->is('up') ||
            $request->is('api*')
        ) {
            return $next($request);
        }

        // Read setting safely
        try {
            $isOn = Setting::where('key', 'maintenance_mode')->value('value');
        } catch (\Throwable) {
            return $next($request); // DB not ready
        }

        if ($isOn === '1') {
            $type    = Setting::where('key', 'maintenance_type')->value('value') ?? 'maintenance';
            $title   = Setting::where('key', 'maintenance_title')->value('value') ?? '';
            $message = Setting::where('key', 'maintenance_message')->value('value') ?? '';
            $launch  = Setting::where('key', 'maintenance_launch_date')->value('value') ?? '';
            $wa      = Setting::where('key', 'maintenance_whatsapp')->value('value') ?? '';

            return response()->view('maintenance', compact('type','title','message','launch','wa'), 503);
        }

        return $next($request);
    }
}
