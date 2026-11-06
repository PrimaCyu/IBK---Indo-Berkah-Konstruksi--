<?php

namespace App\Filament\Admin\Resources\Penawarans\Pages;

use App\Filament\Admin\Resources\Penawarans\PenawaranResource;
use Filament\Resources\Pages\ListRecords;

class ListPenawarans extends ListRecords
{
    protected static string $resource = PenawaranResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}