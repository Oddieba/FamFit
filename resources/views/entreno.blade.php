@extends('layouts.quest')

@section('title', 'Entreno | FamFit Quest')

@section('content')
    <header class="fq-page-heading">
        <div><p class="fq-eyebrow">MUÉVETE A TU RITMO</p><h1>Entreno</h1><p class="fq-muted">Tu plan, tus tiempos. Marca cada ejercicio al completarlo.</p></div>
    </header>

    <section class="fq-card fq-day-card">
        <div>
            <p class="fq-eyebrow">PLAN DEL DÍA</p>
            <h2>{{ $selectedDate->locale('es')->translatedFormat('l, j \d\e F') }}</h2>
        </div>
        <form class="fq-date-form" action="{{ route('entreno') }}" method="GET">
            <label class="fq-field-label" for="trainingDate">Consultar otro día</label>
            <input class="fq-input" id="trainingDate" name="date" type="date" value="{{ $selectedDate->toDateString() }}" required>
            <button class="fq-button fq-button-light" type="submit">Ver día</button>
        </form>
    </section>

    @if(!$routineStorageAvailable)
        <section class="fq-card fq-empty-state">
            <span class="fq-empty-icon" aria-hidden="true">🏋️</span>
            <h2>Entreno no disponible todavía</h2>
            <p class="fq-muted">El almacenamiento de rutinas aún no está instalado en esta base de datos. Esta sección quedará vacía hasta que se agreguen sus tablas.</p>
        </section>
    @elseif(!$routine)
        <section class="fq-card fq-routine-create">
            <div class="fq-card-heading"><div><p class="fq-eyebrow">EMPIEZA CUANDO QUIERAS</p><h2>Crea tu primera rutina</h2></div><span class="fq-card-icon" aria-hidden="true">🌱</span></div>
            <p class="fq-muted">Elige ejercicios y programa los días, series, repeticiones y descansos. Esta rutina se configura con tus elecciones; no es una prescripción médica ni una recomendación automática.</p>
            <form action="{{ route('entreno.rutinas.store') }}" method="POST" id="fqRoutineForm">
                @csrf
                <div class="fq-form-grid">
                    <div><label class="fq-field-label" for="routineName">Nombre de la rutina</label><input class="fq-input" id="routineName" name="name" maxlength="80" value="{{ old('name') }}" placeholder="Ej. Mi rutina semanal" required></div>
                    <div><label class="fq-field-label" for="routineGoal">Objetivo</label><select class="fq-input" id="routineGoal" name="goal" required>@foreach($goalChoices as $choice)<option value="{{ $choice }}" @selected(old('goal') === $choice)>{{ $choice }}</option>@endforeach</select></div>
                    <div><label class="fq-field-label" for="routineExperience">Experiencia</label><select class="fq-input" id="routineExperience" name="experience" required>@foreach($experienceChoices as $choice)<option value="{{ $choice }}" @selected(old('experience') === $choice)>{{ $choice }}</option>@endforeach</select></div>
                </div>
                <div class="fq-exercise-builder">
                    <div class="fq-card-heading"><div><h3>Ejercicios</h3><p class="fq-muted">Añade entre 1 y 5 ejercicios.</p></div><button class="fq-button fq-button-light" type="button" data-add-exercise>+ Añadir ejercicio</button></div>
                    <div class="fq-exercise-fields" data-exercise-fields>
                        <div class="fq-exercise-editor">
                            <label class="fq-field-label">Ejercicio<select class="fq-input" name="exercises[0][name]" required><option value="">Selecciona un ejercicio</option>@foreach($exerciseChoices as $choice)<option value="{{ $choice }}" @selected(old('exercises.0.name') === $choice)>{{ $choice }}</option>@endforeach</select></label>
                            <label class="fq-field-label">Día<select class="fq-input" name="exercises[0][day_of_week]" required>@foreach($days as $number => $day)<option value="{{ $number }}" @selected((int) old('exercises.0.day_of_week', 1) === $number)>{{ $day }}</option>@endforeach</select></label>
                            <label class="fq-field-label">Series<input class="fq-input" name="exercises[0][sets]" type="number" min="1" max="10" value="{{ old('exercises.0.sets', 3) }}" required></label>
                            <label class="fq-field-label">Repeticiones<input class="fq-input" name="exercises[0][reps]" type="number" min="1" max="100" value="{{ old('exercises.0.reps', 12) }}" required></label>
                            <label class="fq-field-label">Descanso (seg.)<input class="fq-input" name="exercises[0][rest_seconds]" type="number" min="15" max="300" step="5" value="{{ old('exercises.0.rest_seconds', 60) }}" required></label>
                        </div>
                    </div>
                    <template id="fqExerciseTemplate">
                        <div class="fq-exercise-editor">
                            <label class="fq-field-label">Ejercicio<select class="fq-input" data-field="name" required><option value="">Selecciona un ejercicio</option>@foreach($exerciseChoices as $choice)<option value="{{ $choice }}">{{ $choice }}</option>@endforeach</select></label>
                            <label class="fq-field-label">Día<select class="fq-input" data-field="day_of_week" required>@foreach($days as $number => $day)<option value="{{ $number }}">{{ $day }}</option>@endforeach</select></label>
                            <label class="fq-field-label">Series<input class="fq-input" data-field="sets" type="number" min="1" max="10" value="3" required></label>
                            <label class="fq-field-label">Repeticiones<input class="fq-input" data-field="reps" type="number" min="1" max="100" value="12" required></label>
                            <label class="fq-field-label">Descanso (seg.)<input class="fq-input" data-field="rest_seconds" type="number" min="15" max="300" step="5" value="60" required></label>
                            <button class="fq-remove-exercise" type="button" data-remove-exercise aria-label="Quitar ejercicio">Quitar</button>
                        </div>
                    </template>
                </div>
                <button class="fq-button fq-button-primary" type="submit">Guardar rutina</button>
            </form>
        </section>
    @else
        <section class="fq-card fq-routine-heading">
            <div><p class="fq-eyebrow">TU RUTINA · {{ $routine->goal }}</p><h2>{{ $routine->name }}</h2><p class="fq-muted">Nivel de experiencia indicado: {{ $routine->experience }}</p></div>
            <span class="fq-level-pill">{{ $exercises->count() }} {{ $exercises->count() === 1 ? 'ejercicio' : 'ejercicios' }}</span>
        </section>
        @if($exercises->isEmpty())
            <section class="fq-card fq-empty-state"><span class="fq-empty-icon" aria-hidden="true">☁️</span><h2>Día de descanso</h2><p class="fq-muted">No programaste ejercicios para este día. Elige otra fecha para revisar tu semana.</p></section>
        @else
            @php($doneCount = $exercises->filter(fn ($exercise) => in_array($exercise->id, $completedIds))->count())
            <section class="fq-card fq-routine-progress">
                <div class="fq-xp-label"><strong>Progreso del día</strong><span>{{ $doneCount }} de {{ $exercises->count() }}</span></div>
                <div class="fq-progress-track" role="progressbar" aria-label="Progreso de la rutina" aria-valuenow="{{ (int) round($doneCount / $exercises->count() * 100) }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ (int) round($doneCount / $exercises->count() * 100) }}%"></span></div>
            </section>
            <div class="fq-exercise-list">
                @foreach($exercises as $exercise)
                    @php($isComplete = in_array($exercise->id, $completedIds))
                    <article class="fq-card fq-exercise-card {{ $isComplete ? 'is-complete' : '' }}">
                        <div class="fq-exercise-number">{{ $loop->iteration }}</div>
                        <div class="fq-exercise-info"><h3>{{ $exercise->name }}</h3><p class="fq-muted">{{ $exercise->sets }} series · {{ $exercise->reps }} repeticiones · {{ $exercise->rest_seconds }} seg. descanso</p></div>
                        @if($isComplete)
                            <span class="fq-complete-label">✓ Completado</span>
                        @elseif($selectedDate->isFuture())
                            <span class="fq-muted">Programado</span>
                        @else
                            <form action="{{ route('entreno.ejercicios.complete', $exercise) }}" method="POST">
                                @csrf
                                <input type="hidden" name="completed_on" value="{{ $selectedDate->toDateString() }}">
                                <button class="fq-button fq-button-primary" type="submit">Marcar hecho</button>
                            </form>
                        @endif
                    </article>
                @endforeach
            </div>
            <p class="fq-muted fq-xp-hint">Cada ejercicio que completes por primera vez ese día suma 10 XP.</p>
        @endif
    @endif
@endsection
