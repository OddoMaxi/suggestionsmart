<?php

// API Routes réservées pour usage futur (application mobile)
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    // Soumission suggestion via API mobile
    // Route::post('/suggestions', [App\Http\Controllers\Api\SuggestionApiController::class, 'store']);
});
