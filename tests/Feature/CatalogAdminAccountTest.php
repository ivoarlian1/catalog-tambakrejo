<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogAdminAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_a_username_only_catalog_admin_that_can_log_in_with_username(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();

        $this->actingAs($superadmin)
            ->post(route('superadmin.admins.store'), [
                'name' => 'Admin Username',
                'username' => 'admin_katalog',
                'password' => 'StrongPass123',
                'password_confirmation' => 'StrongPass123',
                'catalog_id' => $catalog->id,
            ])
            ->assertRedirect(route('superadmin.admins.index'))
            ->assertSessionHas('success', 'Akun Catalog Admin berhasil dibuat.');

        $admin = User::query()->where('username', 'admin_katalog')->firstOrFail();
        $this->assertNull($admin->email);
        $this->assertSame($catalog->id, $admin->catalog_id);
        $this->assertSame('catalog_admin', $admin->role);

        auth()->logout();

        $this->post(route('catalog-admin.login.post'), [
            'email' => 'admin_katalog',
            'password' => 'StrongPass123',
        ])->assertRedirect(route('catalog-admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
    }

    public function test_superadmin_can_create_an_email_only_catalog_admin(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();

        $this->actingAs($superadmin)
            ->post(route('superadmin.admins.store'), [
                'name' => 'Admin Email',
                'email' => 'admin@example.test',
                'password' => 'StrongPass123',
                'password_confirmation' => 'StrongPass123',
                'catalog_id' => $catalog->id,
            ])
            ->assertRedirect(route('superadmin.admins.index'));

        $admin = User::query()->where('email', 'admin@example.test')->firstOrFail();
        $this->assertNull($admin->username);
    }

    public function test_catalog_admin_account_requires_at_least_one_login_identifier(): void
    {
        $superadmin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();

        $this->actingAs($superadmin)
            ->from(route('superadmin.admins.create'))
            ->post(route('superadmin.admins.store'), [
                'name' => 'Admin Tanpa Identitas',
                'password' => 'StrongPass123',
                'password_confirmation' => 'StrongPass123',
                'catalog_id' => $catalog->id,
            ])
            ->assertRedirect(route('superadmin.admins.create'))
            ->assertSessionHasErrors([
                'email' => 'Email atau username wajib diisi.',
                'username' => 'Email atau username wajib diisi.',
            ]);

        $this->assertDatabaseMissing('users', ['name' => 'Admin Tanpa Identitas']);
    }

    public function test_catalog_admin_username_must_be_unique(): void
    {
        User::factory()->catalogAdmin(Catalog::factory()->create())->create([
            'username' => 'admin_katalog',
        ]);
        $superadmin = User::factory()->superadmin()->create();
        $catalog = Catalog::factory()->create();

        $this->actingAs($superadmin)
            ->from(route('superadmin.admins.create'))
            ->post(route('superadmin.admins.store'), [
                'name' => 'Admin Duplikat',
                'username' => 'admin_katalog',
                'password' => 'StrongPass123',
                'password_confirmation' => 'StrongPass123',
                'catalog_id' => $catalog->id,
            ])
            ->assertRedirect(route('superadmin.admins.create'))
            ->assertSessionHasErrors([
                'username' => 'Username sudah digunakan oleh pengguna lain.',
            ]);

        $this->assertDatabaseMissing('users', ['name' => 'Admin Duplikat']);
    }
}
