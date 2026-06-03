<?php

namespace App\Providers\Filament;

use App\Filament\Resources\AgenceResource;
use App\Filament\Resources\ServiceResource;
use App\Filament\Resources\SuggestionResource;
use App\Filament\Resources\UserResource;
use App\Filament\Widgets\FiltreServiceWidget;
use App\Filament\Widgets\GraphiqueEvolutionMensuelle;
use App\Filament\Widgets\GraphiqueRepartitionType;
use App\Filament\Widgets\StatsSuggestions;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->brandName('SmartSuggest QR')
            ->navigationGroups([
                NavigationGroup::make('Gestion'),
                NavigationGroup::make('Administration'),
                NavigationGroup::make('Configuration')
                    ->collapsed(),
            ])
            ->resources([
                SuggestionResource::class,
                ServiceResource::class,
                AgenceResource::class,
                UserResource::class,
            ])
            ->widgets([
                FiltreServiceWidget::class,
                StatsSuggestions::class,
                GraphiqueEvolutionMensuelle::class,
                GraphiqueRepartitionType::class,
            ])
            ->pages([
                Pages\Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
