<?php

namespace App\Filament\Admin\Resources\ClientPartners\Pages;

use App\Filament\Admin\Resources\ClientPartners\ClientPartnerResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListClientPartners extends ListRecords
{
    protected static string $resource = ClientPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
