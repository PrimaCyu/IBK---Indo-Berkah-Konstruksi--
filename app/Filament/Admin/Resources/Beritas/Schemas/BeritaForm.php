<?php

namespace App\Filament\Admin\Resources\Beritas\Schemas;

use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;

class BeritaForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('kategori_berita_id')
                    ->relationship('kategori', 'nama')
                    ->required()
                    ->label('Kategori'),
                TextInput::make('title')
                    ->label('Judul Berita')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn (string $operation, $state, $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('views')
                    ->label('Jumlah Pengunjung')
                    ->numeric()
                    ->default(0),
                FileUpload::make('image')
                    ->label('Foto Berita')
                    ->image()
                    ->maxSize(5120)
                    ->directory('berita-images')
                    ->columnSpanFull(),
                RichEditor::make('content')
                    ->label('Isi Berita')
                    ->required()
                    ->columnSpanFull(),
                Toggle::make('is_published')
                    ->label('Tampilkan Berita?')
                    ->default(true)
                    ->required(),
            ]);
    }
}
