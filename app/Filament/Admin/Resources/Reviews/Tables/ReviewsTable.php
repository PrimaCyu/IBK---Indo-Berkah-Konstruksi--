<?php

namespace App\Filament\Admin\Resources\Reviews\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ReviewsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')
                    ->label('Client')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('message')
                    ->label('Pesan Review')
                    ->limit(50)
                    ->tooltip(fn ($record) => $record->message),
                \Filament\Tables\Columns\ImageColumn::make('image')
                    ->disk('public')
                    ->label('Foto')
                    ->square(),
                \Filament\Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Disetujui (Web)'),
                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Waktu Kirim')
                    ->sortable(),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
