<?php

namespace App\Filament\Admin\Resources\UserDesigns\Pages;

use App\Filament\Admin\Resources\UserDesigns\UserDesignResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserDesign extends EditRecord
{
    protected static string $resource = UserDesignResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
