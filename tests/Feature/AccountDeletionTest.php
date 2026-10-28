<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AccountDeletionTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_delete_their_account_and_related_reset_token(): void
    {
        $user = User::factory()->create();
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => 'reset-token',
            'created_at' => now(),
        ]);

        $this->actingAs($user)
            ->delete(route('cuenta.destroy'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('password_reset_tokens', ['email' => $user->email]);
    }

    public function test_guest_cannot_delete_an_account(): void
    {
        $this->delete(route('cuenta.destroy'))
            ->assertRedirect(route('login'));
    }
}
