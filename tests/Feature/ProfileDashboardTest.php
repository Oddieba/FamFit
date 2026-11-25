<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class ProfileDashboardTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        DB::table('roles')->insert([
            'id' => 1,
            'name' => 'user',
            'description' => 'Regular account',
        ]);
    }

    public function test_user_can_save_biography_from_profile_edit_form(): void
    {
        $user = User::factory()->create(['username' => 'profile_editor']);

        $this->actingAs($user)->put(route('perfil.update'), [
            'name' => $user->name,
            'username' => $user->username,
            'email' => $user->email,
            'bio' => 'Me gusta entrenar al aire libre.',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertDatabaseHas('profiles', [
            'user_id' => $user->id,
            'bio' => 'Me gusta entrenar al aire libre.',
        ]);
    }

    public function test_profile_dashboard_uses_saved_mission_and_friend_data(): void
    {
        $user = User::factory()->create();
        $friend = User::factory()->create();
        $missionId = DB::table('missions')->insertGetId([
            'code' => 'profile-dashboard-test',
            'name' => 'Test mission',
            'category' => 'hydration',
            'completion_mode' => 'button',
            'xp_reward' => 0,
            'coin_reward' => 0,
            'daily_reward_cap' => 1,
            'allows_photo' => false,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        DB::table('mission_logs')->insert([
            'user_id' => $user->id,
            'mission_id' => $missionId,
            'completed_at' => now(),
            'reward_granted' => false,
        ]);
        DB::table('friendships')->insert([
            'requester_id' => $user->id,
            'addressee_id' => $friend->id,
            'status' => 'accepted',
            'created_at' => now(),
            'responded_at' => now(),
        ]);

        $this->actingAs($user)->get(route('perfil'))
            ->assertOk()
            ->assertSee('Misiones')
            ->assertSee('Amigos')
            ->assertSee('>1</strong>', false)
            ->assertSee('aria-label="'.now()->day.', misión completada"', false);
    }

    public function test_theme_selector_is_not_rendered_on_profile(): void
    {
        $this->actingAs(User::factory()->create())->get(route('perfil'))
            ->assertOk()
            ->assertDontSee('fq-profile-themes-card');
    }
}
