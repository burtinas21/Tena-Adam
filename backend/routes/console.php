<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
Schedule::command('mark:noshow-patients')
    ->everyFiveMinutes();
// Run every 5 minutes to catch all reminder windows: 24h, 1h, and 15-min
Schedule::command('appointments:send-reminders')->everyFiveMinutes();
Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
