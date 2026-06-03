<?php

namespace App\Services;

class PrioriteService
{
    private const MOTS_CLES_CRITIQUES = [
        'urgence', 'urgent', 'urgente',
        'corruption', 'corrompu',
        'fraude', 'fraudeur',
        'vol', 'volé', 'voleur',
        'menace', 'menacé', 'menaces',
        'argent', 'détournement',
        'plainte', 'plaintes',
        'grave', 'graves', 'gravité',
        'retard', 'retards',
        'danger', 'dangereux',
        'arnaque', 'escroquerie',
        'abus', 'maltraitance',
        'illégal', 'illégale',
        'mort', 'décès',
    ];

    public function evaluer(string $message): string
    {
        $messageLower = mb_strtolower($message);

        foreach (self::MOTS_CLES_CRITIQUES as $mot) {
            if (str_contains($messageLower, $mot)) {
                return 'haute';
            }
        }

        return 'normale';
    }

    public function getMots(): array
    {
        return self::MOTS_CLES_CRITIQUES;
    }
}
