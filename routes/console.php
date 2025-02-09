<?php

use App\Console\Commands\ImportNewCars;
use App\Console\Commands\ImportOldCars;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::command('app:import-new-cars')->everyMinute()->runInBackground();
Schedule::command('app:import-old-cars')->everyMinute()->runInBackground();
