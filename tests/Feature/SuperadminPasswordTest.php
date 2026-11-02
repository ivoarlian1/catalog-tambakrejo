<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class SuperadminPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_superadmin_can_open_their_password_page(): void
    {
        $superadmin = User::factory()->superadmin()->create();

        $this->actingAs($superadmin)
            ->get(route('superadmin.password.edit'))
            ->assertOk()
            ->assertSee('Ubah Password')
            ->assertSee('current_password', false)
            ->assertSee('password_confirmation', false);
    }

    public function test_superadmin_can_change_password_with_the_current_password(): void
    {
        $superadmin = User::factory()->superadmin()->create([
            'password' => 'OldPassword123',
        ]);

        $this->actingAs($superadmin)
            ->put(route('superadmin.password.update'), [
                'current_password' => 'OldPassword123',
                'password' => 'NewPassword456',
                'password_confirmation' => 'NewPassword456',
            ])
            ->assertRedirect(route('superadmin.password.edit'))
            ->assertSessionHas('success', 'Password superadmin berhasil diperbarui.');

        $this->assertTrue(Hash::check('NewPassword456', $superadmin->fresh()->password));
        $this->assertFalse(Hash::check('OldPassword123', $superadmin->fresh()->password));
    }

    public function test_superadmin_cannot_change_password_with_an_incorrect_current_password(): void
    {
        $superadmin = User::factory()->superadmin()->create([
            'password' => 'OldPassword123',
        ]);
        $originalPassword = $superadmin->password;

        $this->from(route('superadmin.password.edit'))
            ->actingAs($superadmin)
            ->put(route('superadmin.password.update'), [
                'current_password' => 'WrongPassword123',
                'password' => 'NewPassword456',
                'password_confirmation' => 'NewPassword456',
            ])
            ->assertRedirect(route('superadmin.password.edit'))
            ->assertSessionHasErrors(['current_password' => 'Password saat ini tidak sesuai.']);

        $this->assertSame($originalPassword, $superadmin->fresh()->password);
    }

    public function test_superadmin_password_must_be_confirmed_and_meet_the_password_policy(): void
    {
        $superadmin = User::factory()->superadmin()->create([
            'password' => 'OldPassword123',
        ]);

        $this->from(route('superadmin.password.edit'))
            ->actingAs($superadmin)
            ->put(route('superadmin.password.update'), [
                'current_password' => 'OldPassword123',
                'password' => 'short',
                'password_confirmation' => 'different',
            ])
            ->assertRedirect(route('superadmin.password.edit'))
            ->assertSessionHasErrors(['password']);

        $this->assertTrue(Hash::check('OldPassword123', $superadmin->fresh()->password));
    }

    public function test_only_superadmins_can_open_the_password_page(): void
    {
        $catalogAdmin = User::factory()->catalogAdmin()->create();

        $this->actingAs($catalogAdmin)
            ->get(route('superadmin.password.edit'))
            ->assertForbidden();
    }
}
