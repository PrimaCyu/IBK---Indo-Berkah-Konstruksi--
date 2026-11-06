<?php

namespace App\Filament\Admin\Resources\KategoriBeritas\Pages;

use App\Filament\Admin\Resources\KategoriBeritas\KategoriBeritaResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditKategoriBerita extends EditRecord
{
    protected static string $resource = KategoriBeritaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
