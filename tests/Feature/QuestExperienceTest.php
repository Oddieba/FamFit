<?php

namespace Tests\Feature;

use App\Models\FamFitActivity;
use App\Models\QuestExercise;
use App\Models\QuestExerciseCompletion;
use App\Models\QuestRoutine;
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

    public function test_famfit_calendar_routes_are_available_for_authenticated_users(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route('famfit.calendar'))
            ->assertOk()
            ->assertSee('0.00 h')
            ->assertDontSee('data-day-create', false);

        $this->post(route('famfit.activities.store'), [
            'title' => 'Caminata matutina',
            'activity_type' => 'Caminata',
            'activity_date' => today()->toDateString(),
            'start_time' => '00:00',
            'end_time' => '01:00',
            'status' => 'planned',
        ])->assertRedirect();

        $activity = FamFitActivity::query()->firstOrFail();
        $this->assertSame('00:00', substr((string) $activity->start_time, 0, 5));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('famfit.activities.store'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('famfit.activities.update'));
        $this->assertTrue(\Illuminate\Support\Facades\Route::has('famfit.activities.destroy'));
    }

    public function test_calendar_rejects_overlapping_activities(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('famfit.activities.store'), [
            'title' => 'Primera actividad',
            'activity_type' => 'Ejercicio',
            'activity_date' => today()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'planned',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->post(route('famfit.activities.store'), [
            'title' => 'Actividad superpuesta',
            'activity_type' => 'Caminata',
            'activity_date' => today()->toDateString(),
            'start_time' => '09:30',
            'end_time' => '10:30',
            'status' => 'planned',
        ])->assertRedirect()->assertSessionHasErrors('start_time');

        $this->assertSame(1, FamFitActivity::where('user_id', $user->id)->count());
    }

    public function test_calendar_activity_reminders_can_be_configured_and_invalid_values_are_rejected(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->post(route('famfit.activities.store'), [
            'title' => 'Caminata con aviso',
            'activity_type' => 'Caminata',
            'activity_date' => today()->toDateString(),
            'start_time' => '23:00',
            'end_time' => '23:30',
            'reminder_minutes_before' => '15',
            'status' => 'planned',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $activity = FamFitActivity::query()->firstOrFail();
        $this->assertSame(15, $activity->reminder_minutes_before);

        $this->get(route('famfit.calendar'))
            ->assertOk()
            ->assertSee('Preparando notificaciones automáticas')
            ->assertSee('15 minutos antes')
            ->assertSee('15 min antes');

        $this->post(route('famfit.activities.store'), [
            'title' => 'Aviso no válido',
            'activity_type' => 'Caminata',
            'activity_date' => today()->addDay()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'reminder_minutes_before' => '20',
            'status' => 'planned',
        ])->assertSessionHasErrors('reminder_minutes_before');

        $this->assertSame(1, FamFitActivity::query()->count());
    }

    public function test_calendar_enforces_recovery_days_and_weekly_intense_session_limit(): void
    {
        $user = User::factory()->create();
        $user->profile()->create(['experience' => 'principiante']);
        $this->actingAs($user);

        $monday = now()->startOfWeek(\Carbon\Carbon::MONDAY);
        $schedule = function (string $type, int $dayOffset, string $start = '09:00') use ($monday) {
            $startTime = \Carbon\Carbon::createFromFormat('H:i', $start);

            return [
                'title' => $type.' '.$dayOffset,
                'activity_type' => $type,
                'activity_date' => $monday->copy()->addDays($dayOffset)->toDateString(),
                'start_time' => $start,
                'end_time' => $startTime->addHour()->format('H:i'),
                'status' => 'planned',
            ];
        };

        $this->get(route('famfit.calendar'))
            ->assertOk()
            ->assertSee('máximo 3 por');

        $this->post(route('famfit.activities.store'), [
            ...$schedule('Ejercicio', 0),
            'end_time' => '10:30',
        ])->assertSessionHasErrors('end_time');

        $this->post(route('famfit.activities.store'), $schedule('Ejercicio', 0))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->post(route('famfit.activities.store'), $schedule('Deporte', 1))
            ->assertSessionHasErrors('activity_date');

        $this->post(route('famfit.activities.store'), $schedule('Yoga', 1))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->post(route('famfit.activities.store'), $schedule('Ciclismo', 2))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->post(route('famfit.activities.store'), $schedule('Ejercicio', 4))
            ->assertRedirect()
            ->assertSessionHasNoErrors();

        $this->post(route('famfit.activities.store'), $schedule('Deporte', 6))
            ->assertSessionHasErrors('activity_date');

        $this->assertSame(4, FamFitActivity::query()->count());
    }

    public function test_calendar_activities_are_isolated_by_user(): void
    {
        $owner = User::factory()->create();
        $otherUser = User::factory()->create();

        $this->actingAs($owner)->post(route('famfit.activities.store'), [
            'title' => 'Actividad privada',
            'activity_type' => 'Caminata',
            'activity_date' => today()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:00',
            'status' => 'planned',
        ])->assertRedirect();

        $activity = FamFitActivity::query()->firstOrFail();
        $this->assertSame($owner->id, $activity->user_id);

        $this->actingAs($otherUser)
            ->get(route('famfit.calendar'))
            ->assertOk()
            ->assertDontSee('Actividad privada');
    }

    public function test_weekly_availability_is_saved_and_restricts_activity_schedules(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->put(route('famfit.availability.update'), [
            'availability' => [
                'lunes' => ['mañana', 'noche'],
            ],
        ])->assertRedirect()->assertSessionHas('success');

        $this->assertSame(
            ['mañana', 'noche'],
            $user->profile()->firstOrFail()->availability['lunes']
        );

        $this->get(route('famfit.calendar'))
            ->assertOk()
            ->assertSee('Disponibilidad semanal')
            ->assertSee('Mañana · 06:00–12:00');

        $monday = now()->startOfWeek(\Carbon\Carbon::MONDAY)->toDateString();

        $this->post(route('famfit.activities.store'), [
            'title' => 'Actividad disponible',
            'activity_type' => 'Ejercicio',
            'activity_date' => $monday,
            'start_time' => '08:00',
            'end_time' => '09:00',
            'status' => 'planned',
        ])->assertRedirect()->assertSessionHasNoErrors();

        $this->assertSame(1, FamFitActivity::query()->count());
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
        $this->get(route('calendario'))
            ->assertOk()
            ->assertSee('Calendario inteligente')
            ->assertSee('Nueva actividad')
            ->assertSee('Reducir calendario')
            ->assertSee('Agrandar calendario');
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
            ->assertSee('Calendario inteligente')
            ->assertSee('Nueva actividad');

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

    public function test_profile_displays_initial_assessment_answers_as_read_only(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user)->post(route('onboarding.guardar'), [
            'edad' => 24,
            'peso' => 68.5,
            'altura' => 175,
            'genero' => 'masculino',
            'objetivo' => 'perder_grasa',
            'experiencia_ejercicio' => 'principiante',
            'disponibilidad' => [
                'lunes' => 'mañana',
                'miercoles' => 'tarde',
                'sabado' => 'noche',
            ],
            'tiene_lesion' => 'si',
            'descripcion_lesion' => 'Molestia de rodilla',
        ])->assertRedirect(route('menu'));

        $this->get(route('perfil'))
            ->assertOk()
            ->assertSee('Respuestas guardadas')
            ->assertSee('id="testAge" aria-readonly="true">24</output>', false)
            ->assertSee('id="testGoal" aria-readonly="true">Perder grasa / Bajar de peso</output>', false)
            ->assertSee('fq-week-chart', false)
            ->assertSee('fq-slot-morning">Mañana</span>', false)
            ->assertSee('fq-slot-afternoon">Tarde</span>', false)
            ->assertSee('fq-slot-night">Noche</span>', false)
            ->assertSee('Sin disponibilidad')
            ->assertSee('id="testInjuryDescription" aria-readonly="true">Molestia de rodilla</output>', false)
            ->assertSee('name="name"', false)
            ->assertDontSee('name="name" value="'.$user->name.'" readonly', false);
    }

    public function test_repeating_assessment_keeps_old_results_until_new_answers_are_saved(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $firstAnswers = [
            'edad' => 24,
            'peso' => 68.5,
            'altura' => 175,
            'genero' => 'masculino',
            'objetivo' => 'perder_grasa',
            'experiencia_ejercicio' => 'principiante',
            'disponibilidad' => ['lunes' => 'tarde'],
            'tiene_lesion' => 'no',
            'descripcion_lesion' => '',
        ];
        $this->post(route('onboarding.guardar'), $firstAnswers)->assertRedirect(route('menu'));
        $firstAssessment = $user->initialAssessments()->firstOrFail();

        $this->get(route('onboarding', ['retake' => 1]))
            ->assertOk()
            ->assertSee('Cancelar Test');
        $this->assertSame(24, $user->profile()->firstOrFail()->age);
        $this->assertTrue($firstAssessment->fresh()->is_current);

        $this->post(route('onboarding.guardar'), array_merge($firstAnswers, [
            'edad' => 25,
            'peso' => 70,
            'objetivo' => 'ganar_musculo',
        ]))->assertRedirect(route('menu'));

        $this->assertSame(25, $user->profile()->firstOrFail()->age);
        $this->assertSame('ganar_musculo', $user->profile()->firstOrFail()->goals);
        $this->assertFalse($firstAssessment->fresh()->is_current);
        $this->assertSame(2, $user->initialAssessments()->count());
        $this->assertTrue($user->currentAssessment->is_current);
        $this->assertSame(25, $user->currentAssessment->answers['edad']);
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
