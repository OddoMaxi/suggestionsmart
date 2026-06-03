<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ServiceResource\Pages;
use App\Models\Service;
use App\Services\QrCodeService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class ServiceResource extends Resource
{
    protected static ?string $model = Service::class;
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static ?string $navigationLabel = 'Services';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 2;
    protected static ?string $modelLabel = 'Service';
    protected static ?string $pluralModelLabel = 'Services';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations du service')->schema([
                Forms\Components\Select::make('agence_id')
                    ->label('Agence / Site')
                    ->relationship('agence', 'nom')
                    ->searchable()
                    ->preload()
                    ->nullable(),

                Forms\Components\TextInput::make('nom')
                    ->label('Nom du service')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                        $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug (URL)')
                    ->required()
                    ->unique(ignoreRecord: true)
                    ->maxLength(150),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->maxLength(500),
            ])->columns(2),

            Forms\Components\Section::make('Responsable')->schema([
                Forms\Components\TextInput::make('responsable')
                    ->label('Nom du responsable')
                    ->maxLength(150),

                Forms\Components\TextInput::make('email_responsable')
                    ->label('Email responsable')
                    ->email()
                    ->maxLength(150),

                Forms\Components\TextInput::make('telephone_responsable')
                    ->label('Téléphone responsable')
                    ->tel()
                    ->maxLength(30),
            ])->columns(3),

            Forms\Components\Section::make('Apparence & Ordre')->schema([
                Forms\Components\ColorPicker::make('couleur')
                    ->label('Couleur'),

                Forms\Components\TextInput::make('icone')
                    ->label('Icône Heroicon')
                    ->placeholder('heroicon-o-building-office'),

                Forms\Components\TextInput::make('ordre')
                    ->label('Ordre d\'affichage')
                    ->numeric()
                    ->default(0),

                Forms\Components\Toggle::make('actif')
                    ->label('Service actif')
                    ->default(true),
            ])->columns(4),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')
                    ->label('Service')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('agence.nom')
                    ->label('Agence')
                    ->searchable()
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('responsable')
                    ->label('Responsable')
                    ->searchable(),

                Tables\Columns\TextColumn::make('suggestions_count')
                    ->label('Suggestions')
                    ->counts('suggestions')
                    ->badge()
                    ->color('warning'),

                Tables\Columns\ColorColumn::make('couleur')
                    ->label('Couleur'),

                Tables\Columns\IconColumn::make('actif')
                    ->label('Actif')
                    ->boolean(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('actif')->label('Statut actif'),
                Tables\Filters\SelectFilter::make('agence')
                    ->relationship('agence', 'nom')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\Action::make('qrcode')
                    ->label('QR Code')
                    ->icon('heroicon-o-qr-code')
                    ->color('success')
                    ->action(function (Service $record) {
                        app(QrCodeService::class)->sauvegarderPng($record);
                        Notification::make()
                            ->title('QR Code généré avec succès')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\Action::make('telecharger_qr')
                    ->label('Télécharger QR')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('gray')
                    ->url(fn (Service $record) => route('admin.service.qr.download', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('ordre')
            ->reorderable('ordre');
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListServices::route('/'),
            'create' => Pages\CreateService::route('/create'),
            'edit'   => Pages\EditService::route('/{record}/edit'),
        ];
    }
}
