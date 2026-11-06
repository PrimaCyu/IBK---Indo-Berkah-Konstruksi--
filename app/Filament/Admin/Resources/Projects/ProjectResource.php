<?php

namespace App\Filament\Admin\Resources\Projects;

use App\Models\Project;
use App\Models\User;
use Filament\Forms;
use Filament\Tables;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Filament\Admin\Resources\Projects\Pages;
use Filament\Support\Icons\Heroicon;
use Filament\Actions\EditAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static string | \BackedEnum | null $navigationIcon = 'heroicon-o-briefcase';

    protected static ?string $navigationLabel = 'Proyek';

    protected static ?string $modelLabel = 'Proyek';

    protected static ?string $pluralModelLabel = 'Proyek';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Klien / Owner')
                    ->relationship('user', 'name')
                    ->options(User::where('role', 'owner')->pluck('name', 'id'))
                    ->searchable()
                    ->required(),

                Forms\Components\TextInput::make('nama_proyek')
                    ->label('Nama Proyek')
                    ->required(),

                Forms\Components\TextInput::make('alamat_proyek')
                    ->label('Alamat Proyek'),

                Forms\Components\Select::make('kategori')
                    ->label('Kategori')
                    ->options([
                        'Pembangunan Rumah' => 'Pembangunan Rumah',
                        'Gedung Komersial' => 'Gedung Komersial',
                        'Renovasi Bangunan' => 'Renovasi Bangunan',
                        'Konsultasi Konstruksi' => 'Konsultasi Konstruksi',
                    ]),

                Forms\Components\TextInput::make('progress_percentage')
                    ->label('Progress (%)')
                    ->numeric()
                    ->default(0)
                    ->minValue(0)
                    ->maxValue(100),

                Forms\Components\Select::make('status')
                    ->label('Status')
                    ->options([
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ])
                    ->default('aktif')
                    ->required()
                    ->reactive(),

                Forms\Components\DateTimePicker::make('completed_at')
                    ->label('Tanggal Selesai')
                    ->visible(fn ($get) => $get('status') === 'selesai'),

                Forms\Components\FileUpload::make('main_image')
                    ->label('Foto Utama Proyek')
                    ->image()
                    ->directory('projects')
                    ->visibility('public'),

                Forms\Components\FileUpload::make('gallery')
                    ->label('Galeri Proyek')
                    ->image()
                    ->multiple()
                    ->directory('projects/galleries')
                    ->visibility('public'),

                Forms\Components\Toggle::make('portfolio_approved')
                    ->label('Tampilkan di Portofolio Website')
                    ->helperText('Hanya berlaku jika status proyek adalah Selesai')
                    ->default(false),

                Forms\Components\Textarea::make('deskripsi')
                    ->label('Deskripsi / Catatan Proyek')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Klien')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('nama_proyek')
                    ->label('Nama Proyek')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('kategori')
                    ->label('Kategori'),

                Tables\Columns\TextColumn::make('progress_percentage')
                    ->label('Progress (%)')
                    ->badge()
                    ->color(fn (string $state): string => match (true) {
                        $state >= 100 => 'success',
                        $state > 0 => 'info',
                        default => 'gray',
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'aktif' => 'warning',
                        'selesai' => 'success',
                        'dibatalkan' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('portfolio_approved')
                    ->label('Portofolio')
                    ->boolean()
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Dibuat Pada')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'aktif' => 'Aktif',
                        'selesai' => 'Selesai',
                        'dibatalkan' => 'Dibatalkan',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->label('Klien')
                    ->relationship('user', 'name')
                    ->options(User::where('role', 'owner')->pluck('name', 'id')),
            ])
            ->actions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
