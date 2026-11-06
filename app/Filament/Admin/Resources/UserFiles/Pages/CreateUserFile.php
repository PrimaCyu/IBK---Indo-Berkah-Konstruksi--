<?php

namespace App\Filament\Admin\Resources\UserFiles\Pages;

use App\Filament\Admin\Resources\UserFiles\UserFileResource;
use Filament\Resources\Pages\CreateRecord;

class CreateUserFile extends CreateRecord
{
    protected static string $resource = UserFileResource::class;
}