<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\ProjectCashflow;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        ]);

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
