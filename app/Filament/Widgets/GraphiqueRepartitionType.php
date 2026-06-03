<?php

namespace App\Filament\Widgets;

use App\Models\Suggestion;
use Filament\Widgets\ChartWidget;
use Livewire\Attributes\On;

class GraphiqueRepartitionType extends ChartWidget
{
    protected static ?int $sort = 3;

    public string $agenceId  = '';
    public string $serviceId = '';
    public string $periode   = 'all';

    public function getHeading(): string
    {
        return 'Répartition par type' . ($this->serviceId ? ' — service filtré' : '');
    }

    #[On('dashboard-filter-updated')]
    public function applyFilter(array $filters): void
    {
        $this->agenceId  = $filters['agenceId']  ?? '';
        $this->serviceId = $filters['serviceId'] ?? '';
        $this->periode   = $filters['periode']   ?? 'all';
    }

    private function count(string $type): int
    {
        return Suggestion::where('type', $type)
            ->when($this->agenceId,  fn($q) => $q->where('agence_id',  $this->agenceId))
            ->when($this->serviceId, fn($q) => $q->where('service_id', $this->serviceId))
            ->when($this->periode !== 'all', function ($q) {
                return match ($this->periode) {
                    'today' => $q->whereDate('created_at', today()),
                    'week'  => $q->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]),
                    'month' => $q->whereMonth('created_at', now()->month)->whereYear('created_at', now()->year),
                    'year'  => $q->whereYear('created_at', now()->year),
                    default => $q,
                };
            })
            ->count();
    }

    protected function getData(): array
    {
        $data = [
            $this->count('suggestion'),
            $this->count('critique'),
            $this->count('reclamation'),
            $this->count('felicitation'),
        ];

        return [
            'datasets' => [[
                'data'            => $data,
                'backgroundColor' => ['#3b82f6', '#f59e0b', '#ef4444', '#22c55e'],
                'borderWidth'     => 0,
            ]],
            'labels' => ['Suggestions', 'Critiques', 'Réclamations', 'Félicitations'],
        ];
    }

    protected function getType(): string { return 'doughnut'; }
}
