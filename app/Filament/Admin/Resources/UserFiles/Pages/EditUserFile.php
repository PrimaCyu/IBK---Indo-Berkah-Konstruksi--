<?php

namespace App\Filament\Admin\Resources\UserFiles\Pages;

use App\Filament\Admin\Resources\UserFiles\UserFileResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserFile extends EditRecord
{
    protected static string $resource = UserFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}