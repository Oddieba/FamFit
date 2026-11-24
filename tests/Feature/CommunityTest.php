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

    public function test_discovery_and_search_return_only_visible_unblocked_accounts_and_public_fields(): void
    {
        $me = $this->person('guio');
        $public = $this->person('ana_runner');
        $private = $this->person('ana_privada', false);
        $blocked = $this->person('ana_bloqueada');
        $this->person('anaXrunner');
        DB::table('blocks')->insert(['blocker_id' => $blocked->id, 'blocked_id' => $me->id]);
        $this->actingAs($me)->getJson(route('social.people', ['q' => '@ana_']))
            ->assertOk()->assertJsonCount(1, 'people')->assertJsonPath('people.0.id', $public->id)
            ->assertJsonMissingPath('people.0.email')->assertJsonMissingPath('people.0.schedule');
        $this->change($me, $private, 'send')->assertNotFound();
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
