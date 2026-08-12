<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Schedule::command(
    'events:generate-recurring'
)->dailyAt('00:00');


Schedule::command(
    'schedule-plannings:close-and-clean'
)->monthlyOn(1, '00:00');

Schedule::command(
    'schedule-plannings:close-and-clean'
)->monthlyOn(16, '00:00');

Schedule::command(
    'schedule-plannings:autofill-schedule'
)->monthlyOn(1, '00:00');

Schedule::command(
    'schedule-plannings:autofill-schedule'
)->monthlyOn(16, '00:00');

Schedule::command(
    'employees:scheduled-deactivate'
)->dailyAt('00:00');