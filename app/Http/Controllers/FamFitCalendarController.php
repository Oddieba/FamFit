<?php

namespace App\Http\Controllers;

use App\Models\FamFitActivity;
use Carbon\WeekDay;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class FamFitCalendarController extends Controller
{
    private const AVAILABILITY_DAYS = [
        'lunes' => 'Lunes',
        'martes' => 'Martes',
        'miercoles' => 'Miércoles',
        'jueves' => 'Jueves',
        'viernes' => 'Viernes',
        'sabado' => 'Sábado',
        'domingo' => 'Domingo',
    ];

    private const AVAILABILITY_SLOTS = [
        'mañana' => ['label' => 'Mañana · 06:00–12:00', 'start' => 360, 'end' => 720],
        'tarde' => ['label' => 'Tarde · 12:00–18:00', 'start' => 720, 'end' => 1080],
        'noche' => ['label' => 'Noche · 18:00–00:00', 'start' => 1080, 'end' => 1440],
    ];

    public function index(Request $request): View
    {
        $date = $request->query('date');
        $month = $request->query('month');

        try {
            $selectedDate = $date
                ? Carbon::createFromFormat('!Y-m-d', $date)->startOfDay()
                : ($month
                    ? Carbon::createFromFormat('!Y-m', $month)->startOfMonth()
                    : now()->startOfDay());
        } catch (\Throwable $exception) {
            $selectedDate = now()->startOfDay();
        }

        $currentMonth = $selectedDate->copy()->startOfMonth();
        $weekStart = $selectedDate->copy()->startOfWeek(WeekDay::Monday);
        $weekEnd = $weekStart->copy()->endOfWeek(WeekDay::Sunday);

        $activities = FamFitActivity::query()
            ->where('user_id', $request->user()->id)
            ->whereBetween('activity_date', [$weekStart->toDateString(), $weekEnd->toDateString()])
            ->orderBy('start_time')
            ->get()
            ->groupBy(fn (FamFitActivity $activity) => Carbon::parse((string) $activity->getRawOriginal('activity_date'))->toDateString());

        $miniStart = $currentMonth->copy()->startOfMonth()->startOfWeek(WeekDay::Monday);
        $miniEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(WeekDay::Sunday);
        $miniActivityDates = FamFitActivity::query()
            ->where('user_id', $request->user()->id)
            ->whereBetween('activity_date', [$miniStart->toDateString(), $miniEnd->toDateString()])
            ->distinct()
            ->pluck('activity_date')
            ->map(fn ($date) => Carbon::parse($date)->toDateString())
            ->flip();

        $weekActivities = FamFitActivity::query()
            ->where('user_id', $request->user()->id)
            ->whereBetween('activity_date', [$weekStart->toDateString(), $weekEnd->toDateString()]);
        $reminderActivities = FamFitActivity::query()
            ->where('user_id', $request->user()->id)
            ->whereBetween('activity_date', [today()->toDateString(), today()->addDays(14)->toDateString()])
            ->where('status', 'planned')
            ->whereNotNull('reminder_minutes_before')
            ->get(['id', 'title', 'activity_date', 'start_time', 'reminder_minutes_before']);
        $availability = $this->normalizeAvailability($request->user()->profile?->availability);
        $profile = $request->user()->profile;
        $assessment = $request->user()->currentAssessment;
        $preferences = $request->user()->preferences ?? [];
        $notificationPreferences = [
            'habits_enabled' => (bool) ($preferences['notifications_habits'] ?? true),
            'reminder_time' => $preferences['reminder_time'] ?? '19:00',
        ];

        return view('calendar', [
            'currentMonth' => $currentMonth,
            'selectedDate' => $selectedDate,
            'weekStart' => $weekStart,
            'weekEnd' => $weekEnd,
            'activitiesByDate' => $activities,
            'miniActivityDates' => $miniActivityDates,
            'weekCount' => (clone $weekActivities)->count(),
            'completedCount' => (clone $weekActivities)->where('status', 'completed')->count(),
            'weekMinutes' => (clone $weekActivities)->sum('duration_minutes'),
            'weekPoints' => (clone $weekActivities)->where('status', 'completed')->sum('points'),
            'reminderActivities' => $reminderActivities,
            'availability' => $availability,
            'availabilityDays' => self::AVAILABILITY_DAYS,
            'availabilitySlots' => self::AVAILABILITY_SLOTS,
            'scheduleGoal' => $this->normalizeGoal($profile?->goals ?? $assessment?->main_goal),
            'scheduleExperience' => $this->normalizeExperience($profile?->experience ?? $assessment?->experience_level),
            'scheduleDuration' => $this->suggestedDuration(
                $profile?->experience ?? $assessment?->experience_level,
                $assessment?->session_minutes
            ),
            'notificationPreferences' => $notificationPreferences,
            'previousMonth' => $currentMonth->copy()->subMonth()->format('Y-m'),
            'nextMonth' => $currentMonth->copy()->addMonth()->format('Y-m'),
        ]);
    }

    public function updateAvailability(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'availability' => ['sometimes', 'array:'.implode(',', array_keys(self::AVAILABILITY_DAYS))],
            'availability.*' => ['array'],
            'availability.*.*' => ['string', Rule::in(array_keys(self::AVAILABILITY_SLOTS))],
        ]);

        $availability = $this->normalizeAvailability($validated['availability'] ?? []);
        $request->user()->profile()->updateOrCreate([], ['availability' => $availability]);

        return back()->with('success', 'Disponibilidad guardada como preferencia. Las actividades pueden programarse en cualquier día y horario.');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $this->ensureNoScheduleConflict($data, $request);
        $duration = $this->durationInMinutes($data['start_time'], $data['end_time']);

        FamFitActivity::create([
            ...$data,
            'user_id' => $request->user()->id,
            'duration_minutes' => $duration,
            'points' => $data['status'] === 'completed' ? intdiv($duration, 30) * 10 : 0,
        ]);

        return back()->with('success', 'Actividad guardada en el calendario compartido.');
    }

    public function update(Request $request, FamFitActivity $activity): RedirectResponse
    {
        abort_unless((int) $activity->getAttribute('user_id') === (int) $request->user()->getKey(), 403);

        $data = $this->validatedData($request);
        $scheduleChanged = Carbon::parse((string) $activity->getRawOriginal('activity_date'))->toDateString() !== $data['activity_date']
            || substr((string) $activity->getRawOriginal('start_time'), 0, 5) !== $data['start_time']
            || substr((string) $activity->getRawOriginal('end_time'), 0, 5) !== $data['end_time'];

        if ($scheduleChanged) {
            $this->ensureNoScheduleConflict($data, $request, (int) $activity->getKey());
        }

        $duration = $this->durationInMinutes($data['start_time'], $data['end_time']);

        $activity->update([
            ...$data,
            'duration_minutes' => $duration,
            'points' => $data['status'] === 'completed' ? intdiv($duration, 30) * 10 : 0,
        ]);

        return back()->with('success', 'Actividad actualizada correctamente.');
    }

    public function destroy(Request $request, FamFitActivity $activity): RedirectResponse
    {
        abort_unless((int) $activity->getAttribute('user_id') === (int) $request->user()->getKey(), 403);

        $activity->delete();

        return back()->with('success', 'Actividad eliminada.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:100'],
            'activity_type' => ['required', Rule::in(['Ejercicio', 'Caminata', 'Ciclismo', 'Deporte', 'Yoga', 'Otro'])],
            'activity_date' => ['required', 'date'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'reminder_minutes_before' => ['nullable', 'integer', Rule::in(['0', '5', '10', '15', '30', '60', '1440', '2880', '10080'])],
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['planned', 'completed'])],
        ]);
    }

    private function durationInMinutes(string $start, string $end): int
    {
        return (int) Carbon::createFromFormat('H:i', $start)
            ->diffInMinutes(Carbon::createFromFormat('H:i', $end));
    }

    private function normalizeGoal(?string $goal): string
    {
        return match ($goal) {
            'ganar_musculo', 'strength' => 'strength',
            'perder_grasa', 'resistencia_cardio', 'resistencia', 'endurance' => 'endurance',
            'flexibilidad_movilidad', 'flexibilidad', 'mobility' => 'mobility',
            'activo_saludable', 'healthy_habits', 'general_fitness' => 'general_fitness',
            'cardio' => 'cardio',
            default => 'general_fitness',
        };
    }

    private function normalizeExperience(?string $experience): string
    {
        return match ($experience) {
            'principiante', 'beginner', 'Inicial', 'unsure' => 'beginner',
            'intermedio', 'intermediate', 'Intermedio' => 'intermediate',
            'avanzado', 'advanced' => 'advanced',
            default => 'beginner',
        };
    }

    private function suggestedDuration(?string $experience, ?int $sessionMinutes): int
    {
        if ($sessionMinutes !== null && $sessionMinutes >= 15) {
            return min(120, max(15, (int) round($sessionMinutes / 15) * 15));
        }

        return match ($this->normalizeExperience($experience)) {
            'advanced' => 60,
            'intermediate' => 45,
            default => 30,
        };
    }

    private function ensureNoScheduleConflict(array $data, Request $request, ?int $exceptActivityId = null): void
    {
        $conflict = FamFitActivity::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('activity_date', $data['activity_date'])
            ->where('start_time', '<', $data['end_time'])
            ->where('end_time', '>', $data['start_time'])
            ->when($exceptActivityId !== null, fn ($query) => $query->where('id', '!=', $exceptActivityId))
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'start_time' => 'Ya tienes otra actividad en ese horario. Cambia la hora antes de guardar.',
            ]);
        }
    }

    private function normalizeAvailability(mixed $availability): array
    {
        if (\is_string($availability)) {
            $availability = json_decode($availability, true);
        }

        $normalized = [];

        if (! \is_array($availability)) {
            $availability = [];
        }

        foreach (array_keys(self::AVAILABILITY_DAYS) as $day) {
            $selectedSlots = $availability[$day] ?? [];
            $selectedSlots = \is_array($selectedSlots) ? $selectedSlots : [$selectedSlots];

            $normalized[$day] = array_values(array_unique(array_filter(
                $selectedSlots,
                fn ($slot) => \is_string($slot) && isset(self::AVAILABILITY_SLOTS[$slot])
            )));
        }

        return $normalized;
    }
}
