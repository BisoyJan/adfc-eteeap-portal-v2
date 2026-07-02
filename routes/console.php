<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

use App\Console\Commands\AnonymizeGraduatedStudents;
use App\Console\Commands\PurgeIncompleteApplications;
use App\Console\Commands\SendDeadlineReminders;
use Illuminate\Support\Facades\Schedule;

Schedule::command(SendDeadlineReminders::class)->dailyAt('07:00');
Schedule::command(AnonymizeGraduatedStudents::class)->daily();
Schedule::command(PurgeIncompleteApplications::class)->weekly();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');
