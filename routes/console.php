<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune expired OTP codes every hour
Schedule::command('model:prune', ['--model' => [\App\Models\OtpCode::class]])->hourly();

// Drain queued mail. Mail::queue() writes to the jobs table and waits for a
// worker; shared hosting has no long-running process, so without this every
// order confirmation sat in the queue forever. --stop-when-empty exits once
// the table is clear instead of holding the process open.
Schedule::command('queue:work --stop-when-empty --tries=3 --max-time=50')
    ->everyMinute()
    ->withoutOverlapping();

// Pull RoadFN shipment status back onto open orders
Schedule::command('roadfn:sync-shipments')->everyFifteenMinutes();
