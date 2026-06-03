<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;
    protected static ?string $navigationIcon = 'heroicon-o-users';
    protected static ?string $navigationLabel = 'Utilisateurs';
    protected static ?string $navigationGroup = 'Administration';
    protected static ?int $navigationSort = 10;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Informations')->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Nom complet')
                    ->required()
                    ->maxLength(150),

                Forms\Components\TextInput::make('email')
                    ->label('Email')
                    ->email()
                    ->required()
                    ->unique(ignoreRecord: true),

                Forms\Components\TextInput::make('telephone')
                    ->label('Téléphone')
                    ->tel(),

                Forms\Components\TextInput::make('password')
                    ->label('Mot de passe')
                    ->password()
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context) => $context === 'create'),

                Forms\Components\Select::make('roles')
                    ->label('Rôles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),

                Forms\Components\Select::make('agence_id')
                    ->label('Agence')
                    ->relationship('agence', 'nom')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                Forms\Components\Select::make('service_id')
                    ->label('Service')
                    ->relationship('service', 'nom')
                    ->nullable()
                    ->searchable()
                    ->preload(),

                Forms\Components\Toggle::make('actif')
                    ->label('Compte actif')
                    ->default(true),
            ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->label('Nom')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('email')->searchable(),
                Tables\Columns\TextColumn::make('roles.name')->label('Rôles')->badge(),
                Tables\Columns\TextColumn::make('agence.nom')->label('Agence')->badge()->color('info'),
                Tables\Columns\IconColumn::make('actif')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime('d/m/Y')->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit'   => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
