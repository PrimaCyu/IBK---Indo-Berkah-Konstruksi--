<?php

namespace App\Filament\Admin\Resources\ClientPartners\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class ClientPartnerForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                FileUpload::make('image')
                    ->image()
                    ->disk('public')
                    ->directory('client_partners')
                    ->required(fn (string $operation): bool => $operation === 'create'),
                TextInput::make('link'),
            ]);
    }
}
