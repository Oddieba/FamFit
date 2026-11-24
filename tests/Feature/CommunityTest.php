<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class CommunityTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        DB::table('roles')->insert(['id' => 1, 'name' => 'Usuario']);
    }

    private function person(string $username, bool $visible = true): User
    {
        return User::factory()->create(['username' => $username, 'profile_visible' => $visible])->refresh();
    }

    private function change(User $actor, User $peer, string $action)
    {
        return $this->actingAs($actor)->postJson(route('social.friendship'), ['person' => $peer->id, 'action' => $action]);
    }

    public function test_blocking_from_discovery_is_private_and_only_the_owner_can_undo_it(): void
    {
        $guio = $this->person('danguio');
        $jose = $this->person('jose', false);
        $third = $this->person('ines');
        $this->postJson(route('social.friendship'), ['person' => $jose->id, 'action' => 'block'])->assertUnauthorized();
        $this->change($guio, $guio, 'block')->assertUnprocessable();
        $this->change($guio, $jose, 'block')->assertOk();
        $this->change($guio, $jose, 'block')->assertOk();
        $this->assertDatabaseCount('blocks', 1);
        $blockedList = route('social.people', ['list' => 'blocked', 'q' => '@jose']);
        $this->actingAs($guio)->getJson($blockedList)->assertOk()->assertJsonCount(1, 'people')
            ->assertJsonPath('people.0', ['id' => $jose->id, 'username' => 'jose', 'display' => 'jose', 'avatar' => null]);
        foreach ([[$guio, $jose], [$jose, $guio]] as [$actor, $peer]) {
            $this->actingAs($actor)->getJson(route('social.people', ['q' => $peer->username]))->assertJsonCount(0, 'people');
            $this->change($actor, $peer, 'send')->assertNotFound();
        }
        foreach ([$jose, $third] as $actor) {
            $this->actingAs($actor)->getJson(route('social.people', ['list' => 'blocked']))->assertJsonCount(0, 'people');
            $this->change($actor, $guio, 'unblock')->assertOk();
        }
        $this->assertDatabaseHas('blocks', ['blocker_id' => $guio->id, 'blocked_id' => $jose->id]);
        $this->change($guio, $jose, 'unblock')->assertOk();
        $this->change($guio, $jose, 'unblock')->assertOk();
        $this->actingAs($guio)->getJson($blockedList)->assertJsonCount(0, 'people');
        $this->actingAs($guio)->getJson(route('social.people', ['q' => 'jose']))->assertJsonPath('people.0.id', $jose->id);
        $this->change($jose, $guio, 'send')->assertOk();
    }

    public function test_blocking_clears_incoming_outgoing_and_accepted_links_even_if_the_peer_is_private(): void
    {
        $guio = $this->person('danguio');
        $jose = $this->person('jose', false);
        foreach (['incoming', 'outgoing', 'friends'] as $list) {
            if ($list === 'incoming') {
                $this->change($jose, $guio, 'send')->assertOk();
            } else {
                $this->change($guio, $jose, 'send')->assertOk();
                if ($list === 'friends') {
                    $this->change($jose, $guio, 'accept')->assertOk();
                }
            }
            $this->change($guio, $jose, 'unblock')->assertOk();
            $this->assertDatabaseCount('friendships', 1);
            $this->change($guio, $jose, 'block')->assertOk();
            $this->assertDatabaseCount('friendships', 0);
            $this->actingAs($guio)->getJson(route('social.people', ['list' => $list]))
                ->assertJsonCount(0, 'people')->assertJsonPath('counts.'.$list, 0);
            $this->change($jose, $guio, 'accept')->assertNotFound();
            $this->change($guio, $jose, 'unblock')->assertOk();
            $this->assertDatabaseCount('friendships', 0);
            $this->actingAs($guio)->getJson(route('social.people', ['q' => 'jose']))
                ->assertJsonCount(1, 'people')->assertJsonPath('people.0.display', 'jose');
        }
    }

    public function test_unblocking_preserves_a_reciprocal_block_and_clears_a_legacy_link(): void
    {
        $guio = $this->person('danguio');
        $jose = $this->person('jose');
        $this->change($guio, $jose, 'send')->assertOk();
        DB::table('blocks')->insert([
            ['blocker_id' => $guio->id, 'blocked_id' => $jose->id],
            ['blocker_id' => $jose->id, 'blocked_id' => $guio->id],
        ]);
        $this->change($guio, $jose, 'unblock')->assertOk();
        $this->assertDatabaseCount('friendships', 0);
        $this->assertDatabaseCount('blocks', 1);
        $this->change($guio, $jose, 'send')->assertNotFound();
        $this->actingAs($jose)->getJson(route('social.people', ['list' => 'blocked']))
            ->assertJsonPath('people.0.id', $guio->id);
    }

    public function test_alias_search_includes_active_unblocked_accounts_with_either_visibility(): void
    {
        $me = $this->person('guio');
        $public = $this->person('ana_runner');
        $private = $this->person('ana_privada', false);
        $blocked = $this->person('ana_bloqueada');
        $other = $this->person('anaXrunner');
        $this->person('ana_suspendida')->forceFill(['status' => 'suspended'])->save();
        $this->person('ana_eliminada')->forceFill(['deleted_at' => now()])->save();
        DB::table('blocks')->insert(['blocker_id' => $blocked->id, 'blocked_id' => $me->id]);
        DB::table('profiles')->insert([
            ['user_id' => $public->id, 'display_name' => 'Ana Deportista'],
            ['user_id' => $private->id, 'display_name' => 'Nombre reservado'],
        ]);
        $this->actingAs($me)->getJson(route('social.people'))->assertOk()->assertJsonCount(3, 'people')
            ->assertJsonFragment(['id' => $other->id])->assertJsonFragment(['id' => $private->id]);
        $this->actingAs($me)->getJson(route('social.people', ['q' => '@ana_']))
            ->assertOk()->assertJsonCount(2, 'people')
            ->assertJsonPath('people.0', ['id' => $private->id, 'username' => 'ana_privada', 'display' => 'ana_privada', 'avatar' => null])
            ->assertJsonPath('people.1', ['id' => $public->id, 'username' => 'ana_runner', 'display' => 'Ana Deportista', 'avatar' => null]);
        $this->getJson(route('social.people', ['q' => 'Nombre reservado']))->assertJsonCount(0, 'people');
        $this->change($me, $private, 'send')->assertOk();
        $this->actingAs($me)->getJson(route('social.people', ['list' => 'outgoing']))->assertJsonPath('people.0.display', 'ana_privada');
        $this->change($private, $me, 'accept')->assertOk();
        $this->actingAs($me)->getJson(route('social.people', ['list' => 'friends']))->assertJsonPath('people.0.display', 'ana_privada');
        $this->change($me, $blocked, 'send')->assertNotFound();
        $this->change($me, $me, 'send')->assertUnprocessable();
    }

    public function test_request_ownership_transitions_counts_and_removal(): void
    {
        $a = $this->person('guio', false);
        $b = $this->person('ines');
        $stranger = $this->person('tercero');
        $this->change($a, $b, 'send')->assertOk();
        $this->change($a, $b, 'send')->assertConflict();
        $this->change($a, $b, 'accept')->assertConflict();
        $this->change($stranger, $b, 'accept')->assertConflict();
        $this->actingAs($b)->getJson(route('social.people', ['list' => 'incoming']))
            ->assertOk()->assertJsonPath('counts.incoming', 1)->assertJsonPath('people.0.username', 'guio');
        $this->change($b, $a, 'reject')->assertOk();
        $this->change($a, $b, 'send')->assertOk();
        $this->change($a, $b, 'cancel')->assertOk();
        $this->change($a, $b, 'send')->assertOk();
        $this->change($b, $a, 'accept')->assertOk();
        $this->actingAs($a)->getJson(route('social.people', ['list' => 'friends']))
            ->assertJsonPath('counts.friends', 1)->assertJsonPath('counts.outgoing', 0)
            ->assertJsonPath('people.0.id', $b->id);
        $this->change($b, $a, 'remove')->assertOk();
        $this->assertDatabaseCount('friendships', 0);
    }

    public function test_changing_visibility_only_changes_shared_details_and_preserves_theme(): void
    {
        $jose = $this->person('jose', false);
        $guio = $this->person('danguio');
        DB::table('profiles')->insert(['user_id' => $jose->id, 'display_name' => 'José Corredor']);
        $settings = [
            'notifications_habits' => '1',
            'notifications_challenges' => '0',
            'reminder_time' => '19:00',
            'water_goal_ml' => '2000',
            'sleep_goal_hours' => '8',
        ];
        foreach ([false, true] as $dark) {
            $this->actingAs($jose)->put(route('configuracion.theme'), ['dark_mode' => (int) $dark])
                ->assertSessionHasNoErrors();
            foreach ([true, false] as $visible) {
                $this->actingAs($jose->fresh())->from(route('configuracion'))
                    ->put(route('configuracion.update'), $settings + ['profile_visible' => (int) $visible])
                    ->assertRedirect(route('configuracion'))->assertSessionHasNoErrors();
                $saved = $jose->fresh();
                $this->assertSame($dark, $saved->preferences['dark_mode']);
                $this->assertSame($visible, $saved->profile_visible);
                $this->actingAs($guio)->getJson(route('social.people', ['q' => 'jose']))
                    ->assertOk()->assertJsonCount(1, 'people')->assertJsonPath('people.0.username', 'jose')
                    ->assertJsonPath('people.0.display', $visible ? 'José Corredor' : 'jose');
            }
        }
    }

    public function test_general_preferences_allow_an_unset_theme_and_still_accept_an_explicit_theme(): void
    {
        $user = $this->person('jose', false);
        $input = [
            'notifications_habits' => '0',
            'notifications_challenges' => '1',
            'reminder_time' => '18:00',
            'water_goal_ml' => '2500',
            'sleep_goal_hours' => '7',
            'profile_visible' => '1',
        ];
        $this->actingAs($user)->put(route('configuracion.update'), $input)->assertSessionHasNoErrors();
        $this->assertArrayNotHasKey('dark_mode', $user->fresh()->preferences);
        $this->put(route('configuracion.update'), $input + ['dark_mode' => '1'])->assertSessionHasNoErrors();
        $this->assertTrue($user->fresh()->preferences['dark_mode']);
        $this->put(route('configuracion.theme'), ['dark_mode' => '0'])->assertSessionHasNoErrors();
        $this->assertSame(2500, $user->fresh()->preferences['water_goal_ml']);
        $this->assertTrue($user->fresh()->profile_visible);
        $this->put(route('configuracion.theme'), [])->assertSessionHasErrors('dark_mode');
    }

    public function test_authenticated_page_inherits_both_settings_themes(): void
    {
        $this->get(route('social'))->assertRedirect(route('login'));
        $this->getJson(route('social.people'))->assertUnauthorized();
        $user = $this->person('guio');
        $this->actingAs($user)->get(route('social'))->assertOk()->assertSee('data-fq-dark-mode=""', false);
        foreach ([true, false] as $dark) {
            $this->put(route('configuracion.theme'), ['dark_mode' => (int) $dark])->assertRedirect();
            $this->actingAs($user->fresh())->get(route('social'))->assertOk()
                ->assertSee('data-fq-dark-mode="'.($dark ? 'true' : 'false').'"', false)
                ->assertSee('Descubrir personas')->assertSee('Ajustar privacidad');
        }
    }
}
