<?php

namespace App\Services;

use App\Models\Service;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrCodeService
{
    // SVG : pas besoin d'Imagick, vectoriel, qualité parfaite à toute taille
    public function genererSvg(Service $service, int $size = 300): string
    {
        return QrCode::format('svg')
            ->size($size)
            ->errorCorrection('H')
            ->margin(1)
            ->generate($service->qrCodeUrl());
    }

    public function sauvegarderSvg(Service $service, int $size = 300): string
    {
        $chemin = "qrcodes/service-{$service->id}.svg";
        Storage::disk('public')->put($chemin, $this->genererSvg($service, $size));
        return $chemin;
    }

    // Alias pour la compatibilité — génère SVG dans tous les cas
    public function sauvegarderPng(Service $service, int $size = 300): string
    {
        return $this->sauvegarderSvg($service, $size);
    }

    public function urlPublique(Service $service): string
    {
        return Storage::disk('public')->url("qrcodes/service-{$service->id}.svg");
    }

    public function reponseTelechargementSvg(Service $service): \Symfony\Component\HttpFoundation\Response
    {
        return response($this->genererSvg($service, 400))
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="qr-' . $service->slug . '.svg"');
    }
}
