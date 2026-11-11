<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountingAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_only_staff_can_open_finance_and_accounting_navigation_is_restricted(): void
    {
        $owner = User::factory()->create(['role' => 'owner']);
        $accountant = User::factory()->create(['role' => 'accounting']);
        $admin = User::factory()->create(['role' => 'admin']);
        $project = $this->project($owner);

        $this->get(route('admin.cashbook.index'))->assertRedirect(route('login'));
        $this->get(route('admin.cashbook.general'))->assertRedirect(route('login'));
        $this->actingAs($owner)->get(route('admin.cashbook.index'))->assertForbidden();
        $this->get(route('admin.cashbook.general'))->assertForbidden();
        $this->get(route('admin.cashbook.show', $project))->assertForbidden();
        $this->actingAs($admin)->get(route('admin.cashbook.index'))
            ->assertOk()->assertSee('Keuangan Proyek')->assertSee('Keuangan Umum')->assertSee('/admin/penawaran', false);
        $this->get(route('admin.cashbook.general'))
            ->assertOk()->assertSee('Overview Keuangan Umum')->assertSee('Manajemen Keuangan');
        $this->get(route('admin.projects.index'))->assertOk()->assertSee(route('admin.cashbook.show', $project), false);

        $this->actingAs($accountant)->get(route('admin.cashbook.index'))
            ->assertOk()->assertSee('Accounting')->assertSee('Keuangan Proyek')->assertSee('Keuangan Umum')
            ->assertDontSee('/admin/penawaran', false)
            ->assertDontSee('/admin/settings', false)
            ->assertDontSee('/admin/users', false);
        $this->get(route('admin.cashbook.general'))
            ->assertOk()->assertSee('Overview Keuangan Umum')->assertSee('Manajemen Keuangan');
        $this->get(route('admin.cashbook.show', $project))->assertOk();
    }

    public function test_accounting_cannot_use_other_admin_or_client_routes(): void
    {
        $accountant = User::factory()->create(['role' => 'accounting']);
        $this->actingAs($accountant);

        foreach (['/admin', '/admin/penawaran', '/admin/projects', '/admin/users', '/admin/settings', '/dashbord'] as $url) {
            $this->get($url)->assertForbidden();
        }

        foreach (['/admin/projects', '/admin/users', '/projects', '/user-files', '/user-feedback'] as $url) {
            $this->post($url, [])->assertForbidden();
        }
    }

    public function test_admin_can_create_accounting_account_but_accounting_cannot_create_users(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $attributes = [
            'name' => 'Staf Keuangan',
            'email' => 'keuangan@example.test',
            'role' => 'accounting',
            'password' => 'SandiKeuanganCukupPanjang',
        ];

        $this->actingAs($admin)->post(route('admin.users.store'), $attributes)->assertSessionHasNoErrors()->assertRedirect();
        $this->assertDatabaseHas('users', ['email' => $attributes['email'], 'role' => 'accounting']);
        $this->actingAs(User::where('email', $attributes['email'])->firstOrFail())
            ->post(route('admin.users.store'), array_replace($attributes, ['email' => 'lain@example.test']))->assertForbidden();
        $this->assertDatabaseMissing('users', ['email' => 'lain@example.test']);
    }

    public function test_accounting_cannot_be_selected_as_a_project_owner(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $accountant = User::factory()->create(['role' => 'accounting']);
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner);
        $attributes = [
            'user_id' => $accountant->id,
            'nama_proyek' => 'Proyek tidak valid',
            'kategori' => 'Pembangunan Rumah',
            'progress_percentage' => 0,
            'status' => 'aktif',
        ];

        $this->actingAs($admin)->post(route('admin.projects.store'), $attributes)->assertSessionHasErrors('user_id');
        $this->put(route('admin.projects.update', $project), $attributes)->assertSessionHasErrors('user_id');
        $this->assertSame($owner->id, $project->fresh()->user_id);
        $this->assertDatabaseCount('projects', 1);
    }

    public function test_bookkeeping_history_prevents_deletion_of_project_and_client_even_after_a_transaction_is_removed(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner']);
        $project = $this->project($owner);
        $cashflow = $project->cashflows()->create([
            'transaction_date' => '2026-11-08',
            'type' => 'penerimaan',
            'code' => 'DP01',
            'description' => 'Pembayaran DP',
            'unit' => 'termin',
            'quantity_millis' => 1000,
            'unit_price_cents' => 500000,
        ]);
        $cashflow->delete();

        $this->actingAs($admin)->delete(route('admin.projects.destroy', $project))->assertSessionHas('error')->assertRedirect();
        $this->delete(route('admin.users.destroy', $owner))->assertSessionHas('error')->assertRedirect();
        $this->assertDatabaseHas('projects', ['id' => $project->id]);
        $this->assertDatabaseHas('users', ['id' => $owner->id]);
        $this->assertDatabaseHas('project_cashflows', ['id' => $cashflow->id]);
    }

    private function project(User $owner): Project
    {
        return Project::create([
            'user_id' => $owner->id,
            'nama_proyek' => 'Pembangunan Rumah Uji',
            'kategori' => 'Pembangunan Rumah',
            'alamat_proyek' => 'Lokasi pengujian',
            'progress_percentage' => 0,
            'status' => 'aktif',
        ]);
    }
}
