<?php

namespace App\Filament\Admin\Resources\SiteSettings\Pages;

use App\Filament\Admin\Resources\SiteSettings\SiteSettingResource;
use App\Models\SiteSetting;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Schema;

class EditSiteSettings extends Page
{
    protected static string $resource = SiteSettingResource::class;

    public string $view = 'filament.pages.edit-site-settings';

    public ?array $data = [];

    /**
     * Nilai-nilai default yang sama persis dengan yang ditampilkan di frontend.
     * Jika admin belum pernah menyimpan suatu field, form akan menampilkan
     * teks ini (bukan field kosong), sehingga admin tahu apa yang sedang ditampilkan.
     */
    private array $defaults = [
        // Identitas & Footer
        'company_name'             => 'Indo Berkah Konstruksi',
        'footer_telepon'           => '+62 878 6530 9966',
        'footer_email'             => 'partners@indoberkahkonstruksi.com',
        'footer_company_text'      => 'Menghadirkan mahakarya arsitektur dan konstruksi dengan standar kualitas premium, presisi, dan dedikasi penuh.',
        'footer_tagline'           => 'Menjaga Kualitas Mewujud Berkah',
        'footer_eksplorasi_label'  => 'Eksplorasi',
        'footer_keahlian_label'    => 'Keahlian Kami',
        'footer_kontak_label'      => 'Konsultasi',
        'footer_keahlian_1'        => 'Hunian Mewah',
        'footer_keahlian_2'        => 'Komersial Premium',
        'footer_keahlian_3'        => 'Desain Interior',
        'footer_keahlian_4'        => 'Manajemen Konstruksi',

        // Hero
        'hero_title_line1'  => 'Menjaga Kualitas',
        'hero_title_line2'  => 'mewujud',
        'hero_title_line3'  => 'Berkah.',
        'hero_subtitle'     => 'Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.',
        'hero_badge_title'  => 'Fokus pada Kualitas',
        'hero_badge_subtitle' => 'Presisi tingkat tinggi dalam setiap tahap konstruksi.',

        // Tentang Kami
        'about_label'           => 'BestBuild Indo Berkah',
        'about_title_line1'     => 'Pelopor Kualitas',
        'about_title_line2'     => 'dan Keunggulan',
        'about_title_line3'     => 'dalam Setiap Proyek',
        'about_description'     => 'INDO BERKAH KONSTRUKSI adalah perusahaan jasa konstruksi yang menyediakan layanan pembangunan rumah, gedung, infrastruktur, renovasi, serta konstruksi besi dan baja.',
        'about_experience'      => '10+',
        'about_experience_text' => 'Tahun Pengalaman Membangun Kepercayaan',

        // Layanan
        'program_label'      => 'Keahlian Kami',
        'program_title'      => 'Layanan',
        'program_title_bold' => 'Indo Berkah Konstruksi',
        'layanan_1_title'    => 'Pembangunan Rumah',
        'layanan_1_slug'     => 'pembangunan-rumah',
        'layanan_1_description' => 'Kami menyediakan layanan pembangunan rumah dari tahap perencanaan hingga selesai dengan desain mewah dan material premium.',
        'layanan_2_title'    => 'Gedung Komersial',
        'layanan_2_slug'     => 'gedung-komersial',
        'layanan_2_description' => 'Melayani pembangunan gedung komersial dengan standar internasional, fokus pada efisiensi serta ketahanan struktur.',
        'layanan_3_title'    => 'Renovasi Bangunan',
        'layanan_3_slug'     => 'renovasi-bangunan',
        'layanan_3_description' => 'Solusi renovasi cerdas untuk meningkatkan nilai estetika, fungsi, dan kenyamanan properti eksklusif Anda.',
        'layanan_4_title'    => 'Konsultasi Ahli',
        'layanan_4_slug'     => 'konsultasi-ahli',
        'layanan_4_description' => 'Konsultasi perencanaan desain dan manajemen konstruksi mendalam untuk memastikan mahakarya Anda terwujud sempurna.',
    ];

    public function mount(): void
    {
        // Ambil data yang sudah tersimpan di database
        $saved = SiteSetting::all()->pluck('value', 'key')->toArray();

        // Gabungkan: default dahulu, lalu timpa dengan nilai yang sudah disimpan admin.
        // Hasilnya: field yang belum pernah disimpan akan menampilkan teks default frontend,
        // bukan field kosong.
        $this->data = array_merge($this->defaults, $saved);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->statePath('data')
            ->components([
                Tabs::make('Settings')
                    ->tabs([
                        Tabs\Tab::make('🏢 Info Perusahaan')
                            ->schema([
                                Section::make('Identitas Utama')
                                    ->description('Pengaturan dasar mengenai profil perusahaan Anda.')
                                    ->schema([
                                        TextInput::make('company_name')->label('Nama Perusahaan')
                                            ->placeholder('Misal: PT Indo Berkah Konstruksi')
                                            ->helperText('Nama resmi perusahaan Anda.')
                                            ->default('Indo Berkah Konstruksi'),
                                        TextInput::make('company_address')->label('Alamat Perusahaan')
                                            ->placeholder('Misal: Jl. Raya Konstruksi No. 123, Jakarta')
                                            ->helperText('Alamat lengkap kantor atau perusahaan.'),
                                        FileUpload::make('company_logo')
                                            ->label('Logo Perusahaan')
                                            ->image()
                                            ->directory('site-settings/logo')
                                            ->visibility('public')
                                            ->helperText('Rekomendasi ukuran: 512x512px. Format: PNG, JPG.'),
                                    ])->columns(2),
                                Section::make('Kontak & Sosial Media')
                                    ->description('Informasi kontak yang akan ditampilkan di berbagai bagian situs seperti footer dan halaman kontak.')
                                    ->schema([
                                        TextInput::make('footer_telepon')->label('Nomor Telepon / WhatsApp')
                                            ->placeholder('Misal: +62 812-3456-7890')
                                            ->helperText('Disarankan menggunakan kode negara (+62).')
                                            ->default('+62 878 6530 9966'),
                                        TextInput::make('footer_email')->label('Email Perusahaan')
                                            ->placeholder('Misal: info@perusahaan.com')
                                            ->email()
                                            ->default('partners@indoberkahkonstruksi.com'),
                                        TextInput::make('footer_facebook')->label('Link Facebook')
                                            ->placeholder('Misal: https://facebook.com/namaperusahaan')
                                            ->url(),
                                        TextInput::make('footer_instagram')->label('Link Instagram')
                                            ->placeholder('Misal: https://instagram.com/namaperusahaan')
                                            ->url(),
                                    ])->columns(2),
                                Section::make('Teks Bawah Situs (Footer)')
                                    ->description('Teks singkat yang akan muncul di bagian paling bawah halaman situs Anda.')
                                    ->schema([
                                        Textarea::make('footer_company_text')->label('Deskripsi Singkat Perusahaan')
                                            ->rows(2)
                                            ->placeholder('Misal: Kami adalah perusahaan konstruksi terpercaya yang selalu mengutamakan kualitas...')
                                            ->columnSpanFull()
                                            ->default('Menghadirkan mahakarya arsitektur dan konstruksi dengan standar kualitas premium, presisi, dan dedikasi penuh.'),
                                        TextInput::make('footer_tagline')->label('Teks Hak Cipta (Copyright)')
                                            ->placeholder('Misal: © 2026 PT Indo Berkah Konstruksi. All rights reserved.')
                                            ->columnSpanFull()
                                            ->default('Menjaga Kualitas Mewujud Berkah'),
                                    ]),
                            ]),

                        Tabs\Tab::make('🏠 Halaman Utama (Hero)')
                            ->schema([
                                Section::make('Teks Sambutan (Hero)')
                                    ->description('Teks berukuran besar yang pertama kali dilihat pengunjung saat membuka halaman utama situs.')
                                    ->schema([
                                        TextInput::make('hero_title_line1')->label('Baris 1 Judul')
                                            ->placeholder('Misal: Bangun Masa Depan')
                                            ->default('Menjaga Kualitas'),
                                        TextInput::make('hero_title_line2')->label('Baris 2 Judul (Gaya Miring)')
                                            ->placeholder('Misal: Lebih Baik')
                                            ->default('mewujud'),
                                        TextInput::make('hero_title_line3')->label('Baris 3 Judul')
                                            ->placeholder('Misal: Bersama Kami')
                                            ->default('Berkah.'),
                                        Textarea::make('hero_subtitle')->label('Sub-judul (Teks Kecil di Bawah Judul)')
                                            ->placeholder('Misal: Kami menyediakan layanan konstruksi terbaik dengan pengalaman bertahun-tahun...')
                                            ->rows(2)
                                            ->columnSpanFull()
                                            ->default('Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.'),
                                        TextInput::make('hero_badge_title')->label('Teks Sorotan / Badge (Kiri Bawah)')
                                            ->placeholder('Misal: 10+ Tahun')
                                            ->default('Fokus pada Kualitas'),
                                        TextInput::make('hero_badge_subtitle')->label('Keterangan Sorotan / Badge')
                                            ->placeholder('Misal: Pengalaman Membangun')
                                            ->default('Presisi tingkat tinggi dalam setiap tahap konstruksi.'),
                                    ])->columns(2),

                                Section::make('Gambar Latar Utama (Hero)')
                                    ->description('Gambar latar belakang yang menarik untuk bagian atas halaman utama.')
                                    ->schema([
                                        FileUpload::make('hero_image')
                                            ->label('Unggah Gambar Utama')
                                            ->image()
                                            ->directory('site-settings')
                                            ->visibility('public')
                                            ->helperText('Rekomendasi ukuran: 1920x1080 piksel (Landscape). Maksimal 2MB.')
                                            ->columnSpanFull(),
                                    ]),
                            ]),

                        Tabs\Tab::make('📖 Bagian "Tentang Kami"')
                            ->schema([
                                Section::make('Teks Utama "Tentang Kami"')
                                    ->description('Konten ini berfungsi untuk menjelaskan siapa Anda dan apa yang perusahaan Anda lakukan di halaman utama.')
                                    ->schema([
                                        TextInput::make('about_label')->label('Label Kecil (Atas)')
                                            ->placeholder('Misal: TENTANG KAMI')
                                            ->default('Tentang BestBuild Indo Berkah'),
                                        TextInput::make('about_title_line1')->label('Judul Baris 1')
                                            ->placeholder('Misal: Mengenal Lebih Dekat')
                                            ->default('Pelopor Kualitas'),
                                        TextInput::make('about_title_line2')->label('Judul Baris 2 (Tebal)')
                                            ->placeholder('Misal: Perusahaan Kami')
                                            ->default('dan Keunggulan'),
                                        TextInput::make('about_title_line3')->label('Judul Baris 3')
                                            ->placeholder('Misal: yang Berdedikasi')
                                            ->default('dalam Setiap Proyek'),
                                        Textarea::make('about_description')->label('Deskripsi Paragraf')
                                            ->placeholder('Misal: Berdiri sejak tahun 2010, kami telah sukses mengerjakan berbagai proyek berskala besar...')
                                            ->rows(4)
                                            ->columnSpanFull()
                                            ->default('INDO BERKAH KONSTRUKSI adalah perusahaan jasa konstruksi yang menyediakan layanan pembangunan rumah, gedung, infrastruktur, renovasi, serta konstruksi besi dan baja.'),
                                    ])->columns(2),

                                Section::make('Gambar & Statistik Pencapaian')
                                    ->schema([
                                        FileUpload::make('about_image')
                                            ->label('Gambar Samping')
                                            ->image()
                                            ->directory('site-settings')
                                            ->visibility('public')
                                            ->helperText('Rekomendasi: Gambar dengan orientasi potret (portrait).')
                                            ->columnSpanFull(),
                                        TextInput::make('about_experience')->label('Angka Pencapaian (Statistik)')
                                            ->placeholder('Misal: 150+')
                                            ->default('10+'),
                                        TextInput::make('about_experience_text')->label('Keterangan Pencapaian')
                                            ->placeholder('Misal: Proyek Selesai')
                                            ->default('Tahun Pengalaman Membangun Kepercayaan'),
                                    ])->columns(2),
                            ]),

                        Tabs\Tab::make('🔧 Layanan Utama')
                            ->schema([
                                Section::make('Judul Bagian Layanan')
                                    ->description('Teks pengantar sebelum daftar layanan Anda ditampilkan.')
                                    ->schema([
                                        TextInput::make('program_label')->label('Label Kecil (Atas)')
                                            ->placeholder('Misal: LAYANAN KAMI')
                                            ->default('Keahlian Kami'),
                                        TextInput::make('program_title')->label('Teks Biasa')
                                            ->placeholder('Misal: Solusi Untuk')
                                            ->default('Layanan'),
                                        TextInput::make('program_title_bold')->label('Teks Tebal')
                                            ->placeholder('Misal: Kebutuhan Konstruksi Anda')
                                            ->default('Indo Berkah Konstruksi'),
                                    ])->columns(3),

                                Section::make('Daftar Layanan 1')
                                    ->schema([
                                        TextInput::make('layanan_1_title')->label('Nama Layanan 1')->placeholder('Misal: Desain Arsitektur'),
                                        TextInput::make('layanan_1_slug')->label('URL Link (Slug)')->placeholder('Misal: desain-arsitektur')->helperText('Format disarankan huruf kecil dan dipisah dengan strip (-)'),
                                        Textarea::make('layanan_1_description')->label('Deskripsi Singkat')->rows(2)->columnSpanFull()->placeholder('Misal: Menyediakan desain arsitektur modern...'),
                                        FileUpload::make('layanan_1_image')
                                            ->label('Gambar/Ikon Layanan 1')
                                            ->image()
                                            ->directory('site-settings/layanan')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),

                                Section::make('Daftar Layanan 2')
                                    ->schema([
                                        TextInput::make('layanan_2_title')->label('Nama Layanan 2')->placeholder('Misal: Konstruksi Gedung'),
                                        TextInput::make('layanan_2_slug')->label('URL Link (Slug)')->placeholder('Misal: konstruksi-gedung')->helperText('Format disarankan huruf kecil dan dipisah dengan strip (-)'),
                                        Textarea::make('layanan_2_description')->label('Deskripsi Singkat')->rows(2)->columnSpanFull(),
                                        FileUpload::make('layanan_2_image')
                                            ->label('Gambar/Ikon Layanan 2')
                                            ->image()
                                            ->directory('site-settings/layanan')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),

                                Section::make('Daftar Layanan 3')
                                    ->schema([
                                        TextInput::make('layanan_3_title')->label('Nama Layanan 3'),
                                        TextInput::make('layanan_3_slug')->label('URL Link (Slug)'),
                                        Textarea::make('layanan_3_description')->label('Deskripsi Singkat')->rows(2)->columnSpanFull(),
                                        FileUpload::make('layanan_3_image')
                                            ->label('Gambar/Ikon Layanan 3')
                                            ->image()
                                            ->directory('site-settings/layanan')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),

                                Section::make('Daftar Layanan 4')
                                    ->schema([
                                        TextInput::make('layanan_4_title')->label('Nama Layanan 4'),
                                        TextInput::make('layanan_4_slug')->label('URL Link (Slug)'),
                                        Textarea::make('layanan_4_description')->label('Deskripsi Singkat')->rows(2)->columnSpanFull(),
                                        FileUpload::make('layanan_4_image')
                                            ->label('Gambar/Ikon Layanan 4')
                                            ->image()
                                            ->directory('site-settings/layanan')
                                            ->visibility('public')
                                            ->columnSpanFull(),
                                    ])->columns(2)->collapsible(),
                            ]),

                        Tabs\Tab::make('🦶 Pengaturan Footer Lainnya')
                            ->schema([
                                Section::make('Judul Kolom Bawah (Footer)')
                                    ->description('Ubah teks judul untuk kolom menu yang ada di paling bawah situs.')
                                    ->schema([
                                        TextInput::make('footer_eksplorasi_label')->label('Judul Menu Eksplorasi (Kiri)')
                                            ->placeholder('Misal: Eksplorasi Navigasi')
                                            ->default('Eksplorasi'),
                                        TextInput::make('footer_keahlian_label')->label('Judul Menu Keahlian (Tengah)')
                                            ->placeholder('Misal: Layanan Utama Kami')
                                            ->default('Keahlian Kami'),
                                        TextInput::make('footer_kontak_label')->label('Judul Kolom Kontak (Kanan)')
                                            ->placeholder('Misal: Hubungi Kami')
                                            ->default('Konsultasi'),
                                    ])->columns(3),

                                Section::make('Daftar Keahlian Singkat (Untuk Footer)')
                                    ->description('Poin-poin singkat yang ditampilkan pada kolom tengah Footer.')
                                    ->schema([
                                        TextInput::make('footer_keahlian_1')->label('Poin 1')->placeholder('Misal: Konstruksi Sipil')->default('Hunian Mewah'),
                                        TextInput::make('footer_keahlian_2')->label('Poin 2')->placeholder('Misal: Desain Interior')->default('Komersial Premium'),
                                        TextInput::make('footer_keahlian_3')->label('Poin 3')->placeholder('Misal: Konsultasi Proyek')->default('Desain Interior'),
                                        TextInput::make('footer_keahlian_4')->label('Poin 4')->placeholder('Misal: Manajemen Mutu')->default('Manajemen Konstruksi'),
                                    ])->columns(2),
                            ]),

                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ]);
    }

    public function save(): void
    {
        $data = $this->data;

        foreach ($data as $key => $value) {
            if ($value !== null) {
                SiteSetting::set($key, is_array($value) ? ($value[0] ?? '') : (string) $value);
            }
        }

        Notification::make()
            ->title('Pengaturan berhasil disimpan!')
            ->success()
            ->send();
    }
}
