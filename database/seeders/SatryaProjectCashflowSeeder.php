<?php

namespace Database\Seeders;

use App\Models\CashflowOption;
use App\Models\Project;
use App\Models\User;
use Brick\Math\BigDecimal;
use Brick\Math\RoundingMode;
use Illuminate\Database\Seeder;

class SatryaProjectCashflowSeeder extends Seeder
{
    public function run(): void
    {
        $project = Project::where('nama_proyek', 'like', '%Satrya%')->first();
        if (! $project) {
            $owner = User::where('name', 'like', '%Satrya%')->first()
                ?? User::where('role', 'owner')->first()
                ?? User::factory()->create(['name' => 'Satrya', 'email' => 'satrya@example.com', 'role' => 'owner']);

            $project = Project::create([
                'user_id' => $owner->id,
                'nama_proyek' => 'Proyek Satrya',
                'alamat_proyek' => 'Penatih, Denpasar',
                'deskripsi' => 'Pembangunan Rumah Tinggal 2 Lantai',
                'kategori' => 'Pembangunan Rumah',
                'progress_percentage' => 85,
                'status' => 'aktif',
                'tanggal_mulai' => '2026-07-01',
                'tanggal_selesai' => '2026-12-15',
            ]);
        }

        $admin = User::where('role', 'admin')->first();
        $adminId = $admin?->id ?? 1;

        // Ensure options exist
        foreach (['INV', 'MAT', 'UPAH', 'SEWA', 'KONS', 'ELEC', 'PLUMB', 'SANI', 'LIGHT', 'CLEAN', 'FINISH'] as $codePrefix) {
            CashflowOption::remember('code', $codePrefix);
        }
        foreach (['termin', 'sak', 'rit', 'batang', 'hari', 'minggu', 'paket', 'm³', 'buah', 'dus', 'roll', 'm', 'unit', 'set'] as $unit) {
            CashflowOption::remember('unit', $unit);
        }

        $transactions = [
            // === JULI 2026 ===
            [
                'transaction_date' => '2026-07-05',
                'type' => 'penerimaan',
                'code' => 'INV-001',
                'description' => 'Pembayaran DP (Termin 1) Pembangunan Rumah Satrya',
                'unit' => 'termin',
                'quantity' => '1',
                'unit_price' => '75000000',
                'notes' => 'Transfer via Rekening BCA Klien Satrya',
            ],
            [
                'transaction_date' => '2026-07-08',
                'type' => 'pengeluaran',
                'code' => 'MAT-001',
                'description' => 'Pembelian Semen Gresik 100 Sak Fondasi',
                'unit' => 'sak',
                'quantity' => '100',
                'unit_price' => '65000',
                'notes' => 'Material fondasi cakar ayam dan sloof',
            ],
            [
                'transaction_date' => '2026-07-10',
                'type' => 'pengeluaran',
                'code' => 'MAT-002',
                'description' => 'Pasir Pasang dan Batu Kali 4 Truk',
                'unit' => 'rit',
                'quantity' => '4',
                'unit_price' => '1850000',
                'notes' => 'Pengurukan dan pasangan fondasi batu kali',
            ],
            [
                'transaction_date' => '2026-07-12',
                'type' => 'pengeluaran',
                'code' => 'MAT-003',
                'description' => 'Besi Beton Ulir 10mm & 12mm SNI',
                'unit' => 'batang',
                'quantity' => '80',
                'unit_price' => '82000',
                'notes' => 'Perakitan begel dan kolom struktur',
            ],
            [
                'transaction_date' => '2026-07-15',
                'type' => 'pengeluaran',
                'code' => 'SEWA-001',
                'description' => 'Sewa Mesin Molen Cor 3 Hari',
                'unit' => 'hari',
                'quantity' => '3',
                'unit_price' => '250000',
                'notes' => 'Pengecoran tapak dan sloof bawah',
            ],
            [
                'transaction_date' => '2026-07-20',
                'type' => 'pengeluaran',
                'code' => 'UPAH-001',
                'description' => 'Upah Tukang & Mandor Periode Minggu 1-2',
                'unit' => 'minggu',
                'quantity' => '2',
                'unit_price' => '5500000',
                'notes' => 'Upah 5 tukang dan 1 mandor lapangan',
            ],
            [
                'transaction_date' => '2026-07-25',
                'type' => 'pengeluaran',
                'code' => 'KONS-001',
                'description' => 'Konsumsi & Air Mineral Pekerja Lapangan',
                'unit' => 'paket',
                'quantity' => '1',
                'unit_price' => '750000',
                'notes' => 'Konsumsi lembur pengecoran',
            ],

            // === AGUSTUS 2026 ===
            [
                'transaction_date' => '2026-08-03',
                'type' => 'penerimaan',
                'code' => 'INV-002',
                'description' => 'Pembayaran Termin 2 (Progres Struktur Selesai 35%)',
                'unit' => 'termin',
                'quantity' => '1',
                'unit_price' => '60000000',
                'notes' => 'Termin kedua sesuai kesepakatan SPK',
            ],
            [
                'transaction_date' => '2026-08-06',
                'type' => 'pengeluaran',
                'code' => 'MAT-004',
                'description' => 'Bata Ringan Hebel 10cm Grade A',
                'unit' => 'm³',
                'quantity' => '15',
                'unit_price' => '750000',
                'notes' => 'Pasangan dinding lantai 1 & 2',
            ],
            [
                'transaction_date' => '2026-08-10',
                'type' => 'pengeluaran',
                'code' => 'MAT-005',
                'description' => 'Semen Mortar Perekat Hebel 40kg',
                'unit' => 'sak',
                'quantity' => '30',
                'unit_price' => '95000',
                'notes' => 'Perekat hebel dan plesteran instan',
            ],
            [
                'transaction_date' => '2026-08-15',
                'type' => 'pengeluaran',
                'code' => 'UPAH-002',
                'description' => 'Upah Pasang Dinding & Plester Minggu 3-4',
                'unit' => 'minggu',
                'quantity' => '2',
                'unit_price' => '5800000',
                'notes' => 'Penyelesaian dinding bata ringan',
            ],
            [
                'transaction_date' => '2026-08-18',
                'type' => 'pengeluaran',
                'code' => 'MAT-006',
                'description' => 'Rangka Baja Ringan Canal C75 & Reng Zincalume',
                'unit' => 'batang',
                'quantity' => '65',
                'unit_price' => '110000',
                'notes' => 'Struktur kuda-kuda atap utama',
            ],
            [
                'transaction_date' => '2026-08-22',
                'type' => 'pengeluaran',
                'code' => 'MAT-007',
                'description' => 'Genteng Beton Flat Cisankan Charcoal',
                'unit' => 'buah',
                'quantity' => '850',
                'unit_price' => '12500',
                'notes' => 'Penutup atap rumah utama',
            ],
            [
                'transaction_date' => '2026-08-28',
                'type' => 'pengeluaran',
                'code' => 'UPAH-003',
                'description' => 'Upah Pasang Rangka Atap dan Genteng',
                'unit' => 'paket',
                'quantity' => '1',
                'unit_price' => '6500000',
                'notes' => 'Pemasangan atap selesai tanpa bocor',
            ],

            // === SEPTEMBER 2026 ===
            [
                'transaction_date' => '2026-09-02',
                'type' => 'penerimaan',
                'code' => 'INV-003',
                'description' => 'Pembayaran Termin 3 (Progres Arsitektur 65%)',
                'unit' => 'termin',
                'quantity' => '1',
                'unit_price' => '50000000',
                'notes' => 'Pembayaran via Bank Mandiri Klien Satrya',
            ],
            [
                'transaction_date' => '2026-09-06',
                'type' => 'pengeluaran',
                'code' => 'MAT-008',
                'description' => 'Granit Tile 60x60 Glazed Polished Cream',
                'unit' => 'dus',
                'quantity' => '90',
                'unit_price' => '195000',
                'notes' => 'Lantai ruang tamu, keluarga, dan kamar',
            ],
            [
                'transaction_date' => '2026-09-10',
                'type' => 'pengeluaran',
                'code' => 'MAT-009',
                'description' => 'Semen Perekat Granit Tile On Tile MU-400',
                'unit' => 'sak',
                'quantity' => '25',
                'unit_price' => '105000',
                'notes' => 'Perekat granit lantai dan nat tahan air',
            ],
            [
                'transaction_date' => '2026-09-15',
                'type' => 'pengeluaran',
                'code' => 'ELEC-001',
                'description' => 'Instalasi Kabel Eterna NYM 3x2.5mm & Pipa Conduit',
                'unit' => 'roll',
                'quantity' => '5',
                'unit_price' => '820000',
                'notes' => 'Jalur instalasi listrik lantai 1 & 2',
            ],
            [
                'transaction_date' => '2026-09-20',
                'type' => 'pengeluaran',
                'code' => 'PLUMB-001',
                'description' => 'Pipa PVC Rucika AW & Fitting Saluran Air',
                'unit' => 'batang',
                'quantity' => '30',
                'unit_price' => '85000',
                'notes' => 'Instalasi air bersih toren dan pembuangan',
            ],
            [
                'transaction_date' => '2026-09-25',
                'type' => 'pengeluaran',
                'code' => 'UPAH-004',
                'description' => 'Upah Tukang Granit & Instalatur Listrik',
                'unit' => 'minggu',
                'quantity' => '2',
                'unit_price' => '5900000',
                'notes' => 'Pemasangan lantai granit selesai rapi',
            ],

            // === OKTOBER 2026 ===
            [
                'transaction_date' => '2026-10-03',
                'type' => 'penerimaan',
                'code' => 'INV-004',
                'description' => 'Pembayaran Termin 4 (Progres Finishing 85%)',
                'unit' => 'termin',
                'quantity' => '1',
                'unit_price' => '40000000',
                'notes' => 'Termin keempat pelunasan bertahap',
            ],
            [
                'transaction_date' => '2026-10-06',
                'type' => 'pengeluaran',
                'code' => 'MAT-010',
                'description' => 'Kusen Aluminium 4 inch Brown Anodize',
                'unit' => 'm',
                'quantity' => '45',
                'unit_price' => '140000',
                'notes' => 'Kusen jendela kaca dan pintu geser',
            ],
            [
                'transaction_date' => '2026-10-10',
                'type' => 'pengeluaran',
                'code' => 'MAT-011',
                'description' => 'Pintu Solid Kamper Samarinda & Kunci Dekson',
                'unit' => 'unit',
                'quantity' => '4',
                'unit_price' => '2100000',
                'notes' => 'Daun pintu utama dan kamar tidur',
            ],
            [
                'transaction_date' => '2026-10-15',
                'type' => 'pengeluaran',
                'code' => 'MAT-012',
                'description' => 'Cat Tembok Dulux Weathershield & Pentalite',
                'unit' => 'pail',
                'quantity' => '6',
                'unit_price' => '1650000',
                'notes' => 'Pengecatan dinding luar dan dalam',
            ],
            [
                'transaction_date' => '2026-10-19',
                'type' => 'pengeluaran',
                'code' => 'SANI-001',
                'description' => 'Kloset Duduk Toto Eco Washer & Shower Onda',
                'unit' => 'set',
                'quantity' => '2',
                'unit_price' => '3200000',
                'notes' => 'Sanitair kamar mandi lantai 1 & 2',
            ],
            [
                'transaction_date' => '2026-10-26',
                'type' => 'pengeluaran',
                'code' => 'UPAH-005',
                'description' => 'Upah Tukang Finishing Cat & Pemasangan Sanitair',
                'unit' => 'minggu',
                'quantity' => '2',
                'unit_price' => '5600000',
                'notes' => 'Finishing cat dasar dan pasang kusen pintu',
            ],

            // === NOVEMBER 2026 ===
            [
                'transaction_date' => '2026-11-02',
                'type' => 'penerimaan',
                'code' => 'INV-005',
                'description' => 'Pembayaran Termin 5 (Pelunasan Akhir Proyek 100%)',
                'unit' => 'termin',
                'quantity' => '1',
                'unit_price' => '25000000',
                'notes' => 'Pelunasan pekerjaan sebelum serah terima kunci',
            ],
            [
                'transaction_date' => '2026-11-04',
                'type' => 'pengeluaran',
                'code' => 'LIGHT-001',
                'description' => 'Lampu LED Downlight Philips & Saklar Schneider',
                'unit' => 'set',
                'quantity' => '24',
                'unit_price' => '85000',
                'notes' => 'Fitting lampu ruangan dan teras',
            ],
            [
                'transaction_date' => '2026-11-06',
                'type' => 'pengeluaran',
                'code' => 'CLEAN-001',
                'description' => 'Deep Cleaning & Pembersihan Pasca Konstruksi',
                'unit' => 'paket',
                'quantity' => '1',
                'unit_price' => '1500000',
                'notes' => 'Pembersihan kaca, lantai, dan sisa material',
            ],
            [
                'transaction_date' => '2026-11-08',
                'type' => 'pengeluaran',
                'code' => 'FINISH-001',
                'description' => 'Touch-up Cat & Aksesori Grendel Pintu',
                'unit' => 'paket',
                'quantity' => '1',
                'unit_price' => '750000',
                'notes' => 'Penyempurnaan akhir dan serah terima kunci',
            ],
        ];

        foreach ($transactions as $data) {
            $qty = $data['quantity'];
            $price = $data['unit_price'];
            $qtyMillis = BigDecimal::of($qty)->toScale(3, RoundingMode::Unnecessary)->getUnscaledValue()->toInt();
            $priceCents = BigDecimal::of($price)->toScale(2, RoundingMode::Unnecessary)->getUnscaledValue()->toInt();

            $project->cashflows()->create([
                'transaction_date' => $data['transaction_date'],
                'type' => $data['type'],
                'code' => $data['code'],
                'description' => $data['description'],
                'unit' => $data['unit'],
                'quantity_millis' => $qtyMillis,
                'unit_price_cents' => $priceCents,
                'notes' => $data['notes'],
                'created_by' => $adminId,
            ]);
        }
    }
}
