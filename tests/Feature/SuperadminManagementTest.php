<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SuperadminManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_create_another_superadmin(): void
    {
        $creator = User::factory()->superadmin()->create();

        $this->actingAs($creator)
            ->post(route('superadmin.superadmins.store'), [
                'name' => 'Superadmin Baru',
                'email' => 'baru@example.test',
                'password' => 'StrongPass123',
                'password_confirmation' => 'StrongPass123',
            ])
            ->assertRedirect(route('superadmin.superadmins.index'))
            ->assertSessionHas('success', 'Akun Superadmin berhasil dibuat.');

        $newSuperadmin = User::query()->where('email', 'baru@example.test')->firstOrFail();

        $this->assertSame('superadmin', $newSuperadmin->role);
        $this->assertNull($newSuperadmin->catalog_id);
        $this->assertTrue($newSuperadmin->is_active);
        $this->assertTrue(password_verify('StrongPass123', $newSuperadmin->password));
    }

    public function test_catalog_admin_cannot_view_or_create_superadmins(): void
    {
        $catalogAdmin = User::factory()->catalogAdmin()->create();

        $this->actingAs($catalogAdmin)
            ->get(route('superadmin.superadmins.index'))
            ->assertForbidden();

        $this->actingAs($catalogAdmin)
            ->post(route('superadmin.superadmins.store'), [
                'name' => 'Tidak Diizinkan',
                'email' => 'forbidden@example.test',
                'password' => 'StrongPass123',
                'password_confirmation' => 'StrongPass123',
            ])
            ->assertForbidden();

        $this->assertDatabaseMissing('users', ['email' => 'forbidden@example.test']);
    }

    public function test_superadmin_creation_validates_unique_email_and_password_confirmation(): void
    {
        User::factory()->superadmin()->create(['email' => 'existing@example.test']);
        $creator = User::factory()->superadmin()->create();

        $this->actingAs($creator)
            ->from(route('superadmin.superadmins.create'))
            ->post(route('superadmin.superadmins.store'), [
                'name' => 'Akun Invalid',
                'email' => 'existing@example.test',
                'password' => 'StrongPass123',
                'password_confirmation' => 'DifferentPass123',
            ])
            ->assertRedirect(route('superadmin.superadmins.create'))
            ->assertSessionHasErrors(['email', 'password']);

        $this->assertDatabaseCount('users', 2);
    }
}
