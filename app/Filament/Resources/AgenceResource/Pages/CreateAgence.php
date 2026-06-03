<?php

namespace App\Filament\Resources\AgenceResource\Pages;

use App\Filament\Resources\AgenceResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAgence extends CreateRecord
{
    protected static string $resource = AgenceResource::class;
    protected function getRedirectUrl(): string { return $this->getResource()::getUrl('index'); }
}
