<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Suggestion;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class RapportController extends Controller
{
    public function pdfSuggestions(Request $request): Response
    {
        $type = $request->get('type', 'mensuel');

        [$debut, $fin, $periode] = match ($type) {
            'journalier' => [
                now()->startOfDay(),
                now()->endOfDay(),
                'Journalier — ' . now()->format('d/m/Y'),
            ],
            'annuel' => [
                now()->startOfYear(),
                now()->endOfYear(),
                'Annuel — ' . now()->format('Y'),
            ],
            default => [
                now()->startOfMonth(),
                now()->endOfMonth(),
                'Mensuel — ' . now()->translatedFormat('F Y'),
            ],
        };

        $suggestions = Suggestion::with(['service', 'agence'])
            ->whereBetween('created_at', [$debut, $fin])
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total'          => $suggestions->count(),
            'suggestions'    => $suggestions->where('type', 'suggestion')->count(),
            'critiques'      => $suggestions->where('type', 'critique')->count(),
            'felicitations'  => $suggestions->where('type', 'felicitation')->count(),
            'haute_priorite' => $suggestions->where('priorite', 'haute')->count(),
            'traites'        => $suggestions->whereIn('statut', ['traite', 'cloture'])->count(),
            'satisfaction_moy' => $suggestions->whereNotNull('satisfaction')->avg('satisfaction') ?? 0,
        ];

        $pdf = Pdf::loadView('pdf.rapport', [
            'suggestions' => $suggestions,
            'stats'       => $stats,
            'periode'     => $periode,
            'typeRapport' => ucfirst($type),
            'orgNom'      => Setting::get('org_nom'),
        ])->setPaper('a4', 'landscape');

        return $pdf->download("rapport-smartsuggest-{$type}-" . now()->format('Ymd') . '.pdf');
    }
}
