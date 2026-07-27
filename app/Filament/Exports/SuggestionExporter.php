<?php

namespace App\Filament\Exports;

use App\Models\Suggestion;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class SuggestionExporter extends Exporter
{
    protected static ?string $model = Suggestion::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('reference')->label('Référence'),
            ExportColumn::make('agence.nom')->label('Agence'),
            ExportColumn::make('service.nom')->label('Service'),
            ExportColumn::make('type')->label('Type')->formatStateUsing(fn ($state) => ucfirst($state)),
            ExportColumn::make('statut')->label('Statut')->formatStateUsing(fn ($state) => match ($state) {
                'nouveau' => 'Nouveau',
                'en_cours' => 'En cours',
                'traite' => 'Traité',
                'cloture' => 'Clôturé',
                default => $state,
            }),
            ExportColumn::make('priorite')->label('Priorité')->formatStateUsing(fn ($state) => ucfirst($state)),
            ExportColumn::make('nom_affichage')->label('Auteur'),
            ExportColumn::make('telephone')->label('Téléphone')->formatStateUsing(
                fn ($state, Suggestion $record) => $record->anonyme ? '' : $state
            ),
            ExportColumn::make('email')->label('Email')->formatStateUsing(
                fn ($state, Suggestion $record) => $record->anonyme ? '' : $state
            ),
            ExportColumn::make('message')->label('Message'),
            ExportColumn::make('satisfaction')->label('Satisfaction (1-5)'),
            ExportColumn::make('created_at')->label('Date de soumission'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Votre export de suggestions est terminé : ' . number_format($export->successful_rows) . ' ' . str('ligne')->plural($export->successful_rows) . ' exportée(s).';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('ligne')->plural($failedRowsCount) . ' n\'ont pas pu être exportée(s).';
        }

        return $body;
    }
}
