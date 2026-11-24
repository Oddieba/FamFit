<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('roles')->insert(['id' => 1, 'name' => 'Usuario']);
    }

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

    public function test_new_account_with_sharing_off_can_be_found_by_alias(): void
    {
        $viewer = User::factory()->create();
        $this->post(route('registro.store'), [
            'username' => 'jose',
            'email' => 'jose@example.com',
            'password' => 'Clave1234',
            'password_confirmation' => 'Clave1234',
            'terminos' => '1',
        ])->assertSessionHas('registration_success', true);
        $this->assertDatabaseHas('users', ['username' => 'jose', 'profile_visible' => false]);
        $this->actingAs($viewer->refresh())->getJson(route('social.people', ['q' => '@jose']))
            ->assertOk()->assertJsonCount(1, 'people')->assertJsonPath('people.0.display', 'jose');
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
