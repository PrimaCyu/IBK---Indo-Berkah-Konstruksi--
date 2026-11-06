<?php

namespace App\Filament\Admin\Resources\TeamMembers\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class TeamMemberForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nama')
                    ->label('Nama Lengkap (Khusus Admin)')
                    ->helperText('Hanya dapat dilihat oleh admin.')
                    ->required(),
                TextInput::make('nama_pendek')
                    ->label('Nama Pendek / Panggilan (Publik)')
                    ->helperText('Nama ini yang ditampilkan di website publik (Our Team).')
                    ->placeholder('Contoh: Budi')
                    ->required(),
                \Filament\Forms\Components\Select::make('posisi')
                    ->options([
                        'Arsitek' => 'Arsitek',
                        'Pekerja Konstruksi' => 'Pekerja Konstruksi',
                        'Konsultan' => 'Konsultan',
                        'Kepala Tukang' => 'Kepala Tukang',
                        'Pengawas Lapangan' => 'Pengawas Lapangan',
                        'Administrator' => 'Administrator',
                        'Pengawas Pemeriksa' => 'Pengawas Pemeriksa',
                        'Tukang' => 'Tukang',
                        'Asisten Tukang' => 'Asisten Tukang',
                    ])
                    ->searchable()
                    ->required(),
                \Filament\Forms\Components\FileUpload::make('foto')
                    ->image()
                    ->directory('team')
                    ->required(),
                RichEditor::make('profil_singkat')
                    ->columnSpanFull()
                    ->required(),
                TextInput::make('email')
                    ->email(),
                TextInput::make('telepon')
                    ->tel()
                    ->label('No. Telepon / WA'),
                Textarea::make('alamat')
                    ->columnSpanFull(),
                \Filament\Forms\Components\Toggle::make('is_visible')
                    ->label('Tampilkan di Our Team')
                    ->default(true),
            ]);
    }
}
