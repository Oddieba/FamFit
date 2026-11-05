<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_persists_the_user_and_starts_a_session(): void
    {
        $this->post(route('registro.store'), [
            'username' => 'ana_fit',
            'email' => 'ana@example.com',
            'password' => 'Clave1234',
            'password_confirmation' => 'Clave1234',
            'visible' => '1',
            'terminos' => '1',
        ])->assertRedirect(route('registro'))
            ->assertSessionHas('registration_success', true);

        $this->assertDatabaseHas('users', [
            'username' => 'ana_fit',
            'email' => 'ana@example.com',
            'profile_visible' => true,
        ]);

        $user = User::where('email', 'ana@example.com')->firstOrFail();
        $this->assertTrue(password_verify('Clave1234', $user->password));
        $this->assertTrue(Auth::check());
        $this->assertSame($user->id, Auth::id());
    }

    public function test_registration_rejects_duplicate_email_and_username(): void
    {
        $user = User::factory()->create([
            'email' => 'existente@example.com',
            'username' => 'ana_fit',
        ]);

        $this->from(route('registro'))
            ->post(route('registro.store'), [
                'username' => $user->username,
                'email' => $user->email,
                'password' => 'Clave1234',
                'password_confirmation' => 'Clave1234',
                'terminos' => '1',
            ])
            ->assertRedirect(route('registro'))
            ->assertSessionHasErrors(['email', 'username']);

        $this->assertDatabaseCount('users', 1);
    }

    public function test_registration_requires_terms_acceptance(): void
    {
        $this->from(route('registro'))
            ->post(route('registro.store'), [
                'username' => 'ana_fit',
                'email' => 'ana@example.com',
                'password' => 'Clave1234',
                'password_confirmation' => 'Clave1234',
            ])
            ->assertRedirect(route('registro'))
            ->assertSessionHasErrors('terminos');

        $this->assertDatabaseCount('users', 0);
    }
}
