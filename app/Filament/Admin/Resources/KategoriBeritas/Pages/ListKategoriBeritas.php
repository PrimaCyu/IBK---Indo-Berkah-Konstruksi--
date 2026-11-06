<?php

namespace App\Filament\Admin\Resources\KategoriBeritas\Pages;

use App\Filament\Admin\Resources\KategoriBeritas\KategoriBeritaResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListKategoriBeritas extends ListRecords
{
    protected static string $resource = KategoriBeritaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
