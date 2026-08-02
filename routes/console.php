<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// NOTE: this host has proc_open disabled, so Schedule::command() — which runs
// each task as a subprocess — throws every time the scheduler fires. Everything
// below therefore runs in-process via Schedule::call(fn () => Artisan::call()),
// which needs no subprocess. Don't switch these back to Schedule::command().

// Prune expired OTP codes every hour.
Schedule::call(fn () => Artisan::call('model:prune', [
    '--model' => [\App\Models\OtpCode::class],
]))->hourly();

// Drain queued mail. Mail::queue() writes to the jobs table and waits for a
// worker; this host runs none, so without this every order confirmation would
// sit in the queue forever. --stop-when-empty exits once the table is clear.
// A closure event has no command string to derive a mutex key from, so
// withoutOverlapping() needs an explicit name.
Schedule::call(fn () => Artisan::call('queue:work', [
    '--stop-when-empty' => true,
    '--tries' => 3,
    '--max-time' => 50,
]))->name('drain-queued-mail')->everyMinute()->withoutOverlapping();

// Pull RoadFN shipment status back onto open orders.
Schedule::call(fn () => Artisan::call('roadfn:sync-shipments'))
    ->everyFifteenMinutes();

// Nightly database backup, keeping the last 7 days. Pure PHP (no mysqldump —
// this host disables proc_open); writes to storage/app/private/backups.
Schedule::call(fn () => Artisan::call('backup:database', ['--keep' => 7]))
    ->dailyAt('03:30')->name('db-backup')->withoutOverlapping();

// Weekly full backup (database + uploaded images) in one zip, keeping the
// last 4. Sunday before the nightly DB backup so they don't overlap.
Schedule::call(fn () => Artisan::call('backup:full', ['--keep' => 4]))
    ->weeklyOn(0, '03:00')->name('full-backup')->withoutOverlapping();
