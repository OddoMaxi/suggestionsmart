<?php

namespace App\Filament\Widgets;

use App\Models\Suggestion;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Livewire\Attributes\On;

class StatsSuggestions extends BaseWidget
{
    protected static ?int $sort = 1;

    public string $agenceId  = '';
    public string $serviceId = '';
    public string $periode   = 'all';

    #[On('dashboard-filter-updated')]
    public function applyFilter(array $filters): void
    {
        $this->agenceId  = $filters['agenceId']  ?? '';
        $this->serviceId = $filters['serviceId'] ?? '';
        $this->periode   = $filters['periode']   ?? 'all';
    }

    private function baseQuery()
    {
        return Suggestion::query()
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
            });
    }

    protected function getStats(): array
    {
        $base          = $this->baseQuery();
        $total         = (clone $base)->count();
        $suggestions   = (clone $base)->where('type', 'suggestion')->count();
        $critiques     = (clone $base)->where('type', 'critique')->count();
        $reclamations  = (clone $base)->where('type', 'reclamation')->count();
        $felicitations = (clone $base)->where('type', 'felicitation')->count();
        $hautesPrio    = (clone $base)->where('priorite', 'haute')->whereIn('statut', ['nouveau', 'en_cours'])->count();
        $nouveaux      = (clone $base)->where('statut', 'nouveau')->count();

        $filtreActif = $this->serviceId || $this->agenceId || $this->periode !== 'all';
        $description = $filtreActif ? 'Vue filtrée' : 'Tous services confondus';

        return [
            Stat::make('Total', $total)
                ->description($description)
                ->icon('heroicon-o-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Nouveaux', $nouveaux)
                ->description('En attente de traitement')
                ->icon('heroicon-o-bell-alert')
                ->color('info'),

            Stat::make('Haute priorité', $hautesPrio)
                ->description('Non clôturés')
                ->icon('heroicon-o-exclamation-triangle')
                ->color($hautesPrio > 0 ? 'danger' : 'success'),

            Stat::make('Suggestions', $suggestions)
                ->icon('heroicon-o-light-bulb')
                ->color('info'),

            Stat::make('Critiques', $critiques)
                ->icon('heroicon-o-exclamation-triangle')
                ->color('warning'),

            Stat::make('Réclamations', $reclamations)
                ->icon('heroicon-o-x-circle')
                ->color('danger'),

            Stat::make('Félicitations', $felicitations)
                ->icon('heroicon-o-star')
                ->color('success'),
        ];
    }
}
