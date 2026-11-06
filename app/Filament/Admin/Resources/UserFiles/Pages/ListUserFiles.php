<?php

namespace App\Filament\Admin\Resources\UserFiles\Pages;

use App\Filament\Admin\Resources\UserFiles\UserFileResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListUserFiles extends ListRecords
{
    protected static string $resource = UserFileResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('Upload Foto Progres'),
        ];
    }
}