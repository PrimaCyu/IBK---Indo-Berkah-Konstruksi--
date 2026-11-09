<?php

namespace Tests\Feature;

use App\Models\Penawaran;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PenawaranApprovalTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_approval_creates_one_owner_and_active_project_without_financial_entries(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quote = $this->quote(['email' => 'BARU@example.test', 'foto' => 'penawaran/denah.jpg']);
        $financialTables = array_values(array_filter(DB::getSchemaBuilder()->getTableListing(), fn ($table) => (bool) preg_match('/transaction|cashbook|buku_kas|keuangan|ledger/', $table)));
        $before = array_map(fn ($table) => DB::table($table)->count(), $financialTables);

        $response = $this->actingAs($admin)->from('/admin/penawaran')->post('/admin/penawaran/'.$quote->id.'/approve');

        $response->assertRedirect('/admin/penawaran')->assertSessionHas('approved_success.account_created', true);
        $success = session('approved_success');
        $owner = User::where('email', 'baru@example.test')->sole();
        $project = Project::sole();
        $this->assertSame('owner', $owner->role);
        $this->assertSame('6281234567890', $owner->phone);
        $this->assertGreaterThanOrEqual(20, strlen($success['plain_password']));
        $this->assertTrue(Hash::check($success['plain_password'], $owner->password));
        $this->assertNotSame('IBK'.$quote->created_at->format('dmy'), $success['plain_password']);
        $this->assertStringNotContainsString($success['plain_password'], urldecode($success['wa_link']));
        $this->assertStringNotContainsString('Password:', urldecode($success['wa_link']));
        $this->assertStringStartsWith('https://wa.me/6281234567890?', $success['wa_link']);
        $this->assertSame($owner->id, $project->user_id);
        $this->assertSame('Proyek '.$quote->nama, $project->nama_proyek);
        $this->assertSame($quote->alamat, $project->alamat_proyek);
        $this->assertSame($quote->deskripsi, $project->deskripsi);
        $this->assertSame($quote->foto, $project->main_image);
        $this->assertSame('aktif', $project->status);
        $this->assertEquals(0, $project->progress_percentage);
        $this->assertFalse($project->portfolio_approved);
        $this->assertDatabaseHas('penawarans', ['id' => $quote->id, 'status' => 'approved', 'project_id' => $project->id]);
        $this->assertSame($before, array_map(fn ($table) => DB::table($table)->count(), $financialTables));

        $this->get('/admin/penawaran')->assertOk()->assertSee($success['plain_password'])->assertSee('hanya ditampilkan sekali');
        $this->get('/admin/penawaran')->assertOk()->assertDontSee($success['plain_password'])->assertDontSee('IBK'.$quote->created_at->format('dmy'));
    }

    public function test_missing_email_generates_unique_fallback_and_different_random_passwords(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'owner', 'email' => '6281234567890@klien.indoberkah.com', 'phone' => null]);
        $first = $this->quote(['email' => null]);
        $second = $this->quote(['email' => null, 'telepon' => '+62 813-4567-8901']);
        $this->actingAs($admin);

        $this->post('/admin/penawaran/'.$first->id.'/approve')->assertSessionHas('approved_success.account_created', true);
        $firstPassword = session('approved_success.plain_password');
        $this->assertSame('6281234567890_1@klien.indoberkah.com', $first->fresh()->project->user->email);

        $this->post('/admin/penawaran/'.$second->id.'/approve')->assertSessionHas('approved_success.account_created', true);
        $this->assertNotSame($firstPassword, session('approved_success.plain_password'));
        $this->assertSame('6281345678901', $second->fresh()->project->user->phone);
    }

    public function test_repeated_approval_does_not_change_the_account_project_or_expose_password_again(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quote = $this->quote();
        $this->actingAs($admin)->post('/admin/penawaran/'.$quote->id.'/approve');
        $quote->refresh();
        $owner = $quote->project->user;
        $userAttributes = $owner->getAttributes();
        $projectAttributes = $quote->project->getAttributes();

        $this->post('/admin/penawaran/'.$quote->id.'/approve')
            ->assertSessionHas('info')->assertSessionMissing('approved_success');

        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('projects', 1);
        $this->assertSame($userAttributes, $owner->fresh()->getAttributes());
        $this->assertSame($projectAttributes, $quote->project->fresh()->getAttributes());
        $this->assertSame($quote->project_id, $quote->fresh()->project_id);
    }

    public function test_linked_project_is_never_recreated_even_when_status_or_link_is_inconsistent(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quote = $this->quote();
        $this->actingAs($admin)->post('/admin/penawaran/'.$quote->id.'/approve');
        $quote->refresh();
        $projectId = $quote->project_id;
        $hash = $quote->project->user->password;
        $quote->update(['status' => 'pending']);

        $this->post('/admin/penawaran/'.$quote->id.'/approve')->assertSessionHas('error');
        $this->assertDatabaseCount('projects', 1);
        $this->assertSame($projectId, $quote->fresh()->project_id);
        $this->assertSame($hash, $quote->project->user->fresh()->password);

        $quote->update(['status' => 'approved']);
        $quote->project->delete();
        $this->assertNull($quote->fresh()->project_id);
        $this->post('/admin/penawaran/'.$quote->id.'/approve')->assertSessionHas('error');
        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('users', 2);
    }

    public function test_only_pending_unlinked_quotes_can_be_rejected_or_approved(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $quote = $this->quote();
        $this->actingAs($admin)->post('/admin/penawaran/'.$quote->id.'/reject')->assertSessionHas('success');
        $this->assertSame('rejected', $quote->fresh()->status);
        $this->post('/admin/penawaran/'.$quote->id.'/reject')->assertSessionHas('error');
        $this->post('/admin/penawaran/'.$quote->id.'/approve')->assertSessionHas('error');
        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('users', 1);

        $approved = $this->quote(['email' => 'approved@example.test']);
        $this->post('/admin/penawaran/'.$approved->id.'/approve');
        $this->post('/admin/penawaran/'.$approved->id.'/reject')->assertSessionHas('error');
        $this->assertSame('approved', $approved->fresh()->status);
        $approved->refresh()->update(['status' => 'pending']);
        $this->post('/admin/penawaran/'.$approved->id.'/reject')->assertSessionHas('error');
        $this->assertSame('pending', $approved->fresh()->status);
    }

    public function test_confirmed_existing_owner_is_reused_without_changing_credentials_or_contact(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner', 'email' => 'Existing@example.test', 'phone' => '+62 (812) 3456-7890']);
        $attributes = $owner->fresh()->getAttributes();
        $quote = $this->quote(['nama' => 'Nama dari formulir', 'email' => 'EXISTING@example.test', 'telepon' => '0812 3456 7890']);

        $this->actingAs($admin)->post('/admin/penawaran/'.$quote->id.'/approve', ['confirm_existing_account' => '1'])
            ->assertSessionHas('approved_success.account_created', false);

        $this->assertNull(session('approved_success.plain_password'));
        $this->assertSame($attributes, $owner->fresh()->getAttributes());
        $this->assertSame($owner->id, $quote->fresh()->project->user_id);
        $this->assertStringStartsWith('https://wa.me/6281234567890?', session('approved_success.wa_link'));
        $this->assertDatabaseCount('users', 2);
        $this->get('/admin/penawaran')->assertOk()->assertSee('Password dan kontak akun tidak diubah.');
    }

    public function test_existing_owner_requires_explicit_identity_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner', 'email' => 'existing@example.test', 'phone' => '6281234567890']);
        $quote = $this->quote(['email' => 'existing@example.test']);
        $attributes = $owner->fresh()->getAttributes();

        $this->actingAs($admin)->from('/admin/penawaran')->post('/admin/penawaran/'.$quote->id.'/approve')
            ->assertRedirect('/admin/penawaran')->assertSessionHasErrors('confirm_existing_account');
        $this->assertNull(session()->getOldInput('confirm_existing_account'));
        $this->assertSame($quote->id, session()->getOldInput('approval_quote_id'));
        $this->assertDatabaseCount('projects', 0);
        $this->assertSame($attributes, $owner->fresh()->getAttributes());
        session()->reflash();
        session()->save();
        $this->get('/admin/penawaran')->assertOk()->assertSee('Verifikasi identitas pemohon');

        $this->post('/admin/penawaran/'.$quote->id.'/approve', ['confirm_existing_account' => '1'])
            ->assertSessionHasNoErrors()->assertSessionHas('approved_success.account_created', false);
        $this->assertSame([], session()->getOldInput());
    }

    public function test_owner_without_canonical_phone_does_not_receive_lead_phone_or_whatsapp_link(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $owner = User::factory()->create(['role' => 'owner', 'email' => 'existing@example.test', 'phone' => null]);
        $quote = $this->quote(['email' => 'existing@example.test']);

        $this->actingAs($admin)->post('/admin/penawaran/'.$quote->id.'/approve', ['confirm_existing_account' => '1'])
            ->assertSessionHas('approved_success.account_created', false);

        $this->assertNull($owner->fresh()->phone);
        $this->assertNull(session('approved_success.wa_link'));
        $this->get('/admin/penawaran')->assertOk()->assertDontSee('https://wa.me/6281234567890', false);
    }

    public function test_privileged_accounts_cannot_be_reused_even_with_confirmation(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'email' => 'admin@example.test', 'phone' => '+62 812 3456 7890']);
        $accounting = User::factory()->create(['role' => 'accounting', 'email' => 'finance@example.test', 'phone' => '081345678901']);
        $this->actingAs($admin);
        foreach ([
            ['email' => 'ADMIN@example.test', 'telepon' => '081234567890'],
            ['email' => null, 'telepon' => '+62 813-4567-8901'],
        ] as $contacts) {
            $quote = $this->quote($contacts);
            $this->post('/admin/penawaran/'.$quote->id.'/approve', ['confirm_existing_account' => '1'])->assertSessionHas('error');
            $this->assertSame('pending', $quote->fresh()->status);
        }
        $this->assertDatabaseCount('users', 2);
        $this->assertDatabaseCount('projects', 0);
        $this->assertSame($admin->password, $admin->fresh()->password);
        $this->assertSame($accounting->password, $accounting->fresh()->password);
    }

    public function test_mismatched_or_ambiguous_contacts_are_rejected(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        User::factory()->create(['role' => 'owner', 'email' => 'first@example.test', 'phone' => '+62 812 3456 7890']);
        User::factory()->create(['role' => 'owner', 'email' => 'second@example.test', 'phone' => '081345678901']);
        $this->actingAs($admin);
        foreach ([
            ['email' => 'first@example.test', 'telepon' => '081345678901'],
            ['email' => 'new@example.test', 'telepon' => '081234567890'],
            ['email' => 'first@example.test', 'telepon' => '081456789012'],
        ] as $contacts) {
            $quote = $this->quote($contacts);
            $this->post('/admin/penawaran/'.$quote->id.'/approve', ['confirm_existing_account' => '1'])->assertSessionHas('error');
            $this->assertSame('pending', $quote->fresh()->status);
        }

        User::factory()->create(['role' => 'owner', 'email' => 'duplicate@example.test', 'phone' => '6281234567890']);
        $quote = $this->quote(['email' => null]);
        $this->post('/admin/penawaran/'.$quote->id.'/approve', ['confirm_existing_account' => '1'])->assertSessionHas('error');
        $this->assertDatabaseCount('projects', 0);
        $this->assertDatabaseCount('users', 4);
    }

    public function test_invalid_contact_data_never_creates_accounts(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $this->actingAs($admin);
        foreach (['123', '0000000000', '628000', 'call081234567890', '', '+99 812 3456 7890'] as $phone) {
            $quote = $this->quote(['telepon' => $phone]);
            $this->post('/admin/penawaran/'.$quote->id.'/approve')->assertSessionHas('error');
            $this->assertSame('pending', $quote->fresh()->status);
        }
        $quote = $this->quote(['email' => 'not-an-email']);
        $this->post('/admin/penawaran/'.$quote->id.'/approve')->assertSessionHas('error');
        $this->assertDatabaseCount('users', 1);
        $this->assertDatabaseCount('projects', 0);
    }

    public function test_public_registration_remains_closed(): void
    {
        $this->get('/register')->assertRedirect('/penawaran');
        $this->post('/register', ['name' => 'Public', 'email' => 'public@example.test', 'password' => 'password', 'password_confirmation' => 'password', 'role' => 'admin'])
            ->assertRedirect('/penawaran');
        $this->assertDatabaseCount('users', 0);
    }

    public function test_clients_can_login_with_email_normalized_phone_or_unique_name(): void
    {
        $owner = User::factory()->create(['role' => 'owner', 'name' => 'Nama Klien Unik', 'email' => 'client@example.test', 'phone' => '+62 (812) 3456-7890']);
        foreach (['CLIENT@example.test', '081234567890', '62 812-3456-7890', ' Nama Klien Unik '] as $identifier) {
            $this->post('/login', ['login' => $identifier, 'password' => 'password'])->assertRedirect('/dashbord');
            $this->assertAuthenticatedAs($owner);
            $this->post('/logout');
        }
        $this->post('/login', ['email' => 'client@example.test', 'password' => 'password'])->assertRedirect('/dashbord');
        $this->assertAuthenticatedAs($owner);
    }

    public function test_ambiguous_names_or_phone_numbers_never_login_to_the_first_account(): void
    {
        User::factory()->create(['role' => 'owner', 'name' => 'Nama Sama', 'email' => 'one@example.test', 'phone' => '081234567890']);
        User::factory()->create(['role' => 'owner', 'name' => 'Nama Sama', 'email' => 'two@example.test', 'phone' => '+62 812 3456 7890', 'password' => Hash::make('different-password')]);
        foreach (['Nama Sama', '6281234567890'] as $identifier) {
            $this->from('/login')->post('/login', ['login' => $identifier, 'password' => 'password'])
                ->assertRedirect('/login')->assertSessionHasErrors('login');
            $this->assertGuest();
        }
        session()->reflash();
        session()->save();
        $this->get('/login')->assertOk()->assertSee('Gunakan email akun Anda.');
        $this->post('/login', ['login' => 'one@example.test', 'password' => 'password'])->assertRedirect('/dashbord');
    }

    public function test_digits_in_email_or_name_are_not_treated_as_phone_numbers(): void
    {
        User::factory()->create(['role' => 'owner', 'email' => 'real@example.test', 'phone' => '081234567890']);
        foreach (['lead081234567890@example.test', 'Klien 081234567890'] as $identifier) {
            $this->post('/login', ['login' => $identifier, 'password' => 'password'])->assertSessionHas('error');
            $this->assertGuest();
        }
    }

    public function test_invalid_identifiers_return_validation_errors_and_login_page_stays_renderable(): void
    {
        foreach ([['unexpected'], 123, null, str_repeat('a', 256)] as $identifier) {
            $this->from('/login')->post('/login', ['login' => $identifier, 'password' => 'password'])
                ->assertRedirect('/login')->assertSessionHasErrors('login');
            $this->assertGuest();
            $this->get('/login')->assertOk()->assertDontSee('IBK + tgl_penawaran');
        }
    }

    public function test_login_redirects_are_scoped_to_role_and_same_origin(): void
    {
        foreach ([
            ['role' => 'admin', 'intended' => '/dashbord', 'expected' => '/admin'],
            ['role' => 'accounting', 'intended' => '/admin/users', 'expected' => '/admin/keuangan-proyek'],
            ['role' => 'owner', 'intended' => '/admin/keuangan-proyek', 'expected' => '/dashbord'],
            ['role' => 'owner', 'intended' => '/dashbord/project/123', 'expected' => '/dashbord/project/123'],
            ['role' => 'accounting', 'intended' => '/admin/keuangan-proyek?project_id=1', 'expected' => '/admin/keuangan-proyek?project_id=1'],
            ['role' => 'admin', 'intended' => '/admin/projects', 'expected' => '/admin/projects'],
            ['role' => 'owner', 'intended' => 'https://attacker.example/dashbord', 'expected' => '/dashbord'],
            ['role' => 'accounting', 'intended' => '/admin/keuangan-proyek/../users', 'expected' => '/admin/keuangan-proyek'],
            ['role' => 'accounting', 'intended' => '/admin/keuangan-proyek/%2e%2e/users', 'expected' => '/admin/keuangan-proyek'],
        ] as $case) {
            \Illuminate\Support\Facades\RateLimiter::clear(sha1('|127.0.0.1'));
            $user = User::factory()->create(['role' => $case['role']]);
            $this->withSession(['url.intended' => url($case['intended'])])->post('/login', ['login' => $user->email, 'password' => 'password'])
                ->assertRedirect($case['expected']);
            $this->assertAuthenticatedAs($user);
            $this->assertNull(session('url.intended'));
            $this->post('/logout');
        }
    }

    public function test_public_header_uses_role_destinations_on_desktop_and_mobile(): void
    {
        foreach (['admin' => '/admin', 'accounting' => '/admin/keuangan-proyek', 'owner' => '/dashbord'] as $role => $destination) {
            $user = User::factory()->create(['role' => $role]);
            $response = $this->actingAs($user)->get('/login')->assertOk();
            $href = $role === 'accounting' ? route('admin.cashbook.index') : $destination;
            $this->assertSame(2, substr_count($response->getContent(), 'href="'.$href.'"'));
            $response->assertSee('Minta Penawaran')->assertDontSee('href="/register"', false);
        }
    }

    private function quote(array $attributes = []): Penawaran
    {
        return Penawaran::create(array_replace([
            'nama' => 'Klien Baru',
            'telepon' => '081234567890',
            'email' => 'baru@example.test',
            'alamat' => 'Jl. Proyek No. 1',
            'deskripsi' => 'Pembangunan rumah dua lantai.',
            'budget' => 'Rp 500.000.000',
            'status' => 'pending',
        ], $attributes));
    }
}
