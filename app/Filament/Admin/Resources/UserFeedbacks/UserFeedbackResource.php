<?php

namespace App\Filament\Admin\Resources\UserFeedbacks;

use App\Filament\Admin\Resources\UserFeedbacks\Pages\EditUserFeedback;
use App\Filament\Admin\Resources\UserFeedbacks\Pages\ListUserFeedbacks;
use App\Models\UserFeedback;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class UserFeedbackResource extends Resource
{
    protected static ?string $model = UserFeedback::class;
    protected static ?string $slug = 'user-feedbacks';
    protected static ?string $navigationLabel = 'Masukan Lapangan';
    protected static ?string $modelLabel = 'Masukan User';
    protected static ?string $pluralModelLabel = 'Masukan & Catatan User';

    public static function getNavigationIcon(): string|\BackedEnum|null
    {
        return 'heroicon-o-chat-bubble-bottom-center-text';
    }

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return 'Manajemen Proyek';
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) UserFeedback::where('status', 'pending')->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->schema([
            Section::make('Detail Masukan & Catatan User')
                ->description('Catatan lapangan yang dikirim oleh client melalui dashboard.')
                ->schema([
                    Select::make('user_id')
                        ->relationship('user', 'name')
                        ->disabled()
                        ->label('Client'),

                    Select::make('project_id')
                        ->relationship('project', 'nama_proyek')
                        ->disabled()
                        ->label('Proyek'),

                    TextInput::make('title')
                        ->disabled()
                        ->label('Judul / Subjek'),

                    Select::make('status')
                        ->options([
                            'pending'   => '⏳ Pending',
                            'dibaca'    => '👀 Dibaca',
                            'diproses'  => '⚙️ Diproses',
                            'selesai'   => '✅ Selesai',
                        ])
                        ->required()
                        ->label('Status Catatan'),

                    RichEditor::make('content')
                        ->disabled()
                        ->label('Isi Masukan (Rich Text)')
                        ->columnSpanFull(),

                    Textarea::make('admin_reply')
                        ->label('Tanggapan / Catatan Admin (Untuk Client)')
                        ->rows(3)
                        ->placeholder('Tuliskan respon atau tindakan yang diambil...')
                        ->columnSpanFull(),
                ])->columns(3),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
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

                TextColumn::make('content')
                    ->label('Isi Masukan')
                    ->html()
                    ->limit(50)
                    ->tooltip(fn ($record) => strip_tags($record->content)),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'selesai'   => 'success',
                        'dibaca', 'diproses' => 'info',
                        'pending'   => 'warning',
                        default     => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pending'  => '⏳ Pending',
                        'dibaca'   => '👀 Dibaca',
                        'diproses' => '⚙️ Diproses',
                        'selesai'  => '✅ Selesai',
                        default    => $state,
                    }),

                TextColumn::make('created_at')
                    ->dateTime('d M Y, H:i')
                    ->label('Tgl Kirim')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->options([
                        'pending'  => 'Pending',
                        'dibaca'   => 'Dibaca',
                        'diproses' => 'Diproses',
                        'selesai'  => 'Selesai',
                    ])
                    ->label('Status Masukan'),

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
            ])
            ->actions([
                EditAction::make()->label('Tinjau'),
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
            'index' => ListUserFeedbacks::route('/'),
            'edit'  => EditUserFeedback::route('/{record}/edit'),
        ];
    }
}
