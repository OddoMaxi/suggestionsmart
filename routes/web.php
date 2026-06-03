<?php

use App\Http\Controllers\Public\SuggestionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Routes publiques — Interface QR Code
|--------------------------------------------------------------------------
*/
Route::middleware(['throttle:suggestions'])->group(function () {
    Route::get('/suggestion', [SuggestionController::class, 'formulaire'])->name('suggestion.form');
    Route::post('/suggestion', [SuggestionController::class, 'enregistrer'])->name('suggestion.enregistrer');
});

Route::get('/merci/{reference}', [SuggestionController::class, 'merci'])->name('suggestion.merci');
Route::get('/suivi', [SuggestionController::class, 'suiviForm'])->name('suggestion.suivi');
Route::get('/suivi/{reference}', [SuggestionController::class, 'suiviResultat'])->name('suggestion.suivi.resultat');

// Redirection racine vers formulaire
Route::get('/', fn() => redirect()->route('suggestion.form'));
