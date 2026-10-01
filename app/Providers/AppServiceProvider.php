<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Foundation\MaintenanceMode;
use Illuminate\Foundation\FileBasedMaintenanceMode;
use Illuminate\Support\Facades\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    
    public function register(): void
    {
        $this->app->singleton(MaintenanceMode::class, function () {
            return new FileBasedMaintenanceMode(storage_path('framework/down'));
        });

        $this->app->singleton(MenuBarcodeService::class, function ($app) {
            return new MenuBarcodeService();
        });

        if (config('app.env') === 'production') {
            $this->app->bind('path.public', function() {
                return base_path().'/public_html';
            });
        }
    }   

    
    public function boot(): void
    {
        if(config('app.env') === 'production') {
            \URL::forceScheme('https');
        }
    }
}
