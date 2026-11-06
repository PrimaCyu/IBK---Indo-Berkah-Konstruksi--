<?php

namespace App\Filament\Admin\Resources\ClientPartners\Pages;

use App\Filament\Admin\Resources\ClientPartners\ClientPartnerResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditClientPartner extends EditRecord
{
    protected static string $resource = ClientPartnerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
