<?php

namespace App\Providers;

use App\Models\SiteSetting;
use Illuminate\Support\Facades\View;
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
        if (env('APP_ENV') !== 'local' || request()->server('HTTP_X_FORWARDED_PROTO') == 'https') {
            \Illuminate\Support\Facades\URL::forceScheme('https');
        }
        
        // Inject site settings into all views
        View::composer('*', function ($view) {
            try {
                $settings = SiteSetting::all()->toArray();
            } catch (\Exception $e) {
                $settings = [];
            }
            $view->with('settings', $settings);
        });
    }
}
