<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_active_superadmin_can_login_and_logout(): void
    {
        $admin = User::factory()->superadmin()->create([
            'email' => 'superadmin@example.test',
            'password' => 'StrongPass123',
        ]);

        $this->post(route('superadmin.login.post'), [
            'email' => $admin->email,
            'password' => 'StrongPass123',
        ])->assertRedirect(route('superadmin.dashboard'));

        $this->assertAuthenticatedAs($admin);

        $this->post(route('superadmin.logout'))
            ->assertRedirect(route('superadmin.login'));

        $this->assertGuest();
    }

    public function test_inactive_catalog_admin_cannot_login(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->inactive()->create([
            'password' => 'StrongPass123',
        ]);

        $this->post(route('catalog-admin.login.post'), [
            'email' => $admin->email,
            'password' => 'StrongPass123',
        ])->assertInvalid([
            'email' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
        ]);

        $this->assertGuest();
    }

    public function test_catalog_admin_cannot_login_to_superadmin_portal(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create([
            'password' => 'StrongPass123',
        ]);

        $this->post(route('superadmin.login.post'), [
            'email' => $admin->email,
            'password' => 'StrongPass123',
        ])->assertInvalid(['email' => 'Email atau password yang Anda masukkan salah.']);

        $this->assertGuest();
    }

    public function test_catalog_admin_login_entry_is_explicit_and_logout_requires_login_again(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create([
            'password' => 'StrongPass123',
        ]);

        $this->get(route('catalog-admin.login'))->assertOk();

        $this->post(route('catalog-admin.login.post'), [
            'email' => $admin->email,
            'password' => 'StrongPass123',
        ])->assertRedirect(route('catalog-admin.dashboard'));

        $this->get(route('catalog-admin.login'))
            ->assertRedirect(route('catalog-admin.dashboard'));

        $this->post(route('catalog-admin.logout'))
            ->assertRedirect(route('catalog-admin.login'));

        $this->get(route('catalog-admin.dashboard'))
            ->assertRedirect(route('catalog-admin.login'));

        $this->get(route('catalog-admin.login'))->assertOk();
    }

    public function test_superadmin_session_is_not_changed_by_catalog_admin_login_entry(): void
    {
        $admin = User::factory()->superadmin()->create();

        $this->actingAs($admin)
            ->get(route('catalog-admin.login'))
            ->assertRedirect(route('superadmin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_login_validation_and_generic_credentials_errors_stay_on_the_form(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create([
            'email' => 'catalog@example.test',
            'password' => 'StrongPass123',
        ]);

        $this->from(route('catalog-admin.login'))
            ->post(route('catalog-admin.login.post'), [])
            ->assertRedirect(route('catalog-admin.login'))
            ->assertSessionHasErrors([
                'email' => 'Email atau user ID wajib diisi.',
                'password' => 'Password wajib diisi.',
            ]);

        $this->from(route('catalog-admin.login'))
            ->post(route('catalog-admin.login.post'), [
                'email' => 'not-an-email',
                'password' => 'wrong',
            ])
            ->assertRedirect(route('catalog-admin.login'))
            ->assertSessionHasErrors(['email' => 'Format email tidak valid.']);

        $this->from(route('catalog-admin.login'))
            ->post(route('catalog-admin.login.post'), [
                'email' => $admin->email,
                'password' => 'wrong',
            ])
            ->assertRedirect(route('catalog-admin.login'))
            ->assertSessionHasErrors(['email' => 'Email atau password yang Anda masukkan salah.']);

        $this->from(route('catalog-admin.login'))
            ->post(route('catalog-admin.login.post'), [
                'email' => 'missing@example.test',
                'password' => 'wrong',
            ])
            ->assertRedirect(route('catalog-admin.login'))
            ->assertSessionHasErrors(['email' => 'Email atau password yang Anda masukkan salah.']);
    }

    public function test_both_login_forms_include_password_toggle_without_submit_behavior(): void
    {
        $this->get(route('catalog-admin.login'))
            ->assertOk()
            ->assertSee('type="password"', false)
            ->assertSee('type="button"', false)
            ->assertSee('aria-label="Tampilkan password"', false);

        $this->get(route('superadmin.login'))
            ->assertOk()
            ->assertSee('type="password"', false)
            ->assertSee('type="button"', false)
            ->assertSee('aria-label="Tampilkan password"', false);
    }
}
