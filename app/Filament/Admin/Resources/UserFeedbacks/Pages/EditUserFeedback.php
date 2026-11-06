<?php

namespace App\Filament\Admin\Resources\UserFeedbacks\Pages;

use App\Filament\Admin\Resources\UserFeedbacks\UserFeedbackResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditUserFeedback extends EditRecord
{
    protected static string $resource = UserFeedbackResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
