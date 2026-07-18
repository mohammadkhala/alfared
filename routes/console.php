<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Prune expired OTP codes every hour
Schedule::command('model:prune', ['--model' => [\App\Models\OtpCode::class]])->hourly();

// Pull RoadFN shipment status back onto open orders
Schedule::command('roadfn:sync-shipments')->everyFifteenMinutes();
