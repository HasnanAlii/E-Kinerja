<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('notifications:clean-old --days=5')
    ->dailyAt('02:00');


Schedule::command('kehadiran:generate-alpha')->dailyAt('00:00');
    
// Schedule::command('notifications:test-clean')->everyMinute();
