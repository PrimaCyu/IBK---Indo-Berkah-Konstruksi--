<?php

namespace App\Filament\Admin\Resources\UserFeedbacks\Pages;

use App\Filament\Admin\Resources\UserFeedbacks\UserFeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListUserFeedbacks extends ListRecords
{
    protected static string $resource = UserFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
}
