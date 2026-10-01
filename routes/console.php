<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

// Auto-reset scholar balances daily at 12:00 AM (non-carryover)
Schedule::call(function () {
    $scholars = \App\Models\Scholar::where('is_active', true)->get();
    $service = app(\App\Services\ScholarService::class);
    foreach ($scholars as $scholar) {
        $service->resetDailyAllowanceIfNeeded($scholar);
    }
})->everyMinute();

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote')->hourly();
