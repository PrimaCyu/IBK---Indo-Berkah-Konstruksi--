<?php

namespace App\Filament\Admin\Resources\SuratPerjanjians\Pages;

use App\Filament\Admin\Resources\SuratPerjanjians\SuratPerjanjianResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSuratPerjanjian extends ViewRecord
{
    protected static string $resource = SuratPerjanjianResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // Buka file di tab baru
            Action::make('buka_file')
                ->label('Buka File')
                ->icon('heroicon-o-document-arrow-down')
                ->color('info')
                ->url(fn () => asset('storage/' . $this->record->file_surat))
                ->openUrlInNewTab(),

            DeleteAction::make()->label('Hapus'),
        ];
    }
}
