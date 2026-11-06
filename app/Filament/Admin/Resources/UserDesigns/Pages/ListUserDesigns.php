<?php

namespace App\Filament\Admin\Resources\UserDesigns\Pages;

use App\Filament\Admin\Resources\UserDesigns\UserDesignResource;
use Filament\Resources\Pages\ListRecords;

class ListUserDesigns extends ListRecords
{
    protected static string $resource = UserDesignResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
