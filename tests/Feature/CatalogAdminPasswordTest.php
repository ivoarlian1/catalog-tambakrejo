<?php

namespace Tests\Feature;

use App\Models\Catalog;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CatalogAdminPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_catalog_admin_can_open_their_password_page(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create();

        $this->actingAs($admin)
            ->get(route('catalog-admin.password.edit'))
            ->assertOk()
            ->assertSee('Ubah Password')
            ->assertSee('current_password', false)
            ->assertSee('password_confirmation', false);
    }

    public function test_catalog_admin_can_change_password_with_the_current_password(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create([
            'password' => 'OldPassword123',
        ]);

        $this->actingAs($admin)
            ->put(route('catalog-admin.password.update'), [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword456',
                'password_confirmation' => 'NewPassword456',
            ])
            ->assertRedirect(route('catalog-admin.password.edit'))
            ->assertSessionHas('success', 'Password akun berhasil diperbarui.');

        $this->assertTrue(Hash::check('NewPassword456', $admin->fresh()->password));
        $this->assertFalse(Hash::check('OldPassword123', $admin->fresh()->password));
    }

    public function test_catalog_admin_cannot_change_password_with_an_incorrect_current_password(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create([
            'password' => 'OldPassword123',
        ]);
        $originalPassword = $admin->password;

        $this->actingAs($admin)
            ->from(route('catalog-admin.password.edit'))
            ->put(route('catalog-admin.password.update'), [
                'current_password' => 'WrongPassword123',
                'password' => 'NewPassword456',
                'password_confirmation' => 'NewPassword456',
            ])
            ->assertRedirect(route('catalog-admin.password.edit'))
            ->assertSessionHasErrors(['current_password' => 'Password saat ini tidak sesuai.']);

        $this->assertSame($originalPassword, $admin->fresh()->password);
    }

    public function test_catalog_admin_password_must_be_confirmed_and_meet_the_password_policy(): void
    {
        $catalog = Catalog::factory()->create();
        $admin = User::factory()->catalogAdmin($catalog)->create([
            'password' => 'OldPassword123',
        ]);

        $this->actingAs($admin)
            ->from(route('catalog-admin.password.edit'))
            ->put(route('catalog-admin.password.update'), [
                'current_password' => 'OldPassword123',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('catalog-admin.password.edit'))
            ->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('OldPassword123', $admin->fresh()->password));
    }

    public function test_only_catalog_admins_can_open_the_password_page(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get(route('catalog-admin.password.edit'))
            ->assertForbidden();
    }
}
