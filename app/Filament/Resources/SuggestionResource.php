<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SuggestionResource\Pages;
use App\Models\Suggestion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class SuggestionResource extends Resource
{
    protected static ?string $model = Suggestion::class;
    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';
    protected static ?string $navigationLabel = 'Suggestions';
    protected static ?string $navigationGroup = 'Gestion';
    protected static ?int $navigationSort = 1;
    protected static ?string $modelLabel = 'Suggestion';
    protected static ?string $pluralModelLabel = 'Suggestions';

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::nouveau()->count() ?: null;
    }

    public static function getNavigationBadgeColor(): ?string
    {
        return 'danger';
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations de la suggestion')->schema([
                Forms\Components\TextInput::make('reference')
                    ->label('Référence')
                    ->disabled(),

                Forms\Components\Select::make('statut')
                    ->label('Statut')
                    ->options([
                        'nouveau'  => 'Nouveau',
                        'en_cours' => 'En cours',
                        'traite'   => 'Traité',
                        'cloture'  => 'Clôturé',
                    ])
                    ->required(),

                Forms\Components\Select::make('priorite')
                    ->label('Priorité')
                    ->options(['normale' => 'Normale', 'haute' => 'Haute'])
                    ->required(),

                Forms\Components\Select::make('assigne_a')
                    ->label('Assigné à')
                    ->relationship('assigneA', 'name')
                    ->searchable()
                    ->nullable(),
            ])->columns(2),

            Forms\Components\Section::make('Contenu')->schema([
                Forms\Components\Placeholder::make('auteur')
                    ->label('Auteur')
                    ->content(fn ($record) => $record ? ($record->anonyme ? '🕵️ Anonyme' : "{$record->prenom} {$record->nom} — {$record->telephone}") : ''),

                Forms\Components\Placeholder::make('service_nom')
                    ->label('Service')
                    ->content(fn ($record) => $record?->service?->nom),

                Forms\Components\Placeholder::make('type_affichage')
                    ->label('Type')
                    ->content(fn ($record) => $record ? ucfirst($record->type) : ''),

                Forms\Components\Placeholder::make('satisfaction_affichage')
                    ->label('Satisfaction')
                    ->content(fn ($record) => $record?->satisfaction ? str_repeat('★', $record->satisfaction) : 'Non renseignée'),

                Forms\Components\Placeholder::make('message_affichage')
                    ->label('Message')
                    ->content(fn ($record) => $record?->message),
            ])->columns(2),

            Forms\Components\Section::make('Traitement interne')->schema([
                Forms\Components\Textarea::make('commentaire_interne')
                    ->label('Commentaire interne (non visible par l\'auteur)')
                    ->rows(4),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // Référence — compacte, copiable
                Tables\Columns\TextColumn::make('reference')
                    ->label('Référence')
                    ->searchable()
                    ->copyable()
                    ->fontFamily('mono')
                    ->weight('bold')
                    ->grow(false),

                // Priorité — badge compact, caché si normale
                Tables\Columns\BadgeColumn::make('priorite')
                    ->label('Priorité')
                    ->colors(['danger' => 'haute', 'gray' => 'normale'])
                    ->formatStateUsing(fn ($state) => $state === 'haute' ? '▲ Haute' : 'Normale')
                    ->grow(false),

                // Type — badge compact
                Tables\Columns\BadgeColumn::make('type')
                    ->label('Type')
                    ->colors([
                        'info'    => 'suggestion',
                        'warning' => 'critique',
                        'success' => 'felicitation',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state))
                    ->grow(false),

                // Auteur — prénom + nom sur deux lignes (ou Anonyme)
                Tables\Columns\TextColumn::make('prenom')
                    ->label('Auteur')
                    ->formatStateUsing(fn ($state, $record) => $record->anonyme ? '🕵️ Anonyme' : ($state . ' ' . $record->nom))
                    ->description(fn ($record) => $record->anonyme ? null : $record->telephone)
                    ->searchable()
                    ->wrap(),

                // Service — tronqué avec agence en description
                Tables\Columns\TextColumn::make('service.nom')
                    ->label('Service')
                    ->description(fn ($record) => $record->agence?->nom)
                    ->searchable()
                    ->sortable()
                    ->limit(22)
                    ->wrap(),

                // Statut — badge compact
                Tables\Columns\BadgeColumn::make('statut')
                    ->label('Statut')
                    ->colors([
                        'info'    => 'nouveau',
                        'warning' => 'en_cours',
                        'success' => 'traite',
                        'gray'    => 'cloture',
                    ])
                    ->formatStateUsing(fn ($state) => match($state) {
                        'nouveau'  => 'Nouveau',
                        'en_cours' => 'En cours',
                        'traite'   => 'Traité',
                        'cloture'  => 'Clôturé',
                        default    => $state,
                    })
                    ->grow(false),

                // Date — format court, avec heure en description
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date('d/m/Y')
                    ->description(fn ($record) => $record->created_at->format('H:i'))
                    ->sortable()
                    ->grow(false),

                // Note — masquée par défaut, activable
                Tables\Columns\TextColumn::make('satisfaction')
                    ->label('Note')
                    ->formatStateUsing(fn ($state) => $state ? str_repeat('★', $state) . str_repeat('☆', 5 - $state) : '—')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),

                // Téléphone — masqué par défaut
                Tables\Columns\TextColumn::make('telephone')
                    ->label('Téléphone')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->grow(false),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('statut')
                    ->label('Statut')
                    ->options([
                        'nouveau'  => 'Nouveau',
                        'en_cours' => 'En cours',
                        'traite'   => 'Traité',
                        'cloture'  => 'Clôturé',
                    ]),

                Tables\Filters\SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'suggestion'   => 'Suggestion',
                        'critique'     => 'Critique',
                        'felicitation' => 'Félicitation',
                    ]),

                Tables\Filters\SelectFilter::make('priorite')
                    ->label('Priorité')
                    ->options(['normale' => 'Normale', 'haute' => 'Haute']),

                Tables\Filters\SelectFilter::make('service')
                    ->relationship('service', 'nom')
                    ->searchable()
                    ->preload(),

                Tables\Filters\SelectFilter::make('agence')
                    ->relationship('agence', 'nom')
                    ->searchable()
                    ->preload(),

                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('du')->label('Du'),
                        Forms\Components\DatePicker::make('au')->label('Au'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when($data['du'], fn ($q, $v) => $q->whereDate('created_at', '>=', $v))
                            ->when($data['au'], fn ($q, $v) => $q->whereDate('created_at', '<=', $v));
                    }),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('marquer_en_cours')
                        ->label('Marquer En cours')
                        ->icon('heroicon-o-arrow-path')
                        ->action(fn ($records) => $records->each->update(['statut' => 'en_cours'])),

                    Tables\Actions\BulkAction::make('marquer_traite')
                        ->label('Marquer Traité')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(fn ($records) => $records->each->update(['statut' => 'traite'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([15, 25, 50, 100])
            ->defaultPaginationPageOption(15);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListSuggestions::route('/'),
            'view'   => Pages\ViewSuggestion::route('/{record}'),
            'edit'   => Pages\EditSuggestion::route('/{record}/edit'),
        ];
    }
}
