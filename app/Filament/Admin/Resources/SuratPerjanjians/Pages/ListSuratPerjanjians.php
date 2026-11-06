<?php

namespace App\Filament\Admin\Resources\SuratPerjanjians\Pages;

use App\Filament\Admin\Resources\SuratPerjanjians\SuratPerjanjianResource;
use Filament\Resources\Pages\ListRecords;

class ListSuratPerjanjians extends ListRecords
{
    protected static string $resource = SuratPerjanjianResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
