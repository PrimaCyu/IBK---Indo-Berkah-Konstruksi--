<?php

namespace App\Filament\Admin\Resources\UserDesigns\Pages;

use App\Filament\Admin\Resources\UserDesigns\UserDesignResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserDesign extends CreateRecord
{
    protected static string $resource = UserDesignResource::class;
}
