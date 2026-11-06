<?php

namespace Database\Seeders;

use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

class SiteSettingSeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Hero Section
            'hero_title_line1'   => 'Menjaga Kualitas',
            'hero_title_line2'   => 'mewujud',
            'hero_title_line3'   => 'Berkah.',
            'hero_subtitle'      => 'Membangun lingkungan masa depan yang berdampak. Kami memadukan estetika modern dengan ketahanan struktur tak tertandingi.',
            'hero_image'         => 'image/ElementProgram/rumah2.jpg',
            'hero_badge_title'   => 'Fokus pada Kualitas',
            'hero_badge_subtitle'=> 'Presisi tingkat tinggi dalam setiap tahap konstruksi.',

            // About Section
            'about_label'        => 'Tentang BestBuild Indo Berkah',
            'about_title_line1'  => 'Pelopor Kualitas',
            'about_title_line2'  => 'dan Keunggulan',
            'about_title_line3'  => 'dalam Setiap Proyek',
            'about_description'  => 'INDO BERKAH KONSTRUKSI adalah perusahaan jasa konstruksi yang menyediakan layanan pembangunan rumah, gedung, infrastruktur, renovasi, serta konstruksi besi dan baja. Kami siap menangani proyek skala kecil maupun besar dengan kualitas dan profesionalisme terbaik.',
            'about_image'        => 'image/ElementProgram/foto9.jpg',
            'about_experience'   => '10+',
            'about_experience_text' => 'Tahun Pengalaman Membangun Kepercayaan',

            // Program/Layanan Section
            'program_label'      => 'Keahlian Kami',
            'program_title'      => 'Layanan',
            'program_title_bold' => 'Indo Berkah Konstruksi',

            // Layanan Card 1
            'layanan_1_title'       => 'Pembangunan Rumah',
            'layanan_1_description' => 'Kami menyediakan layanan pembangunan rumah dari tahap perencanaan hingga selesai dengan desain mewah dan material premium.',
            'layanan_1_image'       => 'image/ElementProgram/building-construction-industry-18-svgrepo-com.svg',
            'layanan_1_slug'        => 'pembangunan-rumah',

            // Layanan Card 2
            'layanan_2_title'       => 'Gedung Komersial',
            'layanan_2_description' => 'Melayani pembangunan gedung komersial dengan standar internasional, fokus pada efisiensi serta ketahanan struktur.',
            'layanan_2_image'       => 'image/ElementProgram/building-construction-industry-5-svgrepo-com.svg',
            'layanan_2_slug'        => 'gedung-komersial',

            // Layanan Card 3
            'layanan_3_title'       => 'Renovasi Bangunan',
            'layanan_3_description' => 'Solusi renovasi cerdas untuk meningkatkan nilai estetika, fungsi, dan kenyamanan properti eksklusif Anda.',
            'layanan_3_image'       => 'image/ElementProgram/building-concrete-construction-svgrepo-com.svg',
            'layanan_3_slug'        => 'renovasi-bangunan',

            // Layanan Card 4
            'layanan_4_title'       => 'Konsultasi Ahli',
            'layanan_4_description' => 'Konsultasi perencanaan desain dan manajemen konstruksi mendalam untuk memastikan mahakarya Anda terwujud sempurna.',
            'layanan_4_image'       => 'image/ElementProgram/blueprint-building-construction-svgrepo-com.svg',
            'layanan_4_slug'        => 'konsultasi-ahli',

            // Footer
            'footer_company_text'  => 'Menghadirkan mahakarya arsitektur dan konstruksi dengan standar kualitas premium, presisi, dan dedikasi penuh.',
            'footer_tagline'       => 'Menjaga Kualitas Mewujud Berkah',
            'footer_eksplorasi_label' => 'Eksplorasi',
            'footer_keahlian_label'   => 'Keahlian Kami',
            'footer_keahlian_1'    => 'Hunian Mewah',
            'footer_keahlian_2'    => 'Komersial Premium',
            'footer_keahlian_3'    => 'Desain Interior',
            'footer_keahlian_4'    => 'Manajemen Konstruksi',
            'footer_kontak_label'  => 'Konsultasi',
            'footer_telepon'       => '+62 878 6530 9966',
            'footer_email'         => 'partners@indoberkahkonstruksi.com',
            'footer_facebook'      => 'https://web.facebook.com/profile.php?id=100094364576203',
            'footer_instagram'     => 'https://www.instagram.com/ibkonstruksi/',
        ];

        foreach ($defaults as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
