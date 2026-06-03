<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class PreventSpam
{
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'suggestion:' . $request->ip();
        $maxAttempts = (int) \App\Models\Setting::get('max_par_heure', 10);

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $secondes = RateLimiter::availableIn($key);

            return response()->view('public.trop_de_tentatives', [
                'secondes' => $secondes,
                'minutes'  => ceil($secondes / 60),
            ], 429);
        }

        RateLimiter::hit($key, 3600);

        return $next($request);
    }
}
