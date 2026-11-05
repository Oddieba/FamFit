@extends('layouts.quest')

@section('title', 'Calendario | FamFit Quest')

@section('content')
    <header class="fq-page-heading">
        <div><p class="fq-eyebrow">PLANIFICA TU SEMANA</p><h1>Calendario</h1><p class="fq-muted">Tus entrenamientos y ejercicios completados, en un solo lugar.</p></div>
    </header>
    <section class="fq-card fq-day-card">
        <div><p class="fq-eyebrow">DÍA SELECCIONADO</p><h2>{{ $selectedDate->locale('es')->translatedFormat('l, j \d\e F Y') }}</h2></div>
        <form class="fq-date-form" action="{{ route('calendario') }}" method="GET">
            <label class="fq-field-label" for="calendarDate">Ir a una fecha</label><input class="fq-input" id="calendarDate" name="date" type="date" value="{{ $selectedDate->toDateString() }}" required>
            <button class="fq-button fq-button-primary" type="submit">Ver fecha</button>
        </form>
    </section>
    <section class="fq-card fq-month-card" aria-label="Calendario mensual">
        <div class="fq-card-heading">
            <div><p class="fq-eyebrow">VISTA MENSUAL</p><h2>{{ $monthLabel }}</h2></div>
            <div class="fq-month-nav">
                <a class="fq-button fq-button-light" href="{{ route('calendario', ['date' => $selectedDate->copy()->subMonthNoOverflow()->startOfMonth()->toDateString()]) }}" aria-label="Mes anterior">←</a>
                <a class="fq-button fq-button-light" href="{{ route('calendario', ['date' => $selectedDate->copy()->addMonthNoOverflow()->startOfMonth()->toDateString()]) }}" aria-label="Mes siguiente">→</a>
            </div>
        </div>
        <div class="fq-calendar-grid fq-calendar-weekdays" aria-hidden="true">
            @foreach(['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'] as $weekday)<span>{{ $weekday }}</span>@endforeach
        </div>
        <div class="fq-calendar-grid">
            @foreach($calendarDays as $calendarDay)
                @php
                    $calendarDate = $calendarDay->toDateString();
                    $hasWorkout = $routine?->exercises->contains('day_of_week', $calendarDay->dayOfWeekIso) ?? false;
                    $hasCompletion = in_array($calendarDate, $completedDates, true);
                @endphp
                <a class="fq-calendar-day {{ $calendarDay->month !== $selectedDate->month ? 'is-outside' : '' }} {{ $calendarDate === $selectedDate->toDateString() ? 'is-selected' : '' }} {{ $calendarDate === today()->toDateString() ? 'is-today' : '' }}"
                   href="{{ route('calendario', ['date' => $calendarDate]) }}"
                   aria-label="{{ $calendarDay->locale('es')->translatedFormat('l j \d\e F') }}{{ $hasWorkout ? ', entrenamiento programado' : '' }}{{ $hasCompletion ? ', actividad completada' : '' }}">
                    <span>{{ $calendarDay->day }}</span>
                    <span class="fq-calendar-markers" aria-hidden="true">@if($hasWorkout)<i class="is-workout"></i>@endif @if($hasCompletion)<i class="is-completed"></i>@endif</span>
                </a>
            @endforeach
        </div>
        <div class="fq-calendar-legend"><span><i class="is-workout"></i> Entrenamiento</span><span><i class="is-completed"></i> Día con actividad completada</span></div>
    </section>
    <section class="fq-card fq-calendar-list">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">ACTIVIDAD PROGRAMADA</p><h2>{{ $routine?->name ?? 'Entrenamiento' }}</h2></div><a class="fq-text-link" href="{{ route('entreno', ['date' => $selectedDate->toDateString()]) }}">Ir a Entreno →</a></div>
        @if(!$routine)
            <div class="fq-empty-inline"><span aria-hidden="true">🏋️</span><p class="fq-muted">Todavía no tienes rutinas. Crea una para verla aquí.</p><a class="fq-text-link" href="{{ route('entreno') }}">Crear rutina →</a></div>
        @elseif($exercises->isEmpty())
            <div class="fq-empty-inline"><span aria-hidden="true">🌿</span><p class="fq-muted">No hay entrenamiento programado para este día.</p></div>
        @else
            <ul class="fq-calendar-exercises">
                @foreach($exercises as $exercise)
                    <li><span class="fq-calendar-dot {{ in_array($exercise->id, $completedIds) ? 'is-done' : '' }}" aria-hidden="true">{{ in_array($exercise->id, $completedIds) ? '✓' : '' }}</span><div><strong>{{ $exercise->name }}</strong><small>{{ $exercise->sets }} series × {{ $exercise->reps }} repeticiones · descanso {{ $exercise->rest_seconds }} s</small></div><span class="fq-calendar-status">{{ in_array($exercise->id, $completedIds) ? 'Completado' : 'Programado' }}</span></li>
                @endforeach
            </ul>
        @endif
    </section>
    <section class="fq-card fq-empty-state fq-calendar-note">
        <span class="fq-empty-icon" aria-hidden="true">✨</span><h2>Retos y hábitos</h2><p class="fq-muted">Aún no hay retos ni hábitos programables en el proyecto. El calendario muestra la rutina y el progreso realmente guardados en tu cuenta.</p>
    </section>
@endsection
