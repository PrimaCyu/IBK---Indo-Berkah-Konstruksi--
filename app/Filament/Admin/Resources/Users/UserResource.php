<?php
namespace App\Filament\Admin\Resources\Users;

use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use App\Filament\Admin\Resources\Users\Pages\CreateUser;
use App\Filament\Admin\Resources\Users\Pages\EditUser;
use App\Filament\Admin\Resources\Users\Pages\ListUsers;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationLabel = 'Edit / Tambah User';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required(),

                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required(),

                Forms\Components\TextInput::make('phone'),

                Forms\Components\Select::make('role')
                    ->options([
                        'admin' => 'Admin',
                        'owner' => 'client'
                        
                    ])
                    ->required(),

                Forms\Components\TextInput::make('password')
                    ->label('Password (Isi jika ingin diubah)')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->required(fn ($livewire) => $livewire instanceof Pages\CreateUser)
                    ->dehydrateStateUsing(fn ($state) =>
                        filled($state) ? Hash::make($state) : null
                    )
                    ->dehydrated(fn ($state) => filled($state)),
            ]);
    }

   public static function table(Table $table): Table
{
    return $table
        ->columns([
            Tables\Columns\TextColumn::make('name')
                ->searchable(),

            Tables\Columns\TextColumn::make('email')
                ->searchable(),

            Tables\Columns\TextColumn::make('phone'),

            Tables\Columns\TextColumn::make('role')
                ->badge(),
        ])
        ->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ]);
}

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}