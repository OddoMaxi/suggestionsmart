<?php

namespace App\Filament\Resources\SuggestionResource\Pages;

use App\Filament\Exports\SuggestionExporter;
use App\Filament\Resources\SuggestionResource;
use App\Models\Setting;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Contracts\HasTable;

class ListSuggestions extends ListRecords
{
    protected static string $resource = SuggestionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ExportAction::make()
                ->label('Exporter CSV / Excel')
                ->exporter(SuggestionExporter::class),

            Actions\Action::make('exportPdf')
                ->label('Exporter PDF')
                ->icon('heroicon-o-document-arrow-down')
                ->color('gray')
                ->action(fn (HasTable $livewire) => $this->telechargerPdfFiltre($livewire)),

            Actions\ActionGroup::make([
                Actions\Action::make('rapportJournalier')
                    ->label('Rapport journalier')
                    ->icon('heroicon-o-calendar-days')
                    ->url(route('admin.rapport.pdf', ['type' => 'journalier']))
                    ->openUrlInNewTab(),

                Actions\Action::make('rapportMensuel')
                    ->label('Rapport mensuel')
                    ->icon('heroicon-o-calendar')
                    ->url(route('admin.rapport.pdf', ['type' => 'mensuel']))
                    ->openUrlInNewTab(),

                Actions\Action::make('rapportAnnuel')
                    ->label('Rapport annuel')
                    ->icon('heroicon-o-calendar-days')
                    ->url(route('admin.rapport.pdf', ['type' => 'annuel']))
                    ->openUrlInNewTab(),
            ])
                ->label('Rapport périodique')
                ->icon('heroicon-o-chart-bar')
                ->color('gray'),

            Actions\CreateAction::make(),
        ];
    }

    protected function telechargerPdfFiltre(HasTable $livewire)
    {
        $suggestions = $livewire->getTableQueryForExport()->get();

        $stats = [
            'total' => $suggestions->count(),
            'suggestions' => $suggestions->where('type', 'suggestion')->count(),
            'critiques' => $suggestions->where('type', 'critique')->count(),
            'felicitations' => $suggestions->where('type', 'felicitation')->count(),
            'haute_priorite' => $suggestions->where('priorite', 'haute')->count(),
            'traites' => $suggestions->whereIn('statut', ['traite', 'cloture'])->count(),
            'satisfaction_moy' => $suggestions->whereNotNull('satisfaction')->avg('satisfaction') ?? 0,
        ];

        $pdf = Pdf::loadView('pdf.rapport', [
            'suggestions' => $suggestions,
            'stats' => $stats,
            'periode' => 'Export filtré — ' . now()->format('d/m/Y H:i'),
            'typeRapport' => 'Filtré',
            'orgNom' => Setting::get('org_nom'),
        ])->setPaper('a4', 'landscape');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'suggestions-filtre-' . now()->format('Ymd-His') . '.pdf',
        );
    }
}
