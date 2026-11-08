<?php

namespace Database\Seeders;

use App\Models\Berita;
use App\Models\KategoriBerita;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class BeritaDummySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Pastikan Kategori Tersedia
        $kategoriArsitektur = KategoriBerita::firstOrCreate(
            ['slug' => 'arsitektur-desain'],
            ['nama' => 'Arsitektur & Desain']
        );

        $kategoriKonstruksi = KategoriBerita::firstOrCreate(
            ['slug' => 'tips-konstruksi'],
            ['nama' => 'Tips Konstruksi']
        );

        $kategoriTeknologi = KategoriBerita::firstOrCreate(
            ['slug' => 'inovasi-teknologi'],
            ['nama' => 'Inovasi & Teknologi']
        );

        $kategoriPerusahaan = KategoriBerita::firstOrCreate(
            ['slug' => 'kabar-ibk'],
            ['nama' => 'Kabar Perusahaan']
        );

        $kategoriProyek = KategoriBerita::firstOrCreate(
            ['slug' => 'update-proyek'],
            ['nama' => 'Update Proyek']
        );

        // Koleksi 10 dummy berita
        $beritas = [
            [
                'title' => 'Inovasi Struktur Beton Pracetak untuk Efisiensi Pembangunan Gedung Modern',
                'kategori_id' => $kategoriTeknologi->id,
                'image' => 'portfolios/01M1NGVNVV3XP3NTMFB18CENED.webp',
                'views' => 2480,
                'days_ago' => 1,
                'content' => '<p>Penerapan teknologi beton pracetak (precast concrete) kian menjadi standar unggulan dalam proyek konstruksi berskala menengah hingga besar di Indonesia. Metode ini menawarkan kepresisian tinggi serta memangkas durasi pekerjaan struktur hingga 40% dibandingkan metode konvensional.</p><p>Tim ahli <strong>PT Indo Berkah Konstruksi</strong> menerapkan quality control ketat sejak tahap pabrikasi hingga ereksi di lapangan guna memastikan setiap sambungan modul memiliki ketahanan gempa optimal sesuai SNI terbaru.</p><h2>Keunggulan Efisiensi & Kualitas</h2><p>Dengan fabrikasi di lingkungan terkontrol, risiko segregasi beton dan penyusutan dapat diminimalisir secara signifikan. Hasil permukaan struktur lebih presisi, rata, dan siap menerima finishing akhir dengan rapi.</p>',
            ],
            [
                'title' => 'Tren Desain Rumah Tropis Modern: Kombinasi Elemen Kayu dan Sirkulasi Udara Alami',
                'kategori_id' => $kategoriArsitektur->id,
                'image' => 'portfolios/01M1NH5A7V3KY1MY3307P8EE6M.jpg',
                'views' => 1935,
                'days_ago' => 2,
                'content' => '<p>Iklim tropis Indonesia menuntut pendekatan arsitektur yang cermat terhadap pencahayaan alami dan sirkulasi silang. Desain rumah tropis modern saat ini berfokus pada bukaan lebar, inner courtyard, dan kanopi peneduh untuk mereduksi panas berlebih.</p><p>Penggunaan aksen kayu ulin atau bengkirai pada fasad memberikan kehangatan visual sekaligus ketahanan cuaca jangka panjang bila dirawat dengan coating pelindung UV yang tepat.</p>',
            ],
            [
                'title' => 'Panduan Lengkap Memilih Jenis Pondasi Bangunan Sesuai Karakteristik Daya Dukung Tanah',
                'kategori_id' => $kategoriKonstruksi->id,
                'image' => 'progress-photos/01M1AKYEMX9YZXX93ZVCABZ53W.jpg',
                'views' => 1620,
                'days_ago' => 3,
                'content' => '<p>Kekuatan dan ketahanan bangunan berakar pada kestabilan pondasi di bawahnya. Sebelum menentukan jenis pondasi—baik footplate (cakar ayam), bore pile, maupun tiang pancang—uji tanah (sondir test) mutlak dilakukan untuk mengetahui kedalaman lapisan tanah keras.</p><p>Kesalahan umum seperti meremehkan daya dukung tanah lunak dapat mengakibatkan penurunan bangunan (differential settlement) yang meretakkan dinding dan struktur utama.</p>',
            ],
            [
                'title' => 'PT Indo Berkah Konstruksi Raih Sertifikasi Standar Manajemen Mutu dan Keselamatan K3',
                'kategori_id' => $kategoriPerusahaan->id,
                'image' => 'portfolios/01M1NHC4579MMVMZNEVQFGGY15.jpg',
                'views' => 3150,
                'days_ago' => 4,
                'content' => '<p>Komitmen terhadap zero-accident dan kualitas mutu pekerjaan membawa PT Indo Berkah Konstruksi kembali meraih sertifikasi kepatuhan manajemen K3 terintegrasi. Standar ini mencakup protokol keselamatan kerja di ketinggian, sanitasi proyek, dan audit berkala.</p><p>Bagi kami, keselamatan tenaga kerja dan ketepatan waktu serah terima proyek adalah wujud tanggung jawab profesional tertinggi kepada para klien dan mitra strategis.</p>',
            ],
            [
                'title' => 'Mengenal Konsep Green Building: Langkah Nyata Mewujudkan Hunian Rendah Emisi',
                'kategori_id' => $kategoriTeknologi->id,
                'image' => 'portfolios/01M1NHNZ9X02MMTB633HYKKMF4.jpg',
                'views' => 1240,
                'days_ago' => 5,
                'content' => '<p>Bangunan ramah lingkungan bukan sekadar menanam pohon di halaman, melainkan integrasi sistem energi efisien, pemanfaatan panel surya fotovoltaik, serta sistem pemanenan air hujan (rainwater harvesting) untuk kebutuhan sanitasi dan taman.</p><p>Investasi awal pada fitur green building terbukti menghemat biaya operasional listrik dan air bulanan hingga 35% sepanjang siklus hidup bangunan.</p>',
            ],
            [
                'title' => '5 Kesalahan Fatal Saat Renovasi Rumah Tua dan Langkah Antisipasi yang Wajib Diketahui',
                'kategori_id' => $kategoriKonstruksi->id,
                'image' => 'portfolios/01M22XDKEA8VC5Y4XYFRCVE58T.jpg',
                'views' => 2810,
                'days_ago' => 6,
                'content' => '<p>Membongkar dinding tanpa memeriksa jalur struktur kolom utama adalah kesalahan paling sering terjadi pada renovasi rumah lama. Hal ini dapat memicu retak geser hingga keruntuhan parsial pada lantai atas.</p><p>Pastikan selalu memeriksa kondisi instalasi listrik lama yang rawan aus, sistem perpipaan di dalam dinding, serta kelembapan kapiler pada sloof bawah sebelum memulai pekerjaan finishing baru.</p>',
            ],
            [
                'title' => 'Pemanfaatan Rangka Baja Ringan Galvalum: Solusi Struktur Atap Kuat, Ringan, dan Anti Karat',
                'kategori_id' => $kategoriKonstruksi->id,
                'image' => 'portfolios/01M26WTRX0MQF12W1RM5HQBQBH.jpeg',
                'views' => 975,
                'days_ago' => 7,
                'content' => '<p>Baja ringan High Tensile G550 dengan lapisan zinc-aluminium menjadi primadona pengganti kayu untuk kuda-kuda atap. Bobotnya yang ringan meringankan beban gempa pada kolom dan pondasi secara signifikan.</p><p>Kunci keberhasilan rangka baja ringan terletak pada perhitungan software analitis beban angin dan genteng, serta jarak reng yang presisi untuk menghindari lendutan jangka panjang.</p>',
            ],
            [
                'title' => 'Update Proyek: Perkembangan Konstruksi Gedung Komersial Terpadu Memasuki Tahap Topping-Off',
                'kategori_id' => $kategoriProyek->id,
                'image' => 'portfolios/01M26X0KTFAPCMC1SAP61JANY9.jpeg',
                'views' => 1890,
                'days_ago' => 8,
                'content' => '<p>Proyek pembangunan pusat komersial dan perkantoran modern yang dikerjakan tim Indo Berkah Konstruksi telah sukses mencapai tahap topping-off struktur lantai 5 sesuai timeline yang disepakati.</p><p>Pekerjaan kini dilanjutkan pada instalasi MEP (Mechanical, Electrical, and Plumbing) serta pemasangan fasad kaca curtain wall berinsulasi termal tinggi.</p>',
            ],
            [
                'title' => 'Strategi Manajemen Rencana Anggaran Biaya (RAB) Agar Proyek Tidak Mengalami Pembengkakan',
                'kategori_id' => $kategoriKonstruksi->id,
                'image' => 'portfolios/01M26X2GXFP46QG2BGK6NNDYM0.jpeg',
                'views' => 3420,
                'days_ago' => 9,
                'content' => '<p>Transparansi dan ketelitian volume material adalah fondasi utama penyusunan RAB yang sehat. Tanpa Bill of Quantities (BoQ) yang mendetail, perubahan desain di tengah jalan sering kali melipatgandakan pengeluaran.</p><p>Indo Berkah Konstruksi menerapkan sistem kontrak transparan dengan perincian spesifikasi material jelas sejak awal, memberi kepastian biaya tanpa biaya siluman bagi pemilik proyek.</p>',
            ],
            [
                'title' => 'Tips Memilih Cat Eksterior Tahan Cuaca Ekstrem untuk Melindungi Fasad Bangunan',
                'kategori_id' => $kategoriArsitektur->id,
                'image' => 'portfolios/01M26X5ASR86G8WYQ7W1XM87B9.jpeg',
                'views' => 1105,
                'days_ago' => 10,
                'content' => '<p>Paparan sinar matahari terik dan curah hujan tinggi di Indonesia dapat membuat cat eksterior memudar, mengelupas, atau ditumbuhi jamur hanya dalam hitungan tahun. Pemilihan cat berbahan dasar 100% akrilik dengan teknologi elastomeric adalah investasi terbaik.</p><p>Aplikasi cat dasar (sealer) alkali-resisting wajib dilakukan untuk menahan rembesan garam alkali dari plesteran semen baru agar warna akhir tetap cerah dan terlindungi hingga bertahun-tahun.</p>',
            ],
        ];

        foreach ($beritas as $item) {
            $slug = Str::slug($item['title']);

            Berita::updateOrCreate(
                ['slug' => $slug],
                [
                    'kategori_berita_id' => $item['kategori_id'],
                    'title' => $item['title'],
                    'image' => $item['image'],
                    'content' => $item['content'],
                    'is_published' => true,
                    'views' => $item['views'],
                    'created_at' => now()->subDays($item['days_ago']),
                    'updated_at' => now()->subDays($item['days_ago']),
                ]
            );
        }
    }
}
