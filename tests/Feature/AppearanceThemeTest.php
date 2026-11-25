<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\AppearanceThemes;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class AppearanceThemeTest extends TestCase
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

    public function test_all_twenty_one_themes_are_grouped_and_have_the_requested_backgrounds(): void
    {
        $this->assertSame([
            '#FFFFFF', '#F0F0F0', '#E8EAED', '#EAF4FF', '#E8F1F8', '#E8F5EC', '#F0ECFA', '#F7F1E7',
            '#343434', '#242424', '#121212', '#000000', '#17212B', '#101B2D',
            '#1C1825', '#17231D', '#251B1B', '#24201A', '#20252A', '#171B20',
            '#F5F7FB',
        ], array_column(AppearanceThemes::THEMES, 'background'));
        $this->assertCount(21, AppearanceThemes::keys());
        $categoryThemes = array_merge(
            AppearanceThemes::CATEGORIES['light']['themes'],
            AppearanceThemes::CATEGORIES['dark']['themes'],
            AppearanceThemes::CATEGORIES['original']['themes'],
        );
        $this->assertSame(8, count(AppearanceThemes::CATEGORIES['light']['themes']));
        $this->assertSame(12, count(AppearanceThemes::CATEGORIES['dark']['themes']));
        $this->assertSame(1, count(AppearanceThemes::CATEGORIES['original']['themes']));
        $this->assertSame(21, count(array_unique($categoryThemes)));

        $response = $this->actingAs(User::factory()->create())
            ->get(route('configuracion'))
            ->assertOk()
            ->assertSee('data-theme-apply="soft-gray"', false)
            ->assertSee('data-theme-apply="slate"', false)
            ->assertSee('data-theme-apply="classic-white"', false)
            ->assertSee('data-theme-apply="famfit-original"', false)
            ->assertSee('<details class="fq-card fq-settings-card fq-theme-settings-card" id="fqThemeSelector"', false)
            ->assertDontSee('<details class="fq-card fq-settings-card fq-theme-settings-card" id="fqThemeSelector" open', false)
            ->assertSee('Temas claros')
            ->assertSee('Temas oscuros')
            ->assertSee('Tema original FamFit')
            ->assertSee('Gris suave')
            ->assertSee('Pizarra')
            ->assertSee('Apariencia');

        $this->assertSame(21, substr_count($response->getContent(), 'data-theme-apply="'));
    }

    public function test_theme_is_persisted_per_account_and_rendered_on_the_next_request(): void
    {
        $firstUser = User::factory()->create();
        $secondUser = User::factory()->create();

        $this->actingAs($firstUser)
            ->putJson(route('configuracion.theme'), ['theme' => 'amoled'])
            ->assertOk()
            ->assertExactJson(['theme' => 'amoled']);

        $this->assertDatabaseHas('users', ['id' => $firstUser->id, 'theme' => 'amoled']);
        $this->assertDatabaseHas('users', ['id' => $secondUser->id, 'theme' => 'famfit-original']);

        $this->get(route('configuracion'))
            ->assertOk()
            ->assertSee('data-fq-theme="amoled"', false)
            ->assertSee('class="theme-dark"', false);

        $this->actingAs($secondUser)
            ->get(route('configuracion'))
            ->assertOk()
            ->assertSee('data-fq-theme="famfit-original"', false)
            ->assertDontSee('class="theme-dark"', false);
    }

    public function test_new_accounts_default_to_the_original_theme(): void
    {
        $user = User::factory()->create();

        $this->assertSame('famfit-original', $user->theme);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'theme' => 'famfit-original']);
    }

    public function test_theme_endpoint_rejects_values_outside_the_twenty_one_supported_themes(): void
    {
        $this->actingAs(User::factory()->create())
            ->putJson(route('configuracion.theme'), ['theme' => 'pink'])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('theme');
    }
}
