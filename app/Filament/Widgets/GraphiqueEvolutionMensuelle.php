<?php

namespace App\Filament\Widgets;

use App\Models\Suggestion;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\On;

class GraphiqueEvolutionMensuelle extends ChartWidget
{
    protected static ?int $sort = 2;
    protected int|string|array $columnSpan = 'full';

    public string $agenceId  = '';
    public string $serviceId = '';
    public string $periode   = 'all';

    public function getHeading(): string
    {
        $parts = [];
        if ($this->serviceId) $parts[] = 'service filtré';
        if ($this->periode !== 'all') $parts[] = match($this->periode) {
            'today' => "aujourd'hui", 'week' => 'semaine', 'month' => 'mois', 'year' => 'année', default => '',
        };
        return 'Évolution mensuelle' . ($parts ? ' — ' . implode(', ', $parts) : '');
    }

    #[On('dashboard-filter-updated')]
    public function applyFilter(array $filters): void
    {
        $this->agenceId  = $filters['agenceId']  ?? '';
        $this->serviceId = $filters['serviceId'] ?? '';
        $this->periode   = $filters['periode']   ?? 'all';
    }

    protected function getData(): array
    {
        $donnees = Suggestion::select(
            DB::raw('EXTRACT(MONTH FROM created_at)::integer as mois'),
            DB::raw('EXTRACT(YEAR  FROM created_at)::integer as annee'),
            DB::raw('COUNT(*) as total'),
            DB::raw("SUM(CASE WHEN type='suggestion'   THEN 1 ELSE 0 END) as suggestions"),
            DB::raw("SUM(CASE WHEN type='critique'     THEN 1 ELSE 0 END) as critiques"),
            DB::raw("SUM(CASE WHEN type='felicitation' THEN 1 ELSE 0 END) as felicitations"),
        )
            ->when($this->agenceId,  fn($q) => $q->where('agence_id',  $this->agenceId))
            ->when($this->serviceId, fn($q) => $q->where('service_id', $this->serviceId))
            ->whereYear('created_at', now()->year)
            ->groupBy('annee', 'mois')
            ->orderBy('mois')
            ->get();

        $moisLabels = ['Jan','Fév','Mar','Avr','Mai','Jun','Jul','Aoû','Sep','Oct','Nov','Déc'];
        $labels = $donnees->pluck('mois')->map(fn($m) => $moisLabels[$m - 1])->toArray();

        return [
            'datasets' => [
                ['label' => 'Total',        'data' => $donnees->pluck('total')->toArray(),        'borderColor' => '#6366f1', 'backgroundColor' => 'rgba(99,102,241,0.1)', 'fill' => true],
                ['label' => 'Suggestions',  'data' => $donnees->pluck('suggestions')->toArray(),  'borderColor' => '#3b82f6'],
                ['label' => 'Critiques',    'data' => $donnees->pluck('critiques')->toArray(),    'borderColor' => '#f59e0b'],
                ['label' => 'Félicitations','data' => $donnees->pluck('felicitations')->toArray(),'borderColor' => '#22c55e'],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string { return 'line'; }
}
