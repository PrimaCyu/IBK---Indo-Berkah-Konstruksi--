<?php

namespace App\Filament\Admin\Resources\TeamMembers\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeamMembersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nama')
                    ->label('Nama Lengkap (Admin)')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('nama_pendek')
                    ->label('Nama Pendek (Web)')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                TextColumn::make('posisi')
                    ->label('Posisi')
                    ->searchable(),
                TextColumn::make('telepon')
                    ->label('No. Telp / WA')
                    ->searchable(),
                \Filament\Tables\Columns\ImageColumn::make('foto'),
                \Filament\Tables\Columns\TextColumn::make('status')
                    ->label('Status Lamaran')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'pending' => 'warning',
                        'accepted' => 'success',
                        'rejected' => 'danger',
                        default => 'secondary',
                    })
                    ->formatStateUsing(fn (string $state): string => ucfirst($state)),
                \Filament\Tables\Columns\ToggleColumn::make('is_visible')
                    ->label('Tampilkan (Web)'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Actions\Action::make('terima')
                    ->label('Terima (ACC)')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->visible(fn (\App\Models\TeamMember $record): bool => $record->status === 'pending')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('start_date')
                            ->label('Tanggal Mulai Bekerja')
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\TeamMember $record): void {
                        $record->update([
                            'status' => 'accepted',
                            'start_date' => $data['start_date'],
                            'is_visible' => true,
                        ]);
                        if (!empty($record->email)) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($record->email)->send(new \App\Mail\ApplicationAccepted($record));
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning('Gagal kirim email diterima: ' . $e->getMessage());
                            }
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Pelamar Diterima')
                            ->success()
                            ->send();
                    }),
                \Filament\Actions\Action::make('tolak')
                    ->label('Tolak')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->visible(fn (\App\Models\TeamMember $record): bool => $record->status === 'pending')
                    ->form([
                        \Filament\Forms\Components\Textarea::make('rejection_reason')
                            ->label('Alasan Penolakan')
                            ->required(),
                    ])
                    ->action(function (array $data, \App\Models\TeamMember $record): void {
                        $record->update([
                            'status' => 'rejected',
                            'rejection_reason' => $data['rejection_reason'],
                            'is_visible' => false,
                        ]);
                        if (!empty($record->email)) {
                            try {
                                \Illuminate\Support\Facades\Mail::to($record->email)->send(new \App\Mail\ApplicationRejected($record));
                            } catch (\Throwable $e) {
                                \Illuminate\Support\Facades\Log::warning('Gagal kirim email ditolak: ' . $e->getMessage());
                            }
                        }
                        \Filament\Notifications\Notification::make()
                            ->title('Pelamar Ditolak')
                            ->success()
                            ->send();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
