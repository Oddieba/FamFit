<?php

namespace App\Http\Controllers;

use App\Models\QuestExercise;
use App\Models\QuestExerciseCompletion;
use App\Models\QuestRoutine;
use App\Models\User;
use App\Support\AppearanceThemes;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class QuestController extends Controller
{
    private const AVATARS = ['🦊', '🐼', '🐸', '🐯', '🐨', '🐙', '🦁', '🐢'];

    private const OUTFITS = ['#2864F0', '#A7E66D', '#F3A847', '#B58BEA', '#F28BA8'];

    private const ACCESSORIES = ['Ninguno' => '', 'Gafas' => '🕶️', 'Gorra' => '🧢', 'Estrella' => '⭐'];

    private const EXERCISES = [
        'Sentadillas',
        'Flexiones en pared',
        'Puente de glúteos',
        'Marcha en el sitio',
        'Elevaciones de talones',
        'Plancha con rodillas',
        'Zancadas asistidas',
        'Estiramiento dinámico',
    ];

    private const DAYS = [
        1 => 'Lunes',
        2 => 'Martes',
        3 => 'Miércoles',
        4 => 'Jueves',
        5 => 'Viernes',
        6 => 'Sábado',
        7 => 'Domingo',
    ];

    public function home(Request $request): View
    {
        $user = $request->user();
        $routineStorageAvailable = Schema::hasTable('quest_routines') && Schema::hasTable('quest_exercises');
        $completionStorageAvailable = Schema::hasTable('quest_exercise_completions');
        $routine = $routineStorageAvailable ? $user->questRoutine()->with('exercises')->first() : null;
        $today = now()->dayOfWeekIso;
        $todaysExercises = $routine?->exercises->where('day_of_week', $today) ?? collect();
        $completed = $completionStorageAvailable
            ? $user->questCompletions()->whereDate('completed_on', today())->count()
            : 0;

        return view('menu', [
            'routine' => $routine,
            'routineStorageAvailable' => $routineStorageAvailable,
            'avatarStorageAvailable' => Schema::hasColumn('users', 'avatar_settings'),
            'todaysExercises' => $todaysExercises,
            'completedToday' => $completed,
            'avatar' => $this->avatarSettings(Schema::hasColumn('users', 'avatar_settings') ? $user->avatar_settings : null),
            'level' => intdiv((int) (Schema::hasColumn('users', 'xp') ? $user->xp : 0), 100) + 1,
            'xpProgress' => (int) (Schema::hasColumn('users', 'xp') ? $user->xp : 0) % 100,
        ]);
    }

    public function training(Request $request): View
    {
        $date = $request->validate([
            'date' => ['sometimes', 'date_format:Y-m-d'],
        ])['date'] ?? today()->toDateString();
        $selectedDate = Carbon::parse($date);
        $routineStorageAvailable = Schema::hasTable('quest_routines') && Schema::hasTable('quest_exercises');
        $completionStorageAvailable = Schema::hasTable('quest_exercise_completions');
        $routine = $routineStorageAvailable ? $request->user()->questRoutine()->with('exercises')->first() : null;
        $exercises = $routine?->exercises->where('day_of_week', $selectedDate->dayOfWeekIso)->values() ?? collect();
        $completedIds = $completionStorageAvailable
            ? $request->user()->questCompletions()
                ->whereDate('completed_on', $date)
                ->whereIn('quest_exercise_id', $exercises->pluck('id'))
                ->pluck('quest_exercise_id')
                ->all()
            : [];

        return view('entreno', [
            'routine' => $routine,
            'routineStorageAvailable' => $routineStorageAvailable,
            'exercises' => $exercises,
            'completedIds' => $completedIds,
            'selectedDate' => $selectedDate,
            'days' => self::DAYS,
            'exerciseChoices' => self::EXERCISES,
            'goalChoices' => ['Bienestar', 'Fuerza', 'Movilidad'],
            'experienceChoices' => ['Inicial', 'Intermedio'],
        ]);
    }

    public function storeRoutine(Request $request): RedirectResponse
    {
        if (! Schema::hasTable('quest_routines') || ! Schema::hasTable('quest_exercises')) {
            return back()->with('status', 'Las rutinas estarán disponibles después de instalar la actualización de la base de datos.');
        }

        $data = $request->validate([
            'name' => ['required', 'string', 'max:80'],
            'goal' => ['required', Rule::in(['Bienestar', 'Fuerza', 'Movilidad'])],
            'experience' => ['required', Rule::in(['Inicial', 'Intermedio'])],
            'exercises' => ['required', 'array', 'min:1', 'max:5'],
            'exercises.*.name' => ['required', Rule::in(self::EXERCISES)],
            'exercises.*.day_of_week' => ['required', 'integer', 'between:1,7'],
            'exercises.*.sets' => ['required', 'integer', 'between:1,10'],
            'exercises.*.reps' => ['required', 'integer', 'between:1,100'],
            'exercises.*.rest_seconds' => ['required', 'integer', 'between:15,300'],
        ]);

        $created = DB::transaction(function () use ($request, $data): bool {
            $user = User::query()->lockForUpdate()->findOrFail($request->user()->id);
            if ($user->questRoutine()->exists()) {
                return false;
            }

            $routine = QuestRoutine::create([
                'user_id' => $user->id,
                'name' => $data['name'],
                'goal' => $data['goal'],
                'experience' => $data['experience'],
            ]);

            foreach ($data['exercises'] as $position => $exercise) {
                $routine->exercises()->create($exercise + ['position' => $position]);
            }

            return true;
        });

        return redirect()->route('entreno')->with('status', $created
            ? 'Tu rutina quedó guardada. Puedes cambiar el día para consultar el plan semanal.'
            : 'Ya tienes una rutina activa. Puedes consultar sus ejercicios en esta sección.');
    }

    public function completeExercise(Request $request, string $exercise): RedirectResponse
    {
        if (! Schema::hasTable('quest_exercise_completions') || ! Schema::hasTable('quest_exercises')) {
            return back()->with('status', 'El registro de ejercicios aún no está disponible en esta base de datos.');
        }

        $exercise = QuestExercise::findOrFail($exercise);
        $data = $request->validate([
            'completed_on' => ['required', 'date_format:Y-m-d', 'before_or_equal:today'],
        ]);
        abort_unless($exercise->routine?->user_id === $request->user()->id, 404);

        DB::transaction(function () use ($request, $exercise, $data): void {
            $user = $request->user()->newQuery()->lockForUpdate()->findOrFail($request->user()->id);
            $exists = QuestExerciseCompletion::query()
                ->where('user_id', $user->id)
                ->where('quest_exercise_id', $exercise->id)
                ->whereDate('completed_on', $data['completed_on'])
                ->exists();

            if (! $exists) {
                QuestExerciseCompletion::create([
                    'user_id' => $user->id,
                    'quest_exercise_id' => $exercise->id,
                    'completed_on' => $data['completed_on'],
                ]);
                $user->increment('xp', 10);
            }
        });

        return back()->with('status', 'Ejercicio marcado como completado. ¡Sumaste XP!');
    }

    public function calendar(Request $request): View
    {
        $date = $request->validate([
            'date' => ['sometimes', 'date_format:Y-m-d'],
        ])['date'] ?? today()->toDateString();
        $selectedDate = Carbon::parse($date);
        $routineStorageAvailable = Schema::hasTable('quest_routines') && Schema::hasTable('quest_exercises');
        $completionStorageAvailable = Schema::hasTable('quest_exercise_completions');
        $routine = $routineStorageAvailable ? $request->user()->questRoutine()->with('exercises')->first() : null;
        $exercises = $routine?->exercises->where('day_of_week', $selectedDate->dayOfWeekIso)->values() ?? collect();
        $monthStart = $selectedDate->copy()->startOfMonth();
        $calendarStart = $monthStart->copy()->startOfWeek(Carbon::MONDAY);
        $calendarDays = collect(range(0, 41))
            ->map(fn (int $offset) => $calendarStart->copy()->addDays($offset));
        $completedDates = $completionStorageAvailable
            ? $request->user()->questCompletions()
                ->whereBetween('completed_on', [$monthStart->toDateString(), $selectedDate->copy()->endOfMonth()->toDateString()])
                ->pluck('completed_on')
                ->map(fn ($completedOn) => Carbon::parse($completedOn)->toDateString())
                ->unique()
                ->all()
            : [];
        $completedIds = $completionStorageAvailable
            ? $request->user()->questCompletions()
                ->whereDate('completed_on', $date)
                ->whereIn('quest_exercise_id', $exercises->pluck('id'))
                ->pluck('quest_exercise_id')
                ->all()
            : [];

        return view('calendario', compact(
            'routine',
            'routineStorageAvailable',
            'selectedDate',
            'calendarDays',
            'completedDates',
            'exercises',
            'completedIds',
        ) + [
            'monthLabel' => $selectedDate->locale('es')->translatedFormat('F Y'),
        ]);
    }

    public function profile(Request $request): View
    {
        $user = $request->user();
        $routineStorageAvailable = Schema::hasTable('quest_routines');
        $completionStorageAvailable = Schema::hasTable('quest_exercise_completions');
        $missionStorageAvailable = Schema::hasTable('mission_logs');
        $friendshipStorageAvailable = Schema::hasTable('friendships');
        $achievementStorageAvailable = Schema::hasTable('user_achievements');
        $statsStorageAvailable = Schema::hasTable('user_stats');
        $xp = Schema::hasColumn('users', 'xp') ? (int) $user->xp : 0;
        $weekStart = now()->startOfWeek()->toDateString();
        $weekEnd = now()->endOfWeek()->toDateString();
        $monthStart = now()->startOfMonth();
        $monthEnd = now()->endOfMonth();
        $missionDates = $missionStorageAvailable
            ? DB::table('mission_logs')->where('user_id', $user->id)
                ->whereBetween('completed_at', [$monthStart->copy()->startOfDay(), $monthEnd->copy()->endOfDay()])
                ->pluck('completed_at')->map(fn ($date) => Carbon::parse($date)->toDateString())->unique()->values()
            : collect();
        $calendarDays = collect(range(1, $monthEnd->day))->map(fn (int $day) => [
            'day' => $day,
            'completed' => $missionDates->contains($monthStart->copy()->day($day)->toDateString()),
        ]);

        return view('perfil', [
            'user' => $user,
            'profile' => $user->profile,
            'assessment' => $user->currentAssessment,
            'avatar' => $this->avatarSettings(Schema::hasColumn('users', 'avatar_settings') ? $user->avatar_settings : null),
            'level' => intdiv($xp, 100) + 1,
            'xpProgress' => $xp % 100,
            'completedCount' => $completionStorageAvailable ? $user->questCompletions()->count() : 0,
            'routine' => $routineStorageAvailable ? $user->questRoutine()->first() : null,
            'bioAvailable' => Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'bio'),
            'missionsCount' => $missionStorageAvailable
                ? DB::table('mission_logs')->where('user_id', $user->id)->count()
                : null,
            'friendsCount' => $friendshipStorageAvailable
                ? DB::table('friendships')->where('status', 'accepted')
                    ->where(function ($query) use ($user) {
                        $query->where('requester_id', $user->id)->orWhere('addressee_id', $user->id);
                    })->count()
                : null,
            'weeklyWorkouts' => $completionStorageAvailable
                ? $user->questCompletions()->whereBetween('completed_on', [$weekStart, $weekEnd])
                    ->distinct()->count('completed_on')
                : null,
            'currentStreak' => $statsStorageAvailable
                ? DB::table('user_stats')->where('id', $user->id)->value('current_streak')
                : null,
            'achievementsCount' => $achievementStorageAvailable
                ? DB::table('user_achievements')->where('user_id', $user->id)->count()
                : null,
            'calendarDays' => $calendarDays,
            'monthLabel' => $monthStart->locale('es')->translatedFormat('F Y'),
            'monthOffset' => $monthStart->dayOfWeekIso - 1,
        ]);
    }

    public function updateProfile(Request $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[a-zA-Z0-9_]+$/', Rule::unique('users')->ignore($user->id)],
            'email' => ['required', 'email', 'max:190', Rule::unique('users')->ignore($user->id)],
        ]);
        if (Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'bio')) {
            $bioData = $request->validate(['bio' => ['sometimes', 'nullable', 'string', 'max:300']]);
            $data['bio'] = array_key_exists('bio', $bioData) ? $bioData['bio'] : $user->profile?->bio;
        }

        $bio = $data['bio'] ?? null;
        unset($data['bio']);
        $user->update($data);
        if (Schema::hasTable('profiles') && Schema::hasColumn('profiles', 'bio')) {
            $user->profile()->firstOrNew()->fill(['bio' => $bio])->save();
        }

        return back()->with('status', 'Tu perfil se actualizó correctamente.');
    }

    public function updateAvatar(Request $request): RedirectResponse
    {
        if (! Schema::hasColumn('users', 'avatar_settings')) {
            return back()->with('status', 'La personalización del avatar estará disponible después de instalar la actualización de la base de datos.');
        }

        $data = $request->validate([
            'animal' => ['required', Rule::in(self::AVATARS)],
            'outfit' => ['required', Rule::in(self::OUTFITS)],
            'accessory' => ['required', Rule::in(array_keys(self::ACCESSORIES))],
        ]);
        $data['accessory_symbol'] = self::ACCESSORIES[$data['accessory']];
        $request->user()->forceFill(['avatar_settings' => $data])->save();

        return back()->with('status', 'Tu avatar se guardó correctamente.');
    }

    public function settings(Request $request): View
    {
        $settingsAvailable = Schema::hasColumn('users', 'preferences')
            && Schema::hasColumn('users', 'profile_visible');
        $themeAvailable = Schema::hasColumn('users', 'theme');
        $user = $request->user();

        return view('configuracion', [
            'settingsAvailable' => $settingsAvailable,
            'preferences' => array_merge([
                'notifications_habits' => true,
                'notifications_challenges' => true,
                'reminder_time' => '19:00',
                'dark_mode' => false,
                'water_goal_ml' => 2000,
                'sleep_goal_hours' => 8,
            ], $settingsAvailable ? ($request->user()->preferences ?? []) : []),
            'user' => $user,
            'themes' => AppearanceThemes::THEMES,
            'themeCategories' => AppearanceThemes::CATEGORIES,
            'theme' => AppearanceThemes::resolve($themeAvailable ? $user->theme : null),
            'themeAvailable' => $themeAvailable,
        ]);
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        if (! Schema::hasColumn('users', 'preferences') || ! Schema::hasColumn('users', 'profile_visible')) {
            return back()->with('status', 'Las preferencias estarán disponibles después de instalar la actualización de la base de datos.');
        }

        $data = $request->validate([
            'notifications_habits' => ['required', 'boolean'],
            'notifications_challenges' => ['required', 'boolean'],
            'reminder_time' => ['required', 'date_format:H:i'],
            'dark_mode' => ['required', 'boolean'],
            'profile_visible' => ['required', 'boolean'],
            'water_goal_ml' => ['required', 'integer', 'between:1,10000'],
            'sleep_goal_hours' => ['required', 'integer', 'between:1,24'],
        ]);
        $user = $request->user();
        $preferences = collect($data)->except('profile_visible')->all();
        foreach (['notifications_habits', 'notifications_challenges', 'dark_mode'] as $key) {
            $preferences[$key] = $request->boolean($key);
        }
        $preferences['water_goal_ml'] = (int) $data['water_goal_ml'];
        $preferences['sleep_goal_hours'] = (int) $data['sleep_goal_hours'];

        $user->forceFill([
            'preferences' => $preferences,
            'profile_visible' => $request->boolean('profile_visible'),
        ])->save();

        return back()->with('status', 'Tus preferencias quedaron guardadas.');
    }

    public function updateTheme(Request $request): JsonResponse
    {
        $data = $request->validate([
            'theme' => ['required', 'string', Rule::in(AppearanceThemes::keys())],
        ]);

        if (! Schema::hasColumn('users', 'theme')) {
            return response()->json([
                'message' => 'La selección de temas no está disponible hasta instalar las migraciones pendientes.',
            ], 503);
        }

        $user = $request->user();
        $user->forceFill(['theme' => $data['theme']])->save();

        return response()->json(['theme' => $user->theme]);
    }

    public function updatePassword(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        $request->user()->update(['password' => $data['password']]);

        return back()->with('status', 'Tu contraseña se actualizó correctamente.');
    }

    private function avatarSettings(?array $settings): array
    {
        return array_merge([
            'animal' => self::AVATARS[0],
            'outfit' => self::OUTFITS[0],
            'accessory' => 'Ninguno',
            'accessory_symbol' => '',
        ], $settings ?? []);
    }
}
