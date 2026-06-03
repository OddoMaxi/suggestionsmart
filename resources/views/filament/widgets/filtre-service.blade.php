<x-filament-widgets::widget>
    <x-filament::section>
        <div class="flex flex-wrap items-end gap-3">

            {{-- Titre --}}
            <div class="flex-1 min-w-0">
                <p class="text-sm font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                    Filtrer le tableau de bord
                </p>
            </div>

            {{-- Agence --}}
            @if(count($this->getAgences()) > 0)
            <div class="min-w-[180px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Agence / Site</label>
                <select wire:model.live="agenceId"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Toutes les agences</option>
                    @foreach($this->getAgences() as $id => $nom)
                        <option value="{{ $id }}">{{ $nom }}</option>
                    @endforeach
                </select>
            </div>
            @endif

            {{-- Service --}}
            <div class="min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Service</label>
                <select wire:model.live="serviceId"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="">Tous les services</option>
                    @foreach($this->getServices() as $id => $nom)
                        <option value="{{ $id }}">{{ $nom }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Période --}}
            <div class="min-w-[160px]">
                <label class="block text-xs font-medium text-gray-500 mb-1">Période</label>
                <select wire:model.live="periode"
                        class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-primary-500 focus:border-primary-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white">
                    <option value="all">Tout le temps</option>
                    <option value="today">Aujourd'hui</option>
                    <option value="week">Cette semaine</option>
                    <option value="month">Ce mois</option>
                    <option value="year">Cette année</option>
                </select>
            </div>

            {{-- Reset --}}
            @if($agenceId || $serviceId || $periode !== 'all')
            <div>
                <button wire:click="resetFiltres"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-gray-600 bg-gray-100 hover:bg-gray-200 dark:bg-gray-700 dark:text-gray-300 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                    Réinitialiser
                </button>
            </div>
            @endif

            {{-- Badge filtre actif --}}
            @if($serviceId)
            <div class="flex items-center gap-1.5 px-3 py-1.5 bg-primary-50 text-primary-700 rounded-full text-xs font-semibold dark:bg-primary-900/30 dark:text-primary-300">
                <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M3 3a1 1 0 011-1h12a1 1 0 011 1v3a1 1 0 01-.293.707L12 11.414V15a1 1 0 01-.553.894l-4 2A1 1 0 016 17v-5.586L3.293 6.707A1 1 0 013 6V3z"/>
                </svg>
                {{ $this->getServices()[$serviceId] ?? 'Service filtré' }}
            </div>
            @endif

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
