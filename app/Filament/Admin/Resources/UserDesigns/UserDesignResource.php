<?php

namespace App\Filament\Admin\Resources\UserDesigns;

use App\Filament\Admin\Resources\UserDesigns\Pages\CreateUserDesign;
use App\Filament\Admin\Resources\UserDesigns\Pages\EditUserDesign;
use App\Filament\Admin\Resources\UserDesigns\Pages\ListUserDesigns;
use App\Models\UserFile;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserDesignResource extends Resource
{
    protected static ?string $model = UserFile::class;
    protected static ?string $navigationLabel = 'Desain User';
    protected static ?string $modelLabel = 'Desain';
    protected static ?string $pluralModelLabel = 'Desain User';
    protected static ?string $slug = 'user-designs';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-document';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Manajemen Proyek';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) UserFile::where('type', 'design')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'info';
    }

    // Hanya tampilkan type = design
    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()->where('type', 'design');
    }

    public static function form(Schema $schema): Schema
    {
        // Admin hanya lihat, tidak perlu form upload
        return $schema->schema([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('file_path')
                    ->disk('public')
                    ->label('Preview')
                    ->square()
                    ->size(70),

                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('title')
                    ->label('Judul Desain')
                    ->searchable()
                    ->limit(35),

                TextColumn::make('description')
                    ->label('Keterangan')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->description)
                    ->placeholder('-'),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Diunggah')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->label('Client')
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                // Tombol lihat file
                \Filament\Actions\Action::make('lihat')
                    ->label('Lihat File')
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn ($record) => asset('storage/' . $record->file_path))
                    ->openUrlInNewTab(),

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
            'index' => ListUserDesigns::route('/'),
        ];
    }
}
