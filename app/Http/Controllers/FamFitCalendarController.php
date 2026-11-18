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
        $availability = $this->normalizeAvailability($request->user()->profile?->availability);

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
            'availability' => $availability,
            'availabilityDays' => self::AVAILABILITY_DAYS,
            'availabilitySlots' => self::AVAILABILITY_SLOTS,
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
        $this->ensureActivityWithinAvailability($data, $request);
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
        abort_unless($activity->user_id === $request->user()->id, 403);

        $data = $this->validatedData($request);
        $scheduleChanged = $activity->activity_date->toDateString() !== $data['activity_date']
            || substr((string) $activity->start_time, 0, 5) !== $data['start_time']
            || substr((string) $activity->end_time, 0, 5) !== $data['end_time'];

        if ($scheduleChanged) {
            $this->ensureActivityWithinAvailability($data, $request);
            $this->ensureNoScheduleConflict($data, $request, $activity->id);
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
        abort_unless($activity->user_id === $request->user()->id, 403);

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
            'notes' => ['nullable', 'string', 'max:1000'],
            'status' => ['required', Rule::in(['planned', 'completed'])],
        ]);
    }

    private function durationInMinutes(string $start, string $end): int
    {
        return (int) Carbon::createFromFormat('H:i', $start)
            ->diffInMinutes(Carbon::createFromFormat('H:i', $end));
    }

    private function ensureActivityWithinAvailability(array $data, Request $request): void
    {
        // Se permite programar actividades en cualquier horario.
        // La disponibilidad semanal se conserva como preferencia visible,
        // pero no bloquea la creación de actividades.
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
