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

    public function test_superadmin_can_update_another_superadmin_and_change_password(): void
    {
        $creator = User::factory()->superadmin()->create();
        $target = User::factory()->superadmin()->create([
            'name' => 'Nama Lama',
            'email' => 'old@example.test',
            'password' => 'OldPass123',
        ]);

        $this->actingAs($creator)
            ->put(route('superadmin.superadmins.update', $target), [
                'name' => 'Nama Baru',
                'email' => 'new@example.test',
                'password' => 'NewPass123',
                'password_confirmation' => 'NewPass123',
                'is_active' => '1',
            ])
            ->assertRedirect(route('superadmin.superadmins.index'))
            ->assertSessionHas('success', 'Akun Superadmin berhasil diperbarui.');

        $target->refresh();
        $this->assertSame('Nama Baru', $target->name);
        $this->assertSame('new@example.test', $target->email);
        $this->assertTrue(password_verify('NewPass123', $target->password));
        $this->assertTrue($target->is_active);
    }

    public function test_superadmin_can_toggle_another_superadmin_active_status(): void
    {
        $creator = User::factory()->superadmin()->create();
        $target = User::factory()->superadmin()->create();

        $this->actingAs($creator)
            ->post(route('superadmin.superadmins.toggle-active', $target))
            ->assertRedirect()
            ->assertSessionHas('success', "Akun {$target->name} berhasil dinonaktifkan.");

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_active' => false]);

        $this->post(route('superadmin.superadmins.toggle-active', $target))
            ->assertRedirect()
            ->assertSessionHas('success', "Akun {$target->name} berhasil diaktifkan.");

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_active' => true]);
    }

    public function test_superadmin_can_delete_another_superadmin(): void
    {
        $creator = User::factory()->superadmin()->create();
        $target = User::factory()->superadmin()->create();

        $this->actingAs($creator)
            ->delete(route('superadmin.superadmins.destroy', $target))
            ->assertRedirect(route('superadmin.superadmins.index'))
            ->assertSessionHas('success', 'Akun Superadmin berhasil dihapus.');

        $this->assertDatabaseMissing('users', ['id' => $target->id]);
    }

    public function test_superadmin_cannot_deactivate_or_delete_themselves(): void
    {
        $creator = User::factory()->superadmin()->create();

        $this->actingAs($creator)
            ->post(route('superadmin.superadmins.toggle-active', $creator))
            ->assertRedirect()
            ->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun sendiri.');

        $this->delete(route('superadmin.superadmins.destroy', $creator))
            ->assertRedirect()
            ->assertSessionHas('error', 'Anda tidak dapat menghapus akun sendiri.');

        $this->put(route('superadmin.superadmins.update', $creator), [
            'name' => $creator->name,
            'email' => $creator->email,
            'is_active' => '0',
        ])
            ->assertRedirect()
            ->assertSessionHas('error', 'Anda tidak dapat menonaktifkan akun sendiri.');

        $this->assertDatabaseHas('users', ['id' => $creator->id, 'is_active' => true]);
    }

    public function test_superadmin_can_disable_another_account_without_disabling_their_own(): void
    {
        $creator = User::factory()->superadmin()->create();
        $target = User::factory()->superadmin()->create();
        User::factory()->superadmin()->inactive()->create();

        $this->actingAs($creator)
            ->put(route('superadmin.superadmins.update', $target), [
                'name' => $target->name,
                'email' => $target->email,
                'is_active' => '0',
            ])
            ->assertRedirect()
            ->assertSessionHas('success', 'Akun Superadmin berhasil diperbarui.');

        $this->assertDatabaseHas('users', ['id' => $target->id, 'is_active' => false]);
        $this->assertDatabaseHas('users', ['id' => $creator->id, 'is_active' => true]);

        $target->update(['is_active' => true]);
        $this->delete(route('superadmin.superadmins.destroy', $target))
            ->assertRedirect()
            ->assertSessionHas('success', 'Akun Superadmin berhasil dihapus.');

        $this->assertDatabaseHas('users', ['id' => $creator->id, 'is_active' => true]);
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
