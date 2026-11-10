<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCashflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectCashflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_decimal_precision_and_half_up_rounding_work_accurately(): void
    {
        // 0.001 * 5.00 = 0.005 -> rounds to 0.01
        $this->assertSame('0.01', (string) ProjectCashflow::calculateTotal('0.001', '5.00'));
        // 0.001 * 4.00 = 0.004 -> rounds to 0.00
        $this->assertSame('0.00', (string) ProjectCashflow::calculateTotal('0.001', '4.00'));
        // 1.250 * 50000.00 = 62500.00
        $this->assertSame('62500.00', (string) ProjectCashflow::calculateTotal('1.250', '50000.00'));
    }

    public function test_staff_can_store_income_and_expense_with_optional_fields(): void
    {
        /** @var User $accountant */
        $accountant = User::factory()->create(['role' => 'accounting']);
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner);

        $this->actingAs($accountant)->post(route('admin.cashbook.store', $project), [
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'code' => null,
            'description' => 'Pembayaran DP Pertama Klien',
            'unit' => null,
            'quantity' => '1',
            'unit_price' => '25000000',
            'notes' => 'Transfer rekening operasional',
        ])->assertRedirect(route('admin.cashbook.show', $project))->assertSessionHas('success');

        $this->assertDatabaseHas('project_cashflows', [
            'project_id' => $project->id,
            'type' => 'penerimaan',
            'code' => null,
            'unit' => null,
            'quantity_millis' => 1000,
            'unit_price_cents' => 2500000000,
        ]);

        $cashflow = $project->cashflows()->firstOrFail();
        $this->assertSame('1.000', $cashflow->quantity);
        $this->assertSame('25000000.00', $cashflow->unit_price);
        $this->assertSame('25000000.00', $cashflow->total);
    }

    public function test_ledger_calculates_running_balance_and_summary_correctly(): void
    {
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner);

        // Transaction 1: Penerimaan 50.000.000 (2026-11-01)
        $project->cashflows()->create([
            'transaction_date' => '2026-11-01',
            'type' => 'penerimaan',
            'code' => 'IN01',
            'description' => 'DP Termin 1',
            'unit' => 'termin',
            'quantity_millis' => 1000,
            'unit_price_cents' => 5000000000,
        ]);

        // Transaction 2: Pengeluaran 15.000.000 (2026-11-03)
        $project->cashflows()->create([
            'transaction_date' => '2026-11-03',
            'type' => 'pengeluaran',
            'code' => 'OUT01',
            'description' => 'Pembelian Semen & Pasir',
            'unit' => 'paket',
            'quantity_millis' => 1000,
            'unit_price_cents' => 1500000000,
        ]);

        // Transaction 3: Pengeluaran 5.500.000 (2026-11-05)
        $project->cashflows()->create([
            'transaction_date' => '2026-11-05',
            'type' => 'pengeluaran',
            'code' => 'OUT02',
            'description' => 'Upah Tukang Minggu 1',
            'unit' => 'minggu',
            'quantity_millis' => 1000,
            'unit_price_cents' => 550000000,
        ]);

        $ledger = ProjectCashflow::ledger($project);

        $this->assertSame('50000000.00', $ledger['income']);
        $this->assertSame('20500000.00', $ledger['expense']);
        $this->assertSame('29500000.00', $ledger['balance']);

        $transactions = $ledger['transactions'];
        $this->assertCount(3, $transactions);
        $this->assertSame('50000000.00', $transactions[0]->running_balance);
        $this->assertSame('35000000.00', $transactions[1]->running_balance);
        $this->assertSame('29500000.00', $transactions[2]->running_balance);
    }

    public function test_staff_can_update_and_destroy_cashflow_with_scoped_binding_protection(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'owner']);
        $projectA = $this->project($owner, 'Proyek A');
        $projectB = $this->project($owner, 'Proyek B');

        $cashflowA = $projectA->cashflows()->create([
            'transaction_date' => '2026-11-08',
            'type' => 'pengeluaran',
            'code' => 'EXP-A',
            'description' => 'Biaya Logistik Proyek A',
            'unit' => 'rit',
            'quantity_millis' => 2000,
            'unit_price_cents' => 75000000, // 2 x 750.000 = 1.500.000
        ]);

        $this->actingAs($admin);

        // Update Project A's cashflow
        $this->put(route('admin.cashbook.update', [$projectA, $cashflowA]), [
            'transaction_date' => '2026-11-08',
            'type' => 'pengeluaran',
            'code' => 'EXP-A-REV',
            'description' => 'Biaya Logistik Proyek A (Revisi)',
            'unit' => 'rit',
            'quantity' => '3',
            'unit_price' => '750000',
        ])->assertRedirect(route('admin.cashbook.show', $projectA))->assertSessionHas('success');

        $this->assertDatabaseHas('project_cashflows', [
            'id' => $cashflowA->id,
            'code' => 'EXP-A-REV',
            'quantity_millis' => 3000,
            'updated_by' => $admin->id,
        ]);
        $this->assertNotNull($cashflowA->fresh()->last_edited_at);

        // Attempt to update Project A's cashflow under Project B URL (must fail 404)
        $this->put(route('admin.cashbook.update', [$projectB, $cashflowA]), [
            'transaction_date' => '2026-11-08',
            'type' => 'pengeluaran',
            'description' => 'Percobaan sabotase cross project',
            'quantity' => '1',
            'unit_price' => '1000',
        ])->assertNotFound();

        // Attempt to delete Project A's cashflow under Project B URL (must fail 404)
        $this->delete(route('admin.cashbook.destroy', [$projectB, $cashflowA]))->assertNotFound();

        // Valid deletion under Project A
        $this->delete(route('admin.cashbook.destroy', [$projectA, $cashflowA]))
            ->assertRedirect(route('admin.cashbook.show', $projectA))
            ->assertSessionHas('success');

        $this->assertSoftDeleted('project_cashflows', ['id' => $cashflowA->id]);
    }

    public function test_cashbook_project_index_supports_keyword_searching(): void
    {
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin']);
        /** @var User $clientBudi */
        $clientBudi = User::factory()->create(['role' => 'owner', 'name' => 'Budi Santoso', 'email' => 'budi@example.test']);
        /** @var User $clientSiti */
        $clientSiti = User::factory()->create(['role' => 'owner', 'name' => 'Siti Nurhaliza', 'email' => 'siti@example.test']);

        $this->project($clientBudi, 'Pembangunan Villa Canggu', 'Jl. Pantai Berawa No. 12');
        $this->project($clientSiti, 'Renovasi Ruko Sudirman', 'Jl. Jenderal Sudirman No. 45');

        $this->actingAs($admin);

        // Search by project name
        $this->get(route('admin.cashbook.index', ['search' => 'Canggu']))
            ->assertOk()
            ->assertSee('Pembangunan Villa Canggu')
            ->assertDontSee('Renovasi Ruko Sudirman');

        // Search by location
        $this->get(route('admin.cashbook.index', ['search' => 'Sudirman']))
            ->assertOk()
            ->assertSee('Renovasi Ruko Sudirman')
            ->assertDontSee('Pembangunan Villa Canggu');

        // Search by client name
        $this->get(route('admin.cashbook.index', ['search' => 'Siti']))
            ->assertOk()
            ->assertSee('Renovasi Ruko Sudirman')
            ->assertDontSee('Pembangunan Villa Canggu');
    }

    public function test_staff_can_download_excel_cashbook_report(): void
    {
        /** @var User $accountant */
        $accountant = User::factory()->create(['role' => 'accounting']);
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Klien Laporan']);
        $project = $this->project($owner, 'Proyek Laporan Keuangan');

        $project->cashflows()->create([
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'code' => 'INV-01',
            'description' => 'Penerimaan Invoice 1',
            'unit' => 'termin',
            'quantity_millis' => 1000,
            'unit_price_cents' => 1000000000,
            'notes' => 'Catatan transaksi aman',
        ]);

        $project->cashflows()->create([
            'transaction_date' => '2026-11-09',
            'type' => 'pengeluaran',
            'code' => null,
            'description' => '=SUM(A1:A2)',
            'unit' => null,
            'quantity_millis' => 2500,
            'unit_price_cents' => 5000000,
            'notes' => null,
        ]);

        $response = $this->actingAs($accountant)->get(route('admin.cashbook.export', $project));

        $response->assertOk();
        $this->assertStringContainsString('application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', (string) $response->headers->get('content-type'));
        $this->assertStringContainsString('buku-kas-proyek-'.$project->id.'.xlsx', (string) $response->headers->get('content-disposition'));

        $filePath = $response->getFile()->getPathname();
        $this->assertFileExists($filePath);
        $this->assertGreaterThan(0, filesize($filePath));
        $zip = new \ZipArchive;
        $this->assertTrue($zip->open($filePath) === true);
        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();
        @unlink($filePath);

        $this->assertIsString($sheetXml);
        $this->assertStringContainsString('INDO BERKAH KONSTRUKSI', $sheetXml);
        $this->assertStringContainsString('=SUM(A1:A2)', $sheetXml);
        $this->assertStringContainsString('t="inlineStr"', $sheetXml);
    }

    public function test_invalid_or_overflowing_cashflow_inputs_are_rejected(): void
    {
        /** @var User $accountant */
        $accountant = User::factory()->create(['role' => 'accounting']);
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner);

        $this->actingAs($accountant);

        $this->from(route('admin.cashbook.show', $project))->post(route('admin.cashbook.store', $project), [
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'description' => 'Kuantitas nol',
            'quantity' => '0',
            'unit_price' => '1000',
        ])->assertRedirect(route('admin.cashbook.show', $project))->assertSessionHasErrors('quantity');

        $this->from(route('admin.cashbook.show', $project))->post(route('admin.cashbook.store', $project), [
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'description' => 'Desimal berlebih',
            'quantity' => '1.2345',
            'unit_price' => '1000.123',
        ])->assertSessionHasErrors(['quantity', 'unit_price']);

        $this->from(route('admin.cashbook.show', $project))->post(route('admin.cashbook.store', $project), [
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'description' => 'Melebihi batas total maksimal',
            'quantity' => '1000.000',
            'unit_price' => '9999999999.99',
        ])->assertSessionHasErrors('quantity');

        $this->assertDatabaseCount('project_cashflows', 0);
    }

    public function test_staff_can_save_and_reuse_cashflow_options_without_case_duplicates(): void
    {
        $accountant = User::factory()->create(['role' => 'accounting']);
        $project = $this->project(User::factory()->create(['role' => 'owner']));
        $url = route('admin.cashbook.options.store', $project);

        $this->actingAs($accountant)->postJson($url, [
            'option_type' => 'code', 'option_value' => ' INV-01 ',
        ])->assertOk()->assertJsonPath('value', 'INV-01');
        $this->postJson($url, [
            'option_type' => 'code', 'option_value' => 'inv-01',
        ])->assertOk()->assertJsonPath('value', 'INV-01');
        $this->postJson($url, [
            'option_type' => 'unit', 'option_value' => 'm²',
        ])->assertOk()->assertJsonPath('value', 'm²');

        $this->assertDatabaseCount('cashflow_options', 2);
        $this->get(route('admin.cashbook.show', $project))->assertOk()->assertSee('INV-01')->assertSee('m²');
    }

    public function test_cashflow_options_require_authorization_and_valid_values(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner);
        $url = route('admin.cashbook.options.store', $project);

        $this->actingAs($owner)->postJson($url, [
            'option_type' => 'code', 'option_value' => 'INV',
        ])->assertForbidden();

        $this->actingAs(User::factory()->create(['role' => 'accounting']))
            ->postJson($url, ['option_type' => 'unit', 'option_value' => str_repeat('x', 31)])
            ->assertUnprocessable()->assertJsonValidationErrors('option_value');
        $this->postJson($url, ['option_type' => 'unknown', 'option_value' => 'x'])
            ->assertUnprocessable()->assertJsonValidationErrors('option_type');
        $this->postJson($url, ['option_type' => 'code', 'option_value' => "abc\nxyz"])
            ->assertUnprocessable()->assertJsonValidationErrors('option_value');
        $this->assertDatabaseCount('cashflow_options', 0);
    }

    public function test_validation_error_renders_original_inputs_without_changing_them(): void
    {
        $accountant = User::factory()->create(['role' => 'accounting']);
        $project = $this->project(User::factory()->create(['role' => 'owner']));
        $url = route('admin.cashbook.show', $project);

        $this->actingAs($accountant)->from($url)->post(route('admin.cashbook.store', $project), [
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'description' => 'Input tidak valid',
            'quantity' => '1.2500',
            'unit_price' => '25000.50',
        ])->assertRedirect($url)->assertSessionHasErrors('quantity');

        $this->get($url)->assertOk()->assertSee('value="1.2500"', false)->assertSee('Rp 25.000,50');
        $this->assertDatabaseCount('project_cashflows', 0);
    }

    public function test_month_and_year_filter_transaction_dates_and_keep_running_balance(): void
    {
        $project = $this->project(User::factory()->create(['role' => 'owner']));
        foreach ([['2025-11-01', 'Lama', 'penerimaan', 10000], ['2026-11-02', 'Terpilih', 'pengeluaran', 3000], ['2026-12-01', 'Bulan lain', 'penerimaan', 5000]] as [$date, $description, $type, $price]) {
            $project->cashflows()->create([
                'transaction_date' => $date, 'description' => $description, 'type' => $type,
                'quantity_millis' => 1000, 'unit_price_cents' => $price,
            ]);
        }

        $response = $this->actingAs(User::factory()->create(['role' => 'accounting']))
            ->get(route('admin.cashbook.show', ['project' => $project, 'month' => 11, 'year' => 2026]));
        $response->assertOk()->assertSee('Terpilih')->assertDontSee('Bulan lain')->assertDontSee('Lama');
        $response->assertViewHas('expense', '30.00')->assertViewHas('balance', '-30.00');
        $response->assertViewHas('transactions', fn ($transactions) => $transactions->count() === 1 && $transactions->first()->running_balance === '70.00');
    }

    public function test_staff_can_store_and_update_cashflow_with_photo_attachment_or_notes_and_track_last_edited(): void
    {
        Storage::fake('public');

        /** @var User $accountant */
        $accountant = User::factory()->create(['role' => 'accounting', 'name' => 'Akuntan Utama']);
        /** @var User $admin */
        $admin = User::factory()->create(['role' => 'admin', 'name' => 'Admin Super']);
        /** @var User $owner */
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner, 'Proyek Renovasi Kantor');

        $fakePhoto = UploadedFile::fake()->image('nota-material.jpg', 640, 480);

        // 1. Tambah transaksi dengan foto dan catatan teks
        $this->actingAs($accountant)->post(route('admin.cashbook.store', $project), [
            'transaction_date' => '2026-11-09',
            'type' => 'pengeluaran',
            'description' => 'Beli Semen Tiga Roda',
            'quantity' => '10',
            'unit_price' => '65000',
            'notes' => 'Catatan nota toko bangunan',
            'attachment' => $fakePhoto,
        ])->assertRedirect(route('admin.cashbook.show', $project))->assertSessionHas('success');

        $cashflow = $project->cashflows()->firstOrFail();
        $this->assertSame($accountant->id, $cashflow->created_by);
        $this->assertNull($cashflow->last_edited_at);
        $this->assertCount(1, $cashflow->attachments);

        $attachment = $cashflow->attachments->first();
        $this->assertSame('nota-material.jpg', $attachment->original_name);
        Storage::disk('public')->assertExists($attachment->file_path);

        // Periksa tampilan buku kas: memuat catatan teks, tombol foto bukti, dan belum ada tanda last edited
        $showResponse = $this->get(route('admin.cashbook.show', $project));
        $showResponse->assertOk()
            ->assertSee('Catatan nota toko bangunan')
            ->assertSee('Foto bukti')
            ->assertSee($attachment->url)
            ->assertDontSee('Last edited:');

        // 2. Ubah transaksi: edit catatan, ganti foto, dan pastikan last_edited_at tercatat
        $newPhoto = UploadedFile::fake()->image('nota-revisi.png', 800, 600);
        $this->actingAs($admin)->put(route('admin.cashbook.update', [$project, $cashflow]), [
            'transaction_date' => '2026-11-09',
            'type' => 'pengeluaran',
            'description' => 'Beli Semen Tiga Roda (Revisi)',
            'quantity' => '12',
            'unit_price' => '65000',
            'notes' => 'Catatan sudah direvisi mandor',
            'attachment' => $newPhoto,
        ])->assertRedirect(route('admin.cashbook.show', $project))->assertSessionHas('success');

        $cashflow->refresh();
        $this->assertSame($admin->id, $cashflow->updated_by);
        $this->assertNotNull($cashflow->last_edited_at);
        $this->assertSame('Catatan sudah direvisi mandor', $cashflow->notes);

        // Old file deleted, new file saved
        Storage::disk('public')->assertMissing($attachment->file_path);
        $this->assertCount(1, $cashflow->fresh()->attachments);
        $newAttachment = $cashflow->fresh()->attachments->first();
        $this->assertSame('nota-revisi.png', $newAttachment->original_name);
        Storage::disk('public')->assertExists($newAttachment->file_path);

        // Periksa tampilan setelah diedit: sekarang menampilkan Last edited dan nama editor
        $editedResponse = $this->get(route('admin.cashbook.show', $project));
        $editedResponse->assertOk()
            ->assertSee('Catatan sudah direvisi mandor')
            ->assertSee('Last edited: '.$cashflow->last_edited_at->format('d/m/Y H:i'))
            ->assertSee('Admin Super')
            ->assertSee('Foto bukti');

        // 3. Ubah lagi dengan opsi remove_attachment (menghapus foto, menyisakan catatan saja)
        $this->actingAs($admin)->put(route('admin.cashbook.update', [$project, $cashflow]), [
            'transaction_date' => '2026-11-09',
            'type' => 'pengeluaran',
            'description' => 'Beli Semen Tiga Roda (Tanpa Foto)',
            'quantity' => '12',
            'unit_price' => '65000',
            'notes' => 'Hanya catatan tanpa foto',
            'remove_attachment' => '1',
        ])->assertRedirect(route('admin.cashbook.show', $project))->assertSessionHas('success');

        $this->assertCount(0, $cashflow->fresh()->attachments);
        Storage::disk('public')->assertMissing($newAttachment->file_path);
    }

    public function test_cashbook_index_displays_monthly_income_recap_matrix_for_all_projects(): void
    {
        /** @var User $accountant */
        $accountant = User::factory()->create(['role' => 'accounting']);
        /** @var User $owner1 */
        $owner1 = User::factory()->create(['role' => 'owner', 'name' => 'Klien Alpha']);
        /** @var User $owner2 */
        $owner2 = User::factory()->create(['role' => 'owner', 'name' => 'Klien Beta']);

        $projA = $this->project($owner1, 'Proyek Gedung Alpha');
        $projB = $this->project($owner2, 'Proyek Rumah Beta');

        // Proyek Alpha: Pendapatan di Juli (Rp 50.000.000) dan Agustus (Rp 25.000.000)
        $projA->cashflows()->create([
            'transaction_date' => '2026-07-10',
            'type' => 'penerimaan',
            'description' => 'Termin 1 Alpha',
            'quantity_millis' => 1000,
            'unit_price_cents' => 5000000000,
        ]);
        $projA->cashflows()->create([
            'transaction_date' => '2026-08-15',
            'type' => 'penerimaan',
            'description' => 'Termin 2 Alpha',
            'quantity_millis' => 1000,
            'unit_price_cents' => 2500000000,
        ]);

        // Proyek Beta: Pendapatan di Juli (Rp 30.000.000)
        $projB->cashflows()->create([
            'transaction_date' => '2026-07-20',
            'type' => 'penerimaan',
            'description' => 'Termin 1 Beta',
            'quantity_millis' => 1000,
            'unit_price_cents' => 3000000000,
        ]);

        $response = $this->actingAs($accountant)->get(route('admin.cashbook.index', ['year' => 2026]));
        $response->assertOk()
            ->assertSee('Rekapan Pendapatan Bulanan (Semua Proyek)')
            ->assertSee('Proyek Gedung Alpha')
            ->assertSee('Proyek Rumah Beta')
            ->assertSee('Klien Alpha')
            ->assertSee('Klien Beta')
            ->assertSee('Rp 50.000.000')
            ->assertSee('Rp 30.000.000')
            ->assertSee('Rp 25.000.000')
            ->assertSee('TOTAL PENDAPATAN SEMUA PROYEK:');
    }

    private function project(User $owner, string $name = 'Proyek Uji', string $location = 'Lokasi Uji'): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'nama_proyek' => $name,
            'kategori' => 'Pembangunan Rumah',
            'alamat_proyek' => $location,
            'progress_percentage' => 0,
            'status' => 'aktif',
        ]);
    }
}
