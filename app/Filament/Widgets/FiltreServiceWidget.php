<?php

namespace App\Filament\Widgets;

use App\Models\Agence;
use App\Models\Service;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Widgets\Widget;
use Livewire\Attributes\Reactive;

class FiltreServiceWidget extends Widget implements HasForms
{
    use InteractsWithForms;

    protected static ?int $sort = 0;
    protected static string $view = 'filament.widgets.filtre-service';
    protected int|string|array $columnSpan = 'full';

    public string $agenceId  = '';
    public string $serviceId = '';
    public string $periode   = 'all';

    public function updatedAgenceId(): void
    {
        $this->serviceId = '';
        $this->dispatch('dashboard-filter-updated', [
            'agenceId'  => $this->agenceId,
            'serviceId' => $this->serviceId,
            'periode'   => $this->periode,
        ]);
    }

    public function updatedServiceId(): void
    {
        $this->dispatch('dashboard-filter-updated', [
            'agenceId'  => $this->agenceId,
            'serviceId' => $this->serviceId,
            'periode'   => $this->periode,
        ]);
    }

    public function updatedPeriode(): void
    {
        $this->dispatch('dashboard-filter-updated', [
            'agenceId'  => $this->agenceId,
            'serviceId' => $this->serviceId,
            'periode'   => $this->periode,
        ]);
    }

    public function getAgences(): array
    {
        return Agence::actif()->orderBy('nom')->pluck('nom', 'id')->toArray();
    }

    public function getServices(): array
    {
        return Service::actif()
            ->when($this->agenceId, fn($q) => $q->where('agence_id', $this->agenceId))
            ->orderBy('nom')
            ->pluck('nom', 'id')
            ->toArray();
    }

    public function resetFiltres(): void
    {
        $this->agenceId  = '';
        $this->serviceId = '';
        $this->periode   = 'all';
        $this->dispatch('dashboard-filter-updated', [
            'agenceId'  => '',
            'serviceId' => '',
            'periode'   => 'all',
        ]);
    }
}
