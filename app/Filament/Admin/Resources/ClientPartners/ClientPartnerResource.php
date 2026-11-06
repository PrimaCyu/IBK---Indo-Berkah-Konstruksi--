<?php

namespace App\Filament\Admin\Resources\ClientPartners;

use App\Filament\Admin\Resources\ClientPartners\Pages\CreateClientPartner;
use App\Filament\Admin\Resources\ClientPartners\Pages\EditClientPartner;
use App\Filament\Admin\Resources\ClientPartners\Pages\ListClientPartners;
use App\Filament\Admin\Resources\ClientPartners\Schemas\ClientPartnerForm;
use App\Filament\Admin\Resources\ClientPartners\Tables\ClientPartnersTable;
use App\Models\ClientPartner;
use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Table;

class ClientPartnerResource extends Resource
{
    protected static ?string $model = ClientPartner::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Schema $schema): Schema
    {
        return ClientPartnerForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ClientPartnersTable::configure($table);
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
            'index' => ListClientPartners::route('/'),
            'create' => CreateClientPartner::route('/create'),
            'edit' => EditClientPartner::route('/{record}/edit'),
        ];
    }
}
