<?php

namespace App\Providers;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // Forcer HTTPS en production
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Rate limiter pour le formulaire de suggestion
        RateLimiter::for('suggestions', function (Request $request) {
            $max = (int) \App\Models\Setting::get('max_par_heure', 10);
            return Limit::perHour($max)->by($request->ip())->response(function () {
                return redirect()->back()->withErrors([
                    'rate_limit' => 'Trop de soumissions. Veuillez patienter avant de réessayer.',
                ]);
            });
        });
    }
}
