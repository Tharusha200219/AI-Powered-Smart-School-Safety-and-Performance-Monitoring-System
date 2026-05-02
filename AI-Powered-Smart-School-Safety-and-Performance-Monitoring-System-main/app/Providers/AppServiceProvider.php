<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('settings')) {
                $setting = \App\Models\Setting::first();
                if ($setting && $setting->timezone) {
                    date_default_timezone_set($setting->timezone);
                    config(['app.timezone' => $setting->timezone]);
                }
            }
        } catch (\Exception $e) {
            // Ignore if DB is not ready yet
        }
    }
}
