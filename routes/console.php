<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Refresh FX reference rates once a day (the provider updates ~daily).
Schedule::command('fx:refresh')->dailyAt('04:30')->withoutOverlapping();
