<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AgenceResource\Pages;
use App\Models\Agence;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AgenceResource extends Resource
{
    protected static ?string $model = Agence::class;
    protected static ?string $navigationIcon = 'heroicon-o-map-pin';
    protected static ?string $navigationLabel = 'Agences / Sites';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 3;
    protected static ?string $modelLabel = 'Agence';
    protected static ?string $pluralModelLabel = 'Agences';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations de l\'agence')->schema([
                Forms\Components\TextInput::make('nom')
                    ->label('Nom de l\'agence')
                    ->required()
                    ->maxLength(150)
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, Forms\Set $set) =>
                        $set('slug', Str::slug($state))),

                Forms\Components\TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('ville')
                    ->label('Ville'),

                Forms\Components\TextInput::make('adresse')
                    ->label('Adresse'),

                Forms\Components\TextInput::make('telephone')
                    ->label('Téléphone'),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email(),

                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->rows(2)
                    ->columnSpanFull(),

                Forms\Components\Toggle::make('actif')
                    ->label('Active')
                    ->default(true),
            ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nom')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('ville')->searchable(),
                Tables\Columns\TextColumn::make('services_count')
                    ->counts('services')
                    ->label('Services')
                    ->badge()
                    ->color('primary'),
                Tables\Columns\TextColumn::make('suggestions_count')
                    ->counts('suggestions')
                    ->label('Suggestions')
                    ->badge()
                    ->color('warning'),
                Tables\Columns\IconColumn::make('actif')->boolean(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->defaultSort('nom');
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAgences::route('/'),
            'create' => Pages\CreateAgence::route('/create'),
            'edit'   => Pages\EditAgence::route('/{record}/edit'),
        ];
    }
}
