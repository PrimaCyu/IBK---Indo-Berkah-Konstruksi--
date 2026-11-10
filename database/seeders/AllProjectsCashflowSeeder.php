<?php

namespace Database\Seeders;

use App\Models\CashflowOption;
use App\Models\Project;
use App\Models\ProjectCashflow;
use App\Models\TransactionCategory;
use App\Models\User;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Seeder;

class AllProjectsCashflowSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        $adminId = $admin?->id ?? 1;

        // Ensure common options exist
        foreach (['INV', 'MAT', 'UPAH', 'SEWA', 'KONS', 'TRANS', 'ADM', 'OPS', 'ELEC', 'PLUMB', 'SANI', 'FINISH'] as $codePrefix) {
            CashflowOption::remember('code', $codePrefix);
        }
        foreach (['termin', 'sak', 'rit', 'batang', 'hari', 'minggu', 'paket', 'm³', 'm²', 'buah', 'dus', 'roll', 'm', 'unit', 'set', 'pcs'] as $unit) {
            CashflowOption::remember('unit', $unit);
        }

        // Fetch categories by slug
        $categories = TransactionCategory::all()->keyBy('slug');
        $catKlien = $categories->get('pembayaran-klien')?->id;
        $catMaterial = $categories->get('pembelian-material')?->id;
        $catGaji = $categories->get('gaji')?->id;
        $catPeralatan = $categories->get('peralatan')?->id;
        $catOperasional = $categories->get('operasional')?->id;
        $catTransport = $categories->get('transportasi')?->id;
        $catKonsumsi = $categories->get('konsumsi')?->id;
        $catAdmin = $categories->get('administrasi')?->id;

        // Helper to insert a cashflow item
        $createCashflow = function (Project $project, array $data) use ($adminId) {
            $qtyMillis = BigDecimal::of($data['quantity'])->toScale(3, RoundingMode::Unnecessary)->getUnscaledValue()->toInt();
            $priceCents = BigDecimal::of($data['unit_price'])->toScale(2, RoundingMode::Unnecessary)->getUnscaledValue()->toInt();
            $status = $data['status'] ?? 'lunas';

            return $project->cashflows()->create([
                'transaction_date' => $data['transaction_date'],
                'type' => $data['type'],
                'code' => $data['code'] ?? null,
                'description' => $data['description'],
                'unit' => $data['unit'] ?? null,
                'quantity_millis' => $qtyMillis,
                'unit_price_cents' => $priceCents,
                'category_id' => $data['category_id'] ?? null,
                'payment_method' => $data['payment_method'] ?? 'Transfer Bank',
                'notes' => $data['notes'] ?? null,
                'status' => $status,
                'created_by' => $adminId,
                'approved_by' => $status === 'lunas' ? $adminId : null,
                'approved_at' => $status === 'lunas' ? now() : null,
            ]);
        };

        // ==========================================
        // 1. PROYEK ADITYA PRATAMA
        // ==========================================
        $projectAditya = Project::where('nama_proyek', 'like', '%Aditya Pratama%')->first();
        if ($projectAditya) {
            if ($projectAditya->cashflows()->count() === 0) {
                $txAditya = [
                    // Agustus 2026
                    [
                        'transaction_date' => '2026-08-04',
                        'type' => 'penerimaan',
                        'code' => 'INV-001',
                        'description' => 'Pembayaran Uang Muka (DP 30%) Proyek Tebet',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '80000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank BCA',
                        'notes' => 'Transfer termin 1 setelah penandatanganan SPK',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-08-07',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-001',
                        'description' => 'Pembelian Semen Tiga Roda 80 Sak',
                        'unit' => 'sak',
                        'quantity' => '80',
                        'unit_price' => '65000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Material struktur cakar ayam dan sloof bawah',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-08-11',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-002',
                        'description' => 'Pasir Bangka Kasar & Split Cor 3 Truk Ritase',
                        'unit' => 'rit',
                        'quantity' => '3',
                        'unit_price' => '1700000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pengecoran tapak fondasi dan pondasi batu kali',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-08-18',
                        'type' => 'pengeluaran',
                        'code' => 'UPAH-001',
                        'description' => 'Upah Tukang & Kenek Tahap Fondasi (Minggu 1 & 2)',
                        'unit' => 'minggu',
                        'quantity' => '2',
                        'unit_price' => '3200000',
                        'category_id' => $catGaji,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Upah 4 tukang dan 2 laden',
                        'status' => 'lunas',
                    ],
                    // September 2026
                    [
                        'transaction_date' => '2026-09-02',
                        'type' => 'penerimaan',
                        'code' => 'INV-002',
                        'description' => 'Pembayaran Termin 2 (Struktur Kolom & Dinding Lantai 1)',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '60000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank BCA',
                        'notes' => 'Progress pekerjaan mencapai 40%',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-06',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-003',
                        'description' => 'Bata Ringan Hebel Tebal 10cm (12 Kubik)',
                        'unit' => 'm³',
                        'quantity' => '12',
                        'unit_price' => '650000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pasangan dinding keliling lantai 1',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-12',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-004',
                        'description' => 'Besi Beton Ulir D12 & Begel 8mm SNI',
                        'unit' => 'batang',
                        'quantity' => '95',
                        'unit_price' => '89500',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Perakitan kolom praktis dan balok gantung',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-22',
                        'type' => 'pengeluaran',
                        'code' => 'UPAH-002',
                        'description' => 'Upah Borongan Pasang Dinding Bata & Plester Aci',
                        'unit' => 'minggu',
                        'quantity' => '2',
                        'unit_price' => '3600000',
                        'category_id' => $catGaji,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Plesteran ruang tamu dan kamar tidur',
                        'status' => 'lunas',
                    ],
                    // Oktober 2026
                    [
                        'transaction_date' => '2026-10-05',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-005',
                        'description' => 'Keramik Granit Indogress 60x60 Glazed Polished',
                        'unit' => 'dus',
                        'quantity' => '40',
                        'unit_price' => '200000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Lantai utama dan teras depan',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-10-14',
                        'type' => 'pengeluaran',
                        'code' => 'SEWA-001',
                        'description' => 'Sewa Scaffolding & Catwalk 2 Minggu',
                        'unit' => 'set',
                        'quantity' => '10',
                        'unit_price' => '140000',
                        'category_id' => $catPeralatan,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pengerjaan plafon gipsum dan plesteran fasad',
                        'status' => 'lunas',
                    ],
                    // November 2026
                    [
                        'transaction_date' => '2026-11-03',
                        'type' => 'penerimaan',
                        'code' => 'INV-003',
                        'description' => 'Pembayaran Termin 3 (Plafon & Finishing Lantai)',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '40000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank BCA',
                        'notes' => 'Progress pekerjaan mencapai 75%',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-06',
                        'type' => 'pengeluaran',
                        'code' => 'KONS-001',
                        'description' => 'Konsumsi Lembur & Koordinasi Lapangan',
                        'unit' => 'paket',
                        'quantity' => '1',
                        'unit_price' => '650000',
                        'category_id' => $catKonsumsi,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Rapat koordinasi mingguan tim tukang',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-08',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-006',
                        'description' => 'Pengadaan Cat Dulux Pentalite & Weathershield 20L',
                        'unit' => 'pail',
                        'quantity' => '3',
                        'unit_price' => '1500000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Diajukan staf accounting: Pembelian cat eksterior anti bocor',
                        'status' => 'pending', // Menunggu persetujuan Manager!
                    ],
                ];

                foreach ($txAditya as $tx) {
                    $createCashflow($projectAditya, $tx);
                }
            }
        }

        // ==========================================
        // 2. PROYEK BUDI SANTOSO
        // ==========================================
        $projectBudi = Project::where('nama_proyek', 'like', '%Budi Santoso%')->first();
        if ($projectBudi) {
            if ($projectBudi->cashflows()->count() === 0) {
                $txBudi = [
                    // September 2026
                    [
                        'transaction_date' => '2026-09-03',
                        'type' => 'penerimaan',
                        'code' => 'INV-001',
                        'description' => 'Pembayaran Uang Muka (DP 35%) Pembangunan Rumah BSD City',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '95000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank Mandiri',
                        'notes' => 'Penerimaan DP termin 1 pembangunan rumah baru',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-05',
                        'type' => 'pengeluaran',
                        'code' => 'ADM-001',
                        'description' => 'Biaya Pengurusan PBG / IMB & Cetak Gambar Kerja Arsitek',
                        'unit' => 'paket',
                        'quantity' => '1',
                        'unit_price' => '6500000',
                        'category_id' => $catAdmin,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Retribusi perizinan bangunan dan legalitas site',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-08',
                        'type' => 'pengeluaran',
                        'code' => 'OPS-001',
                        'description' => 'Pembersihan Lahan, Pemagaran Seng & Bouwplank Kayu Meranti',
                        'unit' => 'paket',
                        'quantity' => '1',
                        'unit_price' => '3200000',
                        'category_id' => $catOperasional,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Persiapan lapangan dan penentuan titik koordinat as bangunan',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-15',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-001',
                        'description' => 'Batu Belah Belitung & Pasir Cor 5 Truk Ritase',
                        'unit' => 'rit',
                        'quantity' => '5',
                        'unit_price' => '1750000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pasangan batu kali fondasi lajur',
                        'status' => 'lunas',
                    ],
                    // Oktober 2026
                    [
                        'transaction_date' => '2026-10-02',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-002',
                        'description' => 'Besi Ulir D16 & D13 SNI Krakatau Steel untuk Balok',
                        'unit' => 'batang',
                        'quantity' => '100',
                        'unit_price' => '145000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pembesian sloof bawah dan kolom lantai dasar',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-10-10',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-003',
                        'description' => 'Beton Ready Mix Cor K-250 (Pioneer / Merah Putih)',
                        'unit' => 'm³',
                        'quantity' => '14',
                        'unit_price' => '800000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pengecoran dak lantai 1 dan sloof bertulang',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-10-18',
                        'type' => 'pengeluaran',
                        'code' => 'UPAH-001',
                        'description' => 'Upah Tim Mandor & Tukang Cor Struktur Beton',
                        'unit' => 'minggu',
                        'quantity' => '2',
                        'unit_price' => '4400000',
                        'category_id' => $catGaji,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Pengerjaan bekisting, pembesian dan vibrator cor',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-10-25',
                        'type' => 'pengeluaran',
                        'code' => 'TRANS-001',
                        'description' => 'Biaya BBM & Sewa Pick-up Angkut Pipa & Kayu Bekisting',
                        'unit' => 'rit',
                        'quantity' => '5',
                        'unit_price' => '250000',
                        'category_id' => $catTransport,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Mobilitas pengangkutan material lapangan',
                        'status' => 'lunas',
                    ],
                    // November 2026
                    [
                        'transaction_date' => '2026-11-02',
                        'type' => 'penerimaan',
                        'code' => 'INV-002',
                        'description' => 'Pembayaran Termin 2 (Selesai Pengecoran Lantai 1)',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '75000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank Mandiri',
                        'notes' => 'Klien puas dengan hasil uji tekan sampel beton',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-04',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-004',
                        'description' => 'Bata Merah Press Cikarang Berkualitas (8.000 Pcs)',
                        'unit' => 'buah',
                        'quantity' => '8000',
                        'unit_price' => '800',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pasangan dinding bata lantai 1',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-07',
                        'type' => 'pengeluaran',
                        'code' => 'KONS-001',
                        'description' => 'Konsumsi Mingguan & Air Mineral Galon Tim Lapangan',
                        'unit' => 'minggu',
                        'quantity' => '1',
                        'unit_price' => '850000',
                        'category_id' => $catKonsumsi,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Kebutuhan konsumsi pekerja proyek',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-09',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-005',
                        'description' => 'Semen Mortar Perekat Hebel & Plester Acian MU-380',
                        'unit' => 'sak',
                        'quantity' => '35',
                        'unit_price' => '120000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Diajukan staf accounting: Pengadaan perekat bata untuk persiapan dinding lantai 2',
                        'status' => 'pending', // Menunggu persetujuan Manager!
                    ],
                ];

                foreach ($txBudi as $tx) {
                    $createCashflow($projectBudi, $tx);
                }
            }
        }

        // ==========================================
        // 3. PROYEK KADEK PRIMA
        // ==========================================
        $projectKadek = Project::where('nama_proyek', 'like', '%Kadek Prima%')->first();
        if ($projectKadek) {
            // Clean HTML tag from alamat if present
            if (str_contains($projectKadek->alamat_proyek, '<p>')) {
                $projectKadek->update([
                    'alamat_proyek' => strip_tags($projectKadek->alamat_proyek),
                ]);
            }

            if ($projectKadek->cashflows()->count() === 0) {
                $txKadek = [
                    // Agustus 2026
                    [
                        'transaction_date' => '2026-08-08',
                        'type' => 'penerimaan',
                        'code' => 'INV-001',
                        'description' => 'Pembayaran Uang Muka (DP 35%) Pembangunan Villa Bali Modern',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '110000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank BCA',
                        'notes' => 'DP awal proyek pembangunan villa di Denpasar Utara',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-08-12',
                        'type' => 'pengeluaran',
                        'code' => 'OPS-001',
                        'description' => 'Pekerjaan Pondasi Bored Pile & Penggalian Tanah Keras',
                        'unit' => 'titik',
                        'quantity' => '16',
                        'unit_price' => '750000',
                        'category_id' => $catOperasional,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pengeboran tiang pancang setempat untuk tanah labil',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-08-16',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-001',
                        'description' => 'Besi Beton Ulir D13 & D10 SNI 150 Batang',
                        'unit' => 'batang',
                        'quantity' => '150',
                        'unit_price' => '110000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Kerangka struktur kolom cakar ayam',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-08-22',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-002',
                        'description' => 'Pasir Muntilan & Kerikil Cor Beton Kualitas Bagus',
                        'unit' => 'rit',
                        'quantity' => '4',
                        'unit_price' => '1950000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pengadaan material adukan semen cor',
                        'status' => 'lunas',
                    ],
                    // September 2026
                    [
                        'transaction_date' => '2026-09-04',
                        'type' => 'pengeluaran',
                        'code' => 'UPAH-001',
                        'description' => 'Upah Tukang & Mandor Struktur Fondasi Tahap Awal',
                        'unit' => 'minggu',
                        'quantity' => '2',
                        'unit_price' => '6250000',
                        'category_id' => $catGaji,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Upah 8 tukang dan tenaga kerja lokal Bali',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-10',
                        'type' => 'pengeluaran',
                        'code' => 'SEWA-001',
                        'description' => 'Sewa Pompa Beton (Concrete Pump) & Vibrator Cor',
                        'unit' => 'hari',
                        'quantity' => '2',
                        'unit_price' => '1750000',
                        'category_id' => $catPeralatan,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Pengecoran plat lantai dasar dan sloof keliling',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-18',
                        'type' => 'penerimaan',
                        'code' => 'INV-002',
                        'description' => 'Pembayaran Termin 2 (Pondasi & Struktur Bawah Selesai 100%)',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '80000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank BCA',
                        'notes' => 'Disetujui setelah inspeksi kekuatan beton fondasi',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-09-24',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-003',
                        'description' => 'Bata Ringan Hebel AAC & Semen Perekat 15 Kubik',
                        'unit' => 'm³',
                        'quantity' => '15',
                        'unit_price' => '650000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Dinding villa lantai 1 dan area kolam',
                        'status' => 'lunas',
                    ],
                    // Oktober 2026
                    [
                        'transaction_date' => '2026-10-06',
                        'type' => 'pengeluaran',
                        'code' => 'MAT-004',
                        'description' => 'Rangka Atap Baja Ringan Zincalume C75 & Reng',
                        'unit' => 'm²',
                        'quantity' => '120',
                        'unit_price' => '123300',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Kuda-kuda dan rangka atap arsitektur Bali modern',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-10-16',
                        'type' => 'pengeluaran',
                        'code' => 'UPAH-002',
                        'description' => 'Upah Borongan Pemasangan Rangka Baja & Genteng',
                        'unit' => 'paket',
                        'quantity' => '1',
                        'unit_price' => '5500000',
                        'category_id' => $catGaji,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Penyelesaian rangka utama atap miring',
                        'status' => 'lunas',
                    ],
                    // November 2026
                    [
                        'transaction_date' => '2026-11-01',
                        'type' => 'penerimaan',
                        'code' => 'INV-003',
                        'description' => 'Pembayaran Termin 3 (Pemasangan Atap & Dinding Plester)',
                        'unit' => 'termin',
                        'quantity' => '1',
                        'unit_price' => '55000000',
                        'category_id' => $catKlien,
                        'payment_method' => 'Transfer Bank BCA',
                        'notes' => 'Progress pekerjaan fisik mencapai 70%',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-05',
                        'type' => 'pengeluaran',
                        'code' => 'TRANS-001',
                        'description' => 'Ongkos Kirim Truk & Distribusi Genteng Keramik Kanmuri',
                        'unit' => 'rit',
                        'quantity' => '2',
                        'unit_price' => '800000',
                        'category_id' => $catTransport,
                        'payment_method' => 'Kas Tunai',
                        'notes' => 'Pengiriman material genteng dari Denpasar ke site',
                        'status' => 'lunas',
                    ],
                    [
                        'transaction_date' => '2026-11-08',
                        'type' => 'pengeluaran',
                        'code' => 'PLUMB-001',
                        'description' => 'Pengadaan Pipa PVC Rucika AW & Aksesoris Plumbing Kolam',
                        'unit' => 'batang',
                        'quantity' => '35',
                        'unit_price' => '110000',
                        'category_id' => $catMaterial,
                        'payment_method' => 'Transfer Bank',
                        'notes' => 'Diajukan staf accounting: Pengadaan instalasi pipa air bersih & pembuangan swimming pool',
                        'status' => 'pending', // Menunggu persetujuan Manager!
                    ],
                ];

                foreach ($txKadek as $tx) {
                    $createCashflow($projectKadek, $tx);
                }
            }
        }

        // ==========================================
        // 4. PROYEK SATRYA (Pastikan ada 1 pending untuk testing)
        // ==========================================
        $projectSatrya = Project::where('nama_proyek', 'like', '%Satrya%')->first();
        if ($projectSatrya) {
            $hasPending = $projectSatrya->cashflows()->where('status', 'pending')->exists();
            if (! $hasPending) {
                $createCashflow($projectSatrya, [
                    'transaction_date' => '2026-11-09',
                    'type' => 'pengeluaran',
                    'code' => 'LIGHT-002',
                    'description' => 'Pengadaan Lampu Spotlight Taman LED Warm White',
                    'unit' => 'buah',
                    'quantity' => '8',
                    'unit_price' => '156250',
                    'category_id' => $catMaterial,
                    'payment_method' => 'Kas Tunai',
                    'notes' => 'Diajukan staf accounting: Lampu taman estetika area fasad depan',
                    'status' => 'pending', // Menunggu persetujuan Manager!
                ]);
            }
        }
    }
}

