<?php

namespace App\Filament\Admin\Resources\UserFiles\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class UserFileForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('user_id')
                    ->required()
                    ->numeric(),
                TextInput::make('type')
                    ->required()
                    ->default('progress'),
                TextInput::make('title')
                    ->required(),
                TextInput::make('file_path')
                    ->required(),
            ]);
    }
}
