protected $routeMiddleware = [
    'permission' => \App\Http\Middleware\CheckPermission::class,
    'check.user.status' => \App\Http\Middleware\CheckUserStatus::class,
];

protected function schedule($schedule)
{
    // Use app settings for reset time; fallback to 04:00
    $settings = app(\App\Services\SettingsService::class)->getScholarSettings();
    $time = $settings['reset_time'] ?? '04:00';

    $schedule->call(function () {
        $settings = app(\App\Services\SettingsService::class)->getScholarSettings();
        if (!($settings['auto_reset'] ?? true)) {
            return;
        }
        $scholars = \App\Models\Scholar::where('is_active', true)->get();
        $service = app(\App\Services\ScholarService::class);
        foreach ($scholars as $scholar) {
            $service->resetDailyAllowanceIfNeeded($scholar);
        }
    })->dailyAt($time);
}