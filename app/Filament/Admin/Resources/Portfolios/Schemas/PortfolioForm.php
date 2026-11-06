<?php

namespace App\Filament\Admin\Resources\Portfolios\Schemas;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Schema;

class PortfolioForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('kategori')
                    ->required(),
                TextInput::make('program')
                    ->required(),
                \Filament\Forms\Components\RichEditor::make('deskripsi')
                    ->columnSpanFull(),
                DatePicker::make('waktu_pengerjaan'),
                FileUpload::make('main_image')
                    ->image()
                    ->disk('public')
                    ->directory('portfolios')
                    ->visibility('public'),
                FileUpload::make('gallery')
                    ->image()
                    ->multiple()
                    ->disk('public')
                    ->directory('portfolios/galleries')
                    ->visibility('public'),
            ]);
    }
}
