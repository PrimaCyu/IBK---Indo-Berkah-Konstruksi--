<?php

namespace App\Filament\Admin\Resources\KategoriBeritas\Schemas;

use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

use Illuminate\Support\Str;

class KategoriBeritaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
            ]);
    }
}
