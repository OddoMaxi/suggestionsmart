<?php

use App\Http\Controllers\Admin\RapportController;
use App\Services\QrCodeService;
use App\Models\Service;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin-api')->name('admin.')->group(function () {

    Route::get('/services/{service}/qr/download', function (Service $service, QrCodeService $qrService) {
        return $qrService->reponseTelechargementSvg($service);
    })->name('service.qr.download');

    Route::get('/rapport/pdf', [RapportController::class, 'pdfSuggestions'])->name('rapport.pdf');
});
