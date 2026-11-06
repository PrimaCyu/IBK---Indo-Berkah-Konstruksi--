<?php

namespace App\Filament\Admin\Resources\KategoriBeritas;

use App\Filament\Admin\Resources\KategoriBeritas\Pages\CreateKategoriBerita;
use App\Filament\Admin\Resources\KategoriBeritas\Pages\EditKategoriBerita;
use App\Filament\Admin\Resources\KategoriBeritas\Pages\ListKategoriBeritas;
use App\Filament\Admin\Resources\KategoriBeritas\Schemas\KategoriBeritaForm;
use App\Filament\Admin\Resources\KategoriBeritas\Tables\KategoriBeritasTable;
use App\Models\KategoriBerita;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class KategoriBeritaResource extends Resource
{
    protected static ?string $model = KategoriBerita::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'nama';

    public static function form(Schema $schema): Schema
    {
        return KategoriBeritaForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return KategoriBeritasTable::configure($table);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListKategoriBeritas::route('/'),
            'create' => CreateKategoriBerita::route('/create'),
            'edit' => EditKategoriBerita::route('/{record}/edit'),
        ];
    }
}
