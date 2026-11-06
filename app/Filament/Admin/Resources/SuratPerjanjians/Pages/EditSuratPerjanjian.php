<?php

namespace App\Filament\Admin\Resources\SuratPerjanjians\Pages;

use App\Filament\Admin\Resources\SuratPerjanjians\SuratPerjanjianResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditSuratPerjanjian extends EditRecord
{
    protected static string $resource = SuratPerjanjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make()->label('Hapus'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
