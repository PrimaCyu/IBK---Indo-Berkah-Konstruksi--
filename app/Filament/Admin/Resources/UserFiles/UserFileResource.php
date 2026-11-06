<?php

namespace App\Filament\Admin\Resources\UserFiles;

use App\Filament\Admin\Resources\UserFiles\Pages\CreateUserFile;
use App\Filament\Admin\Resources\UserFiles\Pages\EditUserFile;
use App\Filament\Admin\Resources\UserFiles\Pages\ListUserFiles;
use App\Models\UserFile;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Enums\FiltersLayout;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class UserFileResource extends Resource
{
    protected static ?string $model = UserFile::class;
    protected static ?string $navigationLabel = 'File & Desain';
    protected static ?string $modelLabel = 'File';
    protected static ?string $pluralModelLabel = 'File & Desain';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-folder-open';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Manajemen Proyek';
    }

    public static function getNavigationBadge(): ?string
    {
        // Tampilkan jumlah desain user yang belum dibalas dengan foto progres
        return (string) UserFile::where('type', 'design')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Upload Foto Progres untuk User')
                ->description('Admin mengunggah foto perkembangan proyek yang akan terlihat di dashboard user.')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required()
                        ->label('Pilih Client')
                        ->reactive()
                        ->afterStateUpdated(fn (callable $set) => $set('project_id', null)),

                    Select::make('project_id')
                        ->label('Pilih Proyek')
                        ->options(fn (callable $get) => 
                            $get('user_id') 
                                ? \App\Models\Project::where('user_id', $get('user_id'))->pluck('nama_proyek', 'id')
                                : []
                        )
                        ->searchable()
                        ->required()
                        ->placeholder('Pilih proyek client...'),

                    TextInput::make('title')
                        ->required()
                        ->maxLength(255)
                        ->label('Judul')
                        ->placeholder('Contoh: Progres Fondasi Minggu 1'),

                    Textarea::make('description')
                        ->label('Keterangan')
                        ->rows(3)
                        ->placeholder('Jelaskan detail file ini...')
                        ->columnSpanFull(),

                    Select::make('type')
                        ->options([
                            'progress' => '📸 Foto Progres (dari Admin)',
                            'design'   => '📄 Desain (dari User)',
                        ])
                        ->default('progress')
                        ->required()
                        ->label('Tipe'),

                    FileUpload::make('file_path')
                        ->image()
                        ->disk('public')
                        ->directory('progress-photos')
                        ->required()
                        ->label('Upload File')
                        ->columnSpanFull(),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->disk('public')
                    ->label('Preview')
                    ->square()
                    ->size(60),

                TextColumn::make('type')
                    ->label('Tipe')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'progress' => 'success',
                        'design'   => 'info',
                        default    => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'progress' => '📸 Foto Progres',
                        'design'   => '📄 Desain User',
                        default    => $state,
                    }),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('project.nama_proyek')
                    ->label('Proyek')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->description)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Waktu')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options([
                        'design'   => 'Desain User',
                        'progress' => 'Foto Progres',
                    ])
                    ->label('Tipe File')
                    ->placeholder('Semua Tipe'),

                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Client')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('project_id')
                    ->relationship('project', 'nama_proyek')
                    ->label('Proyek')
                    ->searchable()
                    ->preload(),
            ], layout: FiltersLayout::AboveContent)
            ->actions([
                EditAction::make()->label('Edit'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc')
            ->defaultGroup('type');
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListUserFiles::route('/'),
            'create' => CreateUserFile::route('/create'),
            'edit'   => EditUserFile::route('/{record}/edit'),
        ];
    }
}
