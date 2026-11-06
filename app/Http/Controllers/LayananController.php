<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LayananController extends Controller
{
    private $services = [
        'pembangunan-rumah' => [
            'title' => 'Pembangunan Rumah',
            'icon' => 'image/ElementProgram/building-construction-industry-18-svgrepo-com.svg',
            'description' => 'Kami menyediakan layanan pembangunan rumah dari tahap perencanaan hingga selesai dengan desain mewah dan material premium. Wujudkan rumah impian Anda bersama tim ahli kami yang berpengalaman.',
            'steps' => [
                ['title' => 'Konsultasi Awal', 'desc' => 'Diskusi mengenai visi, kebutuhan, dan anggaran pembangunan rumah Anda.'],
                ['title' => 'Survei Lokasi', 'desc' => 'Tim kami akan melakukan pengecekan langsung ke lokasi lahan Anda.'],
                ['title' => 'Desain & RAB', 'desc' => 'Pembuatan desain arsitektur 3D dan Rencana Anggaran Biaya (RAB) secara transparan.'],
                ['title' => 'Tanda Tangan Kontrak', 'desc' => 'Kesepakatan kerja dan penandatanganan kontrak pembangunan.'],
                ['title' => 'Pelaksanaan Konstruksi', 'desc' => 'Proses pembangunan yang diawasi ketat untuk menjamin kualitas terbaik.'],
                ['title' => 'Serah Terima Kunci', 'desc' => 'Penyerahan hasil bangunan yang sudah selesai 100% dan siap huni.']
            ]
        ],
        'gedung-komersial' => [
            'title' => 'Gedung Komersial',
            'icon' => 'image/ElementProgram/building-construction-industry-5-svgrepo-com.svg',
            'description' => 'Melayani pembangunan gedung komersial dengan standar internasional, fokus pada efisiensi serta ketahanan struktur untuk bisnis Anda.',
            'steps' => [
                ['title' => 'Briefing Proyek', 'desc' => 'Pemahaman mengenai skala, fungsi, dan spesifikasi gedung komersial.'],
                ['title' => 'Feasibility Study & Survei', 'desc' => 'Studi kelayakan dan analisis lahan untuk konstruksi skala besar.'],
                ['title' => 'Perencanaan & Estimasi', 'desc' => 'Pembuatan rancangan struktur, MEP, dan perhitungan biaya (RAB).'],
                ['title' => 'Legalitas & Kontrak', 'desc' => 'Pengurusan perizinan (opsional) dan penandatanganan kesepakatan kerjasama.'],
                ['title' => 'Pelaksanaan Proyek', 'desc' => 'Konstruksi dengan standar K3 dan pelaporan progres secara berkala.'],
                ['title' => 'Handover', 'desc' => 'Serah terima proyek yang siap dioperasikan untuk bisnis Anda.']
            ]
        ],
        'renovasi-bangunan' => [
            'title' => 'Renovasi Bangunan',
            'icon' => 'image/ElementProgram/building-concrete-construction-svgrepo-com.svg',
            'description' => 'Solusi renovasi cerdas untuk meningkatkan nilai estetika, fungsi, dan kenyamanan properti eksklusif Anda, baik interior maupun eksterior.',
            'steps' => [
                ['title' => 'Konsultasi Renovasi', 'desc' => 'Sampaikan bagian bangunan yang ingin Anda perbaiki atau perbarui.'],
                ['title' => 'Inspeksi Kondisi Existing', 'desc' => 'Pengecekan struktur dan kondisi bangunan saat ini.'],
                ['title' => 'Usulan Desain & Biaya', 'desc' => 'Penyusunan desain perombakan dan perhitungan estimasi biaya yang efisien.'],
                ['title' => 'Persetujuan Kontrak', 'desc' => 'Persetujuan timeline renovasi dan penandatanganan surat kerja.'],
                ['title' => 'Pekerjaan Renovasi', 'desc' => 'Pelaksanaan renovasi dengan meminimalisir gangguan di area sekitar.'],
                ['title' => 'Final Checking', 'desc' => 'Pengecekan kualitas akhir sebelum serah terima pekerjaan renovasi.']
            ]
        ],
        'konsultasi-ahli' => [
            'title' => 'Konsultasi Ahli',
            'icon' => 'image/ElementProgram/blueprint-building-construction-svgrepo-com.svg',
            'description' => 'Konsultasi perencanaan desain dan manajemen konstruksi mendalam untuk memastikan mahakarya Anda terwujud sempurna tanpa kesalahan fatal.',
            'steps' => [
                ['title' => 'Penjadwalan Sesi', 'desc' => 'Menentukan waktu untuk bertemu atau konsultasi secara virtual.'],
                ['title' => 'Penyampaian Ide', 'desc' => 'Anda menyampaikan gambaran kasar dan kendala proyek yang sedang/akan berjalan.'],
                ['title' => 'Analisis Profesional', 'desc' => 'Ahli kami melakukan kajian dari sisi arsitektur, sipil, atau tata ruang.'],
                ['title' => 'Pemberian Solusi', 'desc' => 'Penyampaian rekomendasi material, desain, atau perbaikan struktural.'],
                ['title' => 'Rencana Tindak Lanjut', 'desc' => 'Penyusunan langkah konkret (blueprint/jadwal) untuk mengeksekusi solusi.']
            ]
        ],
    ];

    public function show($slug)
    {
        if (array_key_exists($slug, $this->services)) {
            $service = $this->services[$slug];
        } else {
            // Fallback dinamis jika admin mengubah slug dari Filament / Site Settings
            $title = ucwords(str_replace('-', ' ', $slug));
            $service = [
                'title' => $title,
                'icon' => 'image/ElementProgram/blueprint-building-construction-svgrepo-com.svg',
                'description' => 'Layanan profesional ' . $title . ' yang dirancang khusus untuk memenuhi standar kualitas premium dan kebutuhan spesifik proyek Anda.',
                'steps' => [
                    ['title' => 'Konsultasi Awal', 'desc' => 'Diskusi mendalam mengenai visi, kebutuhan teknis, dan rancangan anggaran proyek Anda.'],
                    ['title' => 'Survei & Analisis', 'desc' => 'Tim ahli kami akan melakukan pengecekan dan studi kelayakan secara langsung.'],
                    ['title' => 'Desain & RAB', 'desc' => 'Pembuatan rancangan (desain) dan Rencana Anggaran Biaya (RAB) secara transparan.'],
                    ['title' => 'Kesepakatan Kontrak', 'desc' => 'Penandatanganan kontrak kerja yang jelas dan berorientasi pada kepuasan klien.'],
                    ['title' => 'Pelaksanaan Proyek', 'desc' => 'Proses eksekusi pekerjaan yang diawasi dengan standar K3 dan kontrol kualitas yang ketat.'],
                    ['title' => 'Serah Terima', 'desc' => 'Penyerahan hasil akhir pekerjaan yang telah melewati tahap quality checking 100%.']
                ]
            ];
        }

        $service['slug'] = $slug;

        return view('frontend.layanan.show', compact('service'));
    }
}
