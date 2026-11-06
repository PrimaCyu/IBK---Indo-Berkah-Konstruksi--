<?php

namespace App\Filament\Admin\Resources\Penawarans;

use App\Filament\Admin\Resources\Penawarans\Pages\EditPenawaran;
use App\Filament\Admin\Resources\Penawarans\Pages\ListPenawarans;
use App\Filament\Admin\Resources\Penawarans\Pages\ViewPenawaran;
use App\Models\Penawaran;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class PenawaranResource extends Resource
{
    protected static ?string $model = Penawaran::class;
    protected static ?string $navigationLabel = 'Penawaran';
    protected static ?string $modelLabel = 'Penawaran';
    protected static ?string $pluralModelLabel = 'Penawaran';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document-text';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Manajemen';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) \App\Models\Penawaran::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Pengirim')
                ->schema([
                    TextInput::make('nama')->required()->label('Nama Lengkap'),
                    TextInput::make('telepon')->required()->label('No. Telepon'),
                    TextInput::make('email')->email()->label('Email'),
                    TextInput::make('budget')->required()->label('Budget'),
                ])->columns(2),

            Section::make('Detail Proyek')
                ->schema([
                    Textarea::make('deskripsi')
                        ->required()
                        ->rows(5)
                        ->label('Deskripsi')
                        ->columnSpanFull(),

                    Placeholder::make('foto_info')
                        ->label('Foto Referensi')
                        ->content(fn ($record) => $record?->foto
                            ? 'Ada foto (lihat di halaman detail)'
                            : 'Tidak ada foto')
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Informasi Pengirim')
                ->schema([
                    TextEntry::make('nama')->label('Nama'),
                    TextEntry::make('telepon')->label('Telepon'),
                    TextEntry::make('email')->label('Email')->default('-'),
                    TextEntry::make('budget')->label('Budget')->badge()->color('warning'),
                    TextEntry::make('created_at')->label('Dikirim')->dateTime('d M Y, H:i'),
                ])->columns(2),

            Section::make('Detail Proyek')
                ->schema([
                    TextEntry::make('deskripsi')->label('Deskripsi')->columnSpanFull(),

                    ImageEntry::make('foto')
                        ->label('Foto Referensi')
                        ->disk('public')
                        ->height(400)
                        ->columnSpanFull()
                        ->visible(fn ($record) => $record?->foto !== null),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('foto')
                    ->disk('public')
                    ->label('Foto')
                    ->square()
                    ->size(60)
                    ->defaultImageUrl(fn () => null),

                TextColumn::make('nama')
                    ->label('Nama')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('telepon')
                    ->label('Telepon')
                    ->searchable(),

                TextColumn::make('email')
                    ->label('Email')
                    ->default('-'),

                TextColumn::make('budget')
                    ->label('Budget')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('deskripsi')
                    ->label('Deskripsi')
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->deskripsi),

                TextColumn::make('created_at')
                    ->label('Dikirim')
                    ->dateTime('d M Y')
                    ->sortable(),
            ])
            ->actions([
                // Buka foto langsung di tab baru
                Action::make('lihat_foto')
                    ->label('Foto')
                    ->icon('heroicon-o-photo')
                    ->color('info')
                    ->url(fn ($record) => $record->foto
                        ? asset('storage/' . $record->foto)
                        : null)
                    ->openUrlInNewTab()
                    ->visible(fn ($record) => $record->foto !== null),

                ViewAction::make()->label('Detail'),
                EditAction::make()->label('Edit'),
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
            'index' => ListPenawarans::route('/'),
            'view'  => ViewPenawaran::route('/{record}'),
            'edit'  => EditPenawaran::route('/{record}/edit'),
        ];
    }
}