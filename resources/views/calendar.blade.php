@extends('layouts.quest')

@section('title', 'Calendario | FamFit Quest')

@push('styles')
    <link rel="stylesheet"
        href="{{ asset('estilos/famfit-calendar.css') }}?v={{ filemtime(public_path('estilos/famfit-calendar.css')) }}">
@endpush

@push('scripts')
    <script src="{{ asset('js/famfit-calendar.js') }}?v={{ filemtime(public_path('js/famfit-calendar.js')) }}" defer>
    </script>
@endpush

@section('content')
    <div class="ff-calendar-content">
        <header class="ff-topbar">
            <div><span class="ff-eyebrow">FAMFIT / ORGANIZACIÓN</span>
                <h1>Calendario inteligente</h1>
            </div>
            <button class="ff-btn ff-btn-primary" type="button" data-open-create>＋ Nueva actividad</button>
        </header>

        @if (session('success'))
            <div class="ff-alert ff-alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="ff-alert ff-alert-danger" role="alert">
                <strong>No se pudo guardar:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="ff-stats" aria-label="Resumen de esta semana">
            <article class="ff-stat-card"><span class="ff-stat-icon blue">◷</span>
                <div><small>Horas esta semana</small><strong>{{ number_format($weekMinutes / 60, 2) }} h</strong></div>
            </article>
            <article class="ff-stat-card"><span class="ff-stat-icon teal">▦</span>
                <div><small>Actividades</small><strong>{{ $weekCount }}</strong></div>
            </article>
            <article class="ff-stat-card"><span class="ff-stat-icon green">✓</span>
                <div><small>Completadas</small><strong>{{ $completedCount }}</strong></div>
            </article>
            <article class="ff-stat-card"><span class="ff-stat-icon amber">★</span>
                <div><small>Puntos ganados</small><strong>{{ $weekPoints }}</strong></div>
            </article>
        </section>

        <section class="ff-calendar-panel">
            <div class="ff-calendar-toolbar">
                <div>
                    <span class="ff-eyebrow">VISTA SEMANAL</span>
                    <h2>{{ $weekStart->locale('es')->translatedFormat('j M') }} –
                        {{ $weekEnd->locale('es')->translatedFormat('j M Y') }}</h2>
                    <p>Usa el botón “Nueva actividad” para agregar una actividad al calendario.</p>
                </div>
                <div class="ff-calendar-toolbar-actions">
                    <nav class="ff-calendar-actions" aria-label="Navegación del calendario">
                        <a class="ff-icon-btn"
                            href="{{ route('famfit.calendar', ['date' => $selectedDate->copy()->subYear()->toDateString()]) }}"
                            aria-label="Año anterior" title="Año anterior">«</a>
                        <a class="ff-icon-btn"
                            href="{{ route('famfit.calendar', ['date' => $currentMonth->copy()->subMonth()->toDateString()]) }}"
                            aria-label="Mes anterior" title="Mes anterior">‹</a>
                        <a class="ff-today-btn"
                            href="{{ route('famfit.calendar', ['date' => now()->toDateString()]) }}">Hoy</a>
                        <a class="ff-icon-btn"
                            href="{{ route('famfit.calendar', ['date' => $currentMonth->copy()->addMonth()->toDateString()]) }}"
                            aria-label="Mes siguiente" title="Mes siguiente">›</a>
                        <a class="ff-icon-btn"
                            href="{{ route('famfit.calendar', ['date' => $selectedDate->copy()->addYear()->toDateString()]) }}"
                            aria-label="Año siguiente" title="Año siguiente">»</a>
                    </nav>
                    <div class="ff-zoom-controls" role="group" aria-label="Tamaño del calendario">
                        <button class="ff-zoom-button" type="button" data-calendar-zoom="out"
                            aria-label="Reducir calendario" title="Reducir">−</button>
                        <output class="ff-zoom-level" id="calendarZoomLevel" aria-live="polite">100%</output>
                        <button class="ff-zoom-button" type="button" data-calendar-zoom="in"
                            aria-label="Agrandar calendario" title="Agrandar">＋</button>
                        <button class="ff-zoom-reset" type="button" data-calendar-zoom-reset>Restablecer</button>
                    </div>
                </div>
            </div>
            <details class="ff-availability-editor">
                <summary>Disponibilidad semanal <span>Elige días y franjas horarias</span></summary>
                <form method="POST" action="{{ route('famfit.availability.update') }}" class="ff-availability-form">
                    @csrf
                    @method('PUT')
                    <div class="ff-availability-days">
                        @foreach ($availabilityDays as $dayKey => $dayLabel)
                            <fieldset class="ff-availability-day">
                                <legend>{{ $dayLabel }}</legend>
                                @foreach ($availabilitySlots as $slotKey => $slot)
                                    <label class="ff-availability-option">
                                        <input type="checkbox" name="availability[{{ $dayKey }}][]"
                                            value="{{ $slotKey }}" @checked(in_array($slotKey, $availability[$dayKey], true))>
                                        <span>{{ $slot['label'] }}</span>
                                    </label>
                                @endforeach
                            </fieldset>
                        @endforeach
                    </div>
                    <div class="ff-availability-footer">
                        <p>Guarda tus preferencias semanales. Las actividades pueden programarse en cualquier día y horario.</p>
                        <button class="ff-btn ff-btn-primary" type="submit">Guardar disponibilidad</button>
                    </div>
                </form>
            </details>
            <div class="ff-planner-controls">
                <div class="ff-day-picker-panel">
                    <div class="ff-mini-heading">
                        <strong>{{ $currentMonth->locale('es')->translatedFormat('F Y') }}</strong>
                        <span>Elige un día</span>
                    </div>
                    <div class="ff-day-picker" aria-label="Selector de día">
                        @foreach (['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $weekday)
                            <span class="ff-mini-weekday">{{ $weekday }}</span>
                        @endforeach
                        @php
                            $miniDay = $currentMonth->copy()->startOfMonth()->startOfWeek(1);
                            $miniEnd = $currentMonth->copy()->endOfMonth()->endOfWeek(0);
                        @endphp
                        @while ($miniDay->lte($miniEnd))
                            <a class="ff-mini-day {{ $miniDay->month !== $currentMonth->month ? 'is-outside' : '' }} {{ $miniDay->isToday() ? 'is-today' : '' }} {{ $miniDay->isSameDay($selectedDate) ? 'is-selected' : '' }}"
                                href="{{ route('famfit.calendar', ['date' => $miniDay->toDateString()]) }}"
                                aria-label="{{ $miniDay->locale('es')->translatedFormat('j \d\e F Y') }}"
                                @if ($miniDay->isSameDay($selectedDate)) aria-current="date" @endif>
                                {{ $miniDay->day }}
                                @if ($miniActivityDates->has($miniDay->toDateString()))
                                    <i aria-hidden="true"></i>
                                @endif
                            </a>
                            @php $miniDay->addDay(); @endphp
                        @endwhile
                    </div>
                </div>
                <div class="ff-week-planner">
                    <div class="ff-week-schedule">
                        <div class="ff-time-column">
                            <div class="ff-time-header" aria-hidden="true"></div>
                            <div class="ff-time-ruler">
                                @for ($hour = 0; $hour < 24; $hour++)
                                    <span class="ff-time-label"
                                        style="top: {{ (($hour + 0.5) / 24) * 100 }}%">{{ sprintf('%02d:00', $hour) }}</span>
                                @endfor
                            </div>
                        </div>
                        <div class="ff-week-frame">
                            <div class="ff-week-inner">
                                <div class="ff-week-header">
                                    @for ($index = 0; $index < 7; $index++)
                                        @php $day = $weekStart->copy()->addDays($index); @endphp
                                        <div
                                            class="ff-week-heading {{ $day->isToday() ? 'is-today' : '' }} {{ $day->isSameDay($selectedDate) ? 'is-selected' : '' }}">
                                            <span>{{ $day->locale('es')->translatedFormat('D') }}</span>
                                            <strong>{{ $day->day }}</strong>
                                        </div>
                                    @endfor
                                </div>
                                <div class="ff-week-grid" data-today="{{ now()->toDateString() }}">
                                    @for ($index = 0; $index < 7; $index++)
                                        @php
                                            $day = $weekStart->copy()->addDays($index);
                                            $dayActivities = $activitiesByDate->get($day->toDateString(), collect());
                                        @endphp
                                        <div class="ff-day-column {{ $day->isToday() ? 'is-today' : '' }}" data-day-column
                                            data-date="{{ $day->toDateString() }}"
                                            aria-label="Agregar actividad el {{ $day->locale('es')->translatedFormat('j \d\e F') }}">
                                            @foreach ($dayActivities as $activity)
                                                @php
                                                    $start = \Illuminate\Support\Carbon::parse($activity->start_time);
                                                    $end = \Illuminate\Support\Carbon::parse($activity->end_time);
                                                    $top = $start->hour * 60 + $start->minute;
                                                    $duration = max(
                                                        15,
                                                        $top < $end->hour * 60 + $end->minute
                                                            ? $end->hour * 60 + $end->minute - $top
                                                            : 15,
                                                    );
                                                @endphp
                                                <button type="button"
                                                    class="ff-event event-{{ strtolower($activity->activity_type) }} {{ $activity->status === 'completed' ? 'is-completed' : '' }}"
                                                    style="top: {{ ($top / 1440) * 100 }}%; height: {{ ($duration / 1440) * 100 }}%"
                                                    title="{{ $activity->title }} · {{ substr($activity->start_time, 0, 5) }}–{{ substr($activity->end_time, 0, 5) }}"
                                                    data-activity='@json($activity)'
                                                    data-update-url="{{ route('famfit.activities.update', $activity) }}"
                                                    data-delete-url="{{ route('famfit.activities.destroy', $activity) }}">
                                                    <strong>{{ $activity->title }}</strong>
                                                    <span>{{ substr($activity->start_time, 0, 5) }} –
                                                        {{ substr($activity->end_time, 0, 5) }}</span>
                                                </button>
                                            @endforeach
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="ff-week-navigation">
                        <a class="ff-btn ff-btn-ghost"
                            href="{{ route('famfit.calendar', ['date' => $selectedDate->copy()->subWeek()->toDateString()]) }}">‹
                            Semana anterior</a>
                        <span>{{ $weekStart->locale('es')->translatedFormat('j M') }} –
                            {{ $weekEnd->locale('es')->translatedFormat('j M') }}</span>
                        <a class="ff-btn ff-btn-ghost"
                            href="{{ route('famfit.calendar', ['date' => $selectedDate->copy()->addWeek()->toDateString()]) }}">Semana
                            siguiente ›</a>
                    </div>
                </div>
            </div>
            <div class="ff-legend">
                <span><i class="legend-teal"></i> Hábitos y movimiento</span>
                <span><i class="legend-blue"></i> Actividad programada</span>
                <span><i class="legend-green"></i> Actividad completada</span>
            </div>
        </section>
        <footer class="ff-footer">FamFit · Cada pequeño hábito cuenta.</footer>
    </div>
    <div class="ff-modal-backdrop" id="activityModal" aria-hidden="true">
        <section class="ff-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
            <div class="ff-modal-head">
                <div><span class="ff-eyebrow">PLANIFICACIÓN FAMFIT</span>
                    <h2 id="modalTitle">Nueva actividad</h2>
                </div>
                <button type="button" class="ff-close" data-close-modal aria-label="Cerrar">×</button>
            </div>
            <form id="activityForm" method="POST" action="{{ route('famfit.activities.store') }}"
                data-store-url="{{ route('famfit.activities.store') }}" data-today="{{ now()->toDateString() }}"
                data-availability='@json($availability)' data-availability-slots='@json($availabilitySlots)'>
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">
                <div class="ff-form-grid">
                    <label class="ff-field ff-field-full">Nombre de la actividad
                        <input name="title" id="activityTitle" maxlength="100" placeholder="Ej. Caminata matutina"
                            required>
                    </label>
                    <label class="ff-field">Tipo de actividad
                        <select name="activity_type" id="activityType" required>
                            <option>Ejercicio</option>
                            <option>Caminata</option>
                            <option>Ciclismo</option>
                            <option>Deporte</option>
                            <option>Yoga</option>
                            <option>Otro</option>
                        </select>
                    </label>
                    <label class="ff-field">Fecha
                        <input type="date" name="activity_date" id="activityDate" required>
                    </label>
                    <label class="ff-field">Hora de inicio
                        <input type="time" name="start_time" id="startTime" required>
                    </label>
                    <label class="ff-field">Hora de finalización
                        <input type="time" name="end_time" id="endTime" required>
                    </label>
                    <label class="ff-field ff-field-full">Estado
                        <select name="status" id="activityStatus">
                            <option value="planned">Programada</option>
                            <option value="completed">Completada</option>
                        </select>
                    </label>
                    <label class="ff-field ff-field-full">Notas (opcional)
                        <textarea name="notes" id="activityNotes" rows="3" maxlength="1000"
                            placeholder="Agrega detalles de tu actividad..."></textarea>
                    </label>
                </div>
                <p class="ff-availability-feedback" id="availabilityFeedback" aria-live="polite"></p>
                <div class="ff-modal-actions">
                    <button type="button" class="ff-btn ff-btn-ghost" data-close-modal>Cancelar</button>
                    <button type="submit" class="ff-btn ff-btn-primary" id="saveActivityBtn">Guardar actividad</button>
                </div>
            </form>
            <form id="deleteForm" method="POST" class="ff-delete-form" hidden>
                @csrf
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" class="ff-delete-link"
                    onclick="return confirm('¿Seguro que deseas eliminar esta actividad?')">Eliminar actividad</button>
            </form>
        </section>
    </div>
@endsection
