<?php

namespace App\Filament\Admin\Resources\ClientPartners\Pages;

use App\Filament\Admin\Resources\ClientPartners\ClientPartnerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateClientPartner extends CreateRecord
{
    protected static string $resource = ClientPartnerResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
