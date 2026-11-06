<?php

namespace App\Filament\Admin\Resources\SuratPerjanjians;

use App\Filament\Admin\Resources\SuratPerjanjians\Pages\ListSuratPerjanjians;
use App\Filament\Admin\Resources\SuratPerjanjians\Pages\ViewSuratPerjanjian;
use App\Models\SuratPerjanjian;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class SuratPerjanjianResource extends Resource
{
    protected static ?string $model = SuratPerjanjian::class;
    protected static ?string $navigationLabel = 'Surat Perjanjian';
    protected static ?string $modelLabel = 'Surat Perjanjian';
    protected static ?string $pluralModelLabel = 'Surat Perjanjian';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-check';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Manajemen';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) SuratPerjanjian::count() ?: null;
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Pengirim')
                ->schema([
                    TextEntry::make('nama')->label('Nama'),
                    TextEntry::make('telepon')->label('Telepon')->default('-'),
                    TextEntry::make('email')->label('Email')->default('-'),
                    TextEntry::make('created_at')->label('Dikirim')->dateTime('d M Y, H:i'),
                ])->columns(2),

            Section::make('Keterangan')
                ->schema([
                    TextEntry::make('keterangan')
                        ->label('Keterangan')
                        ->default('Tidak ada keterangan')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('telepon')
                    ->label('Telepon')
                    ->default('-'),

                TextColumn::make('email')
                    ->label('Email')
                    ->default('-')
                    ->searchable(),

                TextColumn::make('keterangan')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->keterangan),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                // Buka file langsung di tab baru
                Action::make('lihat_file')
                    ->label('Buka File')
                    ->icon('heroicon-o-document-arrow-down')
                    ->color('info')
                    ->url(fn ($record) => asset('storage/' . $record->file_surat))
                    ->openUrlInNewTab(),

                ViewAction::make()->label('Detail'),
                DeleteAction::make()->label('Hapus'),
            ])
            ->bulkActions([
                DeleteBulkAction::make(),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => ListSuratPerjanjians::route('/'),
            'view'  => ViewSuratPerjanjian::route('/{record}'),
        ];
    }
}
