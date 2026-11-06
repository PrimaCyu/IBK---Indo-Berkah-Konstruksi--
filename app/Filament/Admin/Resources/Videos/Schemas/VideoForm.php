<?php

namespace App\Filament\Admin\Resources\Videos\Schemas;

use Filament\Schemas\Schema;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class VideoForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('title')
                    ->label('Judul Video')
                    ->maxLength(255),
                TextInput::make('youtube_link')
                    ->label('Link YouTube')
                    ->required()
                    ->url()
                    ->maxLength(255),
                Toggle::make('is_active')
                    ->label('Aktif')
                    ->default(true),
            ]);
    }
}
