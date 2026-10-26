<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_successful_login_redirects_to_the_menu(): void
    {
        $user = User::factory()->create([
            'password' => 'password123',
        ]);

        $this->post(route('login.post'), [
            'email' => $user->email,
            'password' => 'password123',
        ])->assertRedirect(route('menu'))
            ->assertSessionHasNoErrors();

        $this->get(route('menu'))->assertOk();
    }

    public function test_authenticated_user_visiting_login_sees_the_login_page(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('login'))
            ->assertOk()
            ->assertSee('Inicia sesión');
    }

    public function test_login_page_displays_authentication_errors_and_keeps_the_email(): void
    {
        $email = 'missing@example.com';

        $this->from(route('login'))
            ->post(route('login.post'), [
                'email' => $email,
                'password' => 'incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->get(route('login'))
            ->assertSee('El correo o la contraseña son incorrectos.')
            ->assertSee('value="' . $email . '"', false);
    }
}
