<?php

namespace Tests\Feature;

use App\Models\QuestExerciseCompletion;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class QuestExperienceTest extends TestCase
{
    use RefreshDatabase;

    public function test_all_quest_sections_require_authentication(): void
    {
        foreach (['menu', 'entreno', 'calendario', 'social', 'perfil', 'configuracion'] as $route) {
            $this->get(route($route))->assertRedirect(route('login'));
        }
    }

    public function test_authenticated_user_can_open_each_quest_section(): void
    {
        $this->actingAs(User::factory()->create());

        foreach (['menu', 'entreno', 'calendario', 'social', 'perfil', 'configuracion'] as $route) {
            $this->get(route($route))->assertOk();
        }
    }

    public function test_optional_sections_remain_available_when_quest_tables_are_missing(): void
    {
        Schema::dropIfExists('quest_exercise_completions');
        Schema::dropIfExists('quest_exercises');
        Schema::dropIfExists('quest_routines');

        $this->actingAs(User::factory()->create());

        $this->get(route('menu'))->assertOk();
        $this->get(route('entreno'))->assertOk()->assertSee('rutinas aún no está instalado');
        $this->from(route('entreno'))
            ->post(route('entreno.ejercicios.complete', 1), ['completed_on' => today()->toDateString()])
            ->assertRedirect(route('entreno'));
        $this->get(route('calendario'))->assertOk();
        $this->get(route('perfil'))->assertOk();
    }

    public function test_settings_page_hides_database_preferences_when_the_column_is_missing(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            $table->dropColumn('preferences');
        });

        $this->actingAs(User::factory()->create())
            ->get(route('configuracion'))
            ->assertOk()
            ->assertDontSee('Guardar preferencias')
            ->assertSee('Activar modo oscuro');

        $this->put(route('configuracion.theme'), ['dark_mode' => '1'])
            ->assertRedirect()
            ->assertSessionHas('status');
    }

    public function test_avatar_and_profile_preferences_are_persisted(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('avatar.update'), [
                'animal' => '🐼',
                'outfit' => '#A7E66D',
                'accessory' => 'Gafas',
            ])
            ->assertRedirect();

        $this->assertSame('🐼', $user->fresh()->avatar_settings['animal']);

        $this->put(route('configuracion.update'), [
            'notifications_habits' => '1',
            'notifications_challenges' => '0',
            'reminder_time' => '18:30',
            'dark_mode' => '1',
            'profile_visible' => '0',
            'water_goal_ml' => 1800,
            'sleep_goal_hours' => 7,
        ])->assertRedirect();

        $this->assertSame('18:30', $user->fresh()->preferences['reminder_time']);
        $this->assertTrue($user->fresh()->preferences['dark_mode']);
        $this->assertSame(1800, $user->fresh()->preferences['water_goal_ml']);
        $this->assertFalse($user->fresh()->profile_visible);
    }

    public function test_user_can_create_a_weekly_routine_and_completion_awards_xp_only_once(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('entreno.rutinas.store'), [
            'name' => 'Rutina inicial',
            'goal' => 'Bienestar',
            'experience' => 'Inicial',
            'exercises' => [
                ['name' => 'Sentadillas', 'day_of_week' => 1, 'sets' => 3, 'reps' => 12, 'rest_seconds' => 60],
                ['name' => 'Marcha en el sitio', 'day_of_week' => 3, 'sets' => 2, 'reps' => 20, 'rest_seconds' => 45],
            ],
        ])->assertRedirect(route('entreno'));

        $exercise = $user->questRoutine()->firstOrFail()->exercises()->firstOrFail();
        $payload = ['completed_on' => today()->toDateString()];

        $this->post(route('entreno.ejercicios.complete', $exercise), $payload)->assertRedirect();
        $this->post(route('entreno.ejercicios.complete', $exercise), $payload)->assertRedirect();

        $this->get(route('calendario', ['date' => today()->toDateString()]))
            ->assertOk()
            ->assertSee('VISTA MENSUAL')
            ->assertSee('actividad completada');

        $this->assertSame(10, $user->fresh()->xp);
        $this->assertSame(1, QuestExerciseCompletion::query()->count());
    }

    public function test_profile_edit_validates_and_saves_only_account_fields(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('perfil.update'), [
                'name' => 'Campeona',
                'username' => 'campeona_1',
                'email' => $user->email,
            ])
            ->assertRedirect();

        $this->assertSame('Campeona', $user->fresh()->name);
        $this->assertSame('campeona_1', $user->fresh()->username);
    }

    public function test_password_change_is_hashed_and_saved_to_the_user_record(): void
    {
        $user = User::factory()->create(['password' => 'password']);

        $this->actingAs($user)
            ->put(route('configuracion.password'), [
                'current_password' => 'password',
                'password' => 'new-password-123',
                'password_confirmation' => 'new-password-123',
            ])
            ->assertRedirect();

        $savedPassword = $user->fresh()->getRawOriginal('password');
        $this->assertNotSame('new-password-123', $savedPassword);
        $this->assertTrue(Hash::check('new-password-123', $savedPassword));
    }

    public function test_dark_mode_preference_is_saved_to_the_user_record(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->put(route('configuracion.theme'), ['dark_mode' => '1'])
            ->assertRedirect();

        $this->assertTrue($user->fresh()->preferences['dark_mode']);
    }

    public function test_onboarding_saves_the_authenticated_users_profile(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post(route('onboarding.guardar'), [
                'edad' => 24,
                'peso' => 68.5,
                'altura' => 175,
                'genero' => 'masculino',
                'objetivo' => 'perder_grasa',
                'experiencia_ejercicio' => 'principiante',
                'disponibilidad' => [
                    'lunes' => 'tarde',
                    'miercoles' => 'manana',
                ],
                'tiene_lesion' => 'si',
                'descripcion_lesion' => 'Molestia de rodilla',
            ])
            ->assertRedirect(route('menu'));

        $profile = $user->profile()->firstOrFail();

        $this->assertSame(24, $profile->age);
        $this->assertSame('tarde', $profile->availability['lunes']);
        $this->assertTrue($profile->has_injury);
        $this->assertSame('Molestia de rodilla', $profile->injury_description);
    }
}
