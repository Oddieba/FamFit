@extends('layouts.quest')

@section('title', 'Perfil | FamFit Quest')

@push('styles')
    <link rel="stylesheet" href="{{ asset('estilos/perfil.css') }}">
@endpush

@section('content')
    @php
        $answers = $assessment?->answers ?? [];
        $testAge = $profile?->age ?? ($answers['edad'] ?? '');
        $testWeight = $profile?->weight ?? ($answers['peso'] ?? '');
        $testHeight = $profile?->height ?? ($answers['altura'] ?? '');
        $testGender = $profile?->gender ?? ($answers['genero'] ?? '');
        $testGoal = $profile?->goals ?? ($answers['objetivo'] ?? '');
        $testExperience = $profile?->experience ?? ($answers['experiencia_ejercicio'] ?? '');
        $testAvailability = $profile?->availability ?? ($answers['disponibilidad'] ?? []);
        $testInjury = $answers['tiene_lesion'] ?? ($profile?->has_injury ? 'si' : 'no');
        $testInjuryDescription = $profile?->injury_description ?? ($answers['descripcion_lesion'] ?? '');
        $dayLabels = [
            'lunes' => 'Lunes',
            'martes' => 'Martes',
            'miercoles' => 'Miércoles',
            'jueves' => 'Jueves',
            'viernes' => 'Viernes',
            'sabado' => 'Sábado',
            'domingo' => 'Domingo',
        ];
        $slotLabels = ['manana' => 'Mañana', 'mañana' => 'Mañana', 'tarde' => 'Tarde', 'noche' => 'Noche'];
        $slotClasses = ['manana' => 'morning', 'mañana' => 'morning', 'tarde' => 'afternoon', 'noche' => 'night'];
        $goalLabels = [
            'perder_grasa' => 'Perder grasa / Bajar de peso',
            'ganar_musculo' => 'Ganar masa muscular / Fuerza',
            'resistencia_cardio' => 'Mejorar resistencia / Cardio',
            'activo_saludable' => 'Mantenerme activo y saludable',
            'flexibilidad_movilidad' => 'Flexibilidad y movilidad',
        ];
        $experienceLabels = ['principiante' => 'Principiante', 'intermedio' => 'Intermedio', 'avanzado' => 'Avanzado'];
        $genderLabels = ['masculino' => 'Masculino', 'femenino' => 'Femenino', 'otro' => 'Otro'];
        $injuryLabels = ['si' => 'Sí', 'no' => 'No', '1' => 'Sí', '0' => 'No'];
        $weekDays = ['L', 'M', 'X', 'J', 'V', 'S', 'D'];
    @endphp

    <h1 class="fq-profile-sr-only">Perfil</h1>
    <section class="fq-profile-hero" aria-label="Perfil de {{ $user->name }}">
        <div class="fq-profile-banner">
            <div class="fq-profile-banner-copy">
                <span class="fq-profile-banner-mark" aria-hidden="true">🏋️</span>
                <div>
                    <p>Tu espacio de aventura</p>
                    <h2>FamFit Quest</h2>
                </div>
            </div>
            <div class="fq-profile-sports-art" aria-hidden="true">
                <span>🏀</span><span>⚽</span><span>🏃</span>
            </div>
        </div>

        <div class="fq-profile-main">
            <div class="fq-profile-identity">
                <div class="fq-avatar-art fq-profile-avatar" style="--fq-outfit: {{ $avatar['outfit'] }}">
                    <span class="fq-avatar-character">{{ $avatar['animal'] }}</span>
                    @if($avatar['accessory_symbol'])
                        <span class="fq-avatar-accessory">{{ $avatar['accessory_symbol'] }}</span>
                    @endif
                </div>
                <div class="fq-profile-name">
                    <span class="fq-profile-level">Nivel {{ $level }}</span>
                    <h2>{{ $user->name }}</h2>
                    <p>{{ $user->username ? '@'.$user->username : $user->email }}</p>
                    <a class="fq-profile-customize-link" href="{{ route('menu') }}">Personalizar avatar <span aria-hidden="true">→</span></a>
                </div>
            </div>

            <div class="fq-profile-social-stats" aria-label="Estadísticas sociales">
                <div class="fq-profile-social-stat">
                    <strong>{{ $missionsCount ?? '—' }}</strong>
                    <span>Misiones</span>
                </div>
                <div class="fq-profile-social-stat">
                    <strong aria-label="No disponible">—</strong>
                    <span>Seguidores</span>
                    <small>No disponible</small>
                </div>
                <div class="fq-profile-social-stat">
                    <strong>{{ $friendsCount ?? '—' }}</strong>
                    <span>Amigos</span>
                </div>
            </div>

            <button class="fq-button fq-button-primary fq-profile-edit-button" type="button" id="profileEditToggle" aria-controls="profileEditPanel" aria-expanded="false">
                <i class="fa-solid fa-pen" aria-hidden="true"></i> Editar perfil
            </button>
        </div>

        <div class="fq-profile-bio">
            <div class="fq-profile-section-heading">
                <span class="fq-profile-section-icon" aria-hidden="true"><i class="fa-regular fa-user"></i></span>
                <h3>Biografía</h3>
            </div>
            <p>{{ $profile?->bio ?: 'Todavía no has añadido una biografía. Cuéntales a tus amigos qué te motiva a entrenar.' }}</p>
        </div>
    </section>

    <section class="fq-profile-dashboard" aria-label="Resumen de actividad">
        <article class="fq-card fq-profile-panel">
            <div class="fq-profile-panel-heading">
                <span class="fq-profile-panel-icon fq-profile-icon-blue" aria-hidden="true"><i class="fa-solid fa-chart-line"></i></span>
                <div><p class="fq-eyebrow">TU ACTIVIDAD</p><h2>Métricas de la semana</h2></div>
            </div>
            <div class="fq-profile-metric-list">
                <div class="fq-profile-metric">
                    <span class="fq-profile-metric-icon" aria-hidden="true"><i class="fa-solid fa-shoe-prints"></i></span>
                    <div><span>Pasos</span><small>Sin registro disponible</small></div>
                    <strong aria-label="Sin datos">—</strong>
                </div>
                <div class="fq-profile-metric">
                    <span class="fq-profile-metric-icon fq-profile-icon-orange" aria-hidden="true"><i class="fa-solid fa-fire"></i></span>
                    <div><span>Calorías</span><small>Sin registro disponible</small></div>
                    <strong aria-label="Sin datos">—</strong>
                </div>
                <div class="fq-profile-metric">
                    <span class="fq-profile-metric-icon fq-profile-icon-green" aria-hidden="true"><i class="fa-solid fa-dumbbell"></i></span>
                    <div><span>Entrenamientos</span><small>Días con ejercicios registrados</small></div>
                    <strong>{{ $weeklyWorkouts ?? '—' }}</strong>
                </div>
            </div>
            <p class="fq-profile-routine">{{ $routine ? 'Rutina: '.$routine->name : 'Aún no tienes una rutina' }}</p>
        </article>

        <article class="fq-card fq-profile-panel">
            <div class="fq-profile-panel-heading">
                <span class="fq-profile-panel-icon fq-profile-icon-purple" aria-hidden="true"><i class="fa-solid fa-medal"></i></span>
                <div><p class="fq-eyebrow">SIGUE AVANZANDO</p><h2>Estadísticas de aventurero</h2></div>
            </div>
            <div class="fq-profile-adventurer">
                <div class="fq-profile-level-row"><span>Nivel {{ $level }}</span><strong>{{ $user->xp ?? 0 }} XP</strong></div>
                <div class="fq-progress-track" role="progressbar" aria-label="Progreso de nivel" aria-valuenow="{{ $xpProgress }}" aria-valuemin="0" aria-valuemax="100">
                    <span style="width: {{ $xpProgress }}%"></span>
                </div>
                <small class="fq-profile-muted">{{ $xpProgress }} / 100 XP para el siguiente nivel</small>
                <div class="fq-profile-adventurer-stats">
                    <div><span class="fq-profile-small-icon" aria-hidden="true">🔥</span><span>Racha de entrenamientos</span><strong>{{ $currentStreak ?? '—' }}{{ $currentStreak !== null ? ' días' : '' }}</strong></div>
                    <div><span class="fq-profile-small-icon" aria-hidden="true">🏅</span><span>Logros desbloqueados</span><strong>{{ $achievementsCount ?? '—' }}</strong></div>
                    <div><span class="fq-profile-small-icon" aria-hidden="true">✓</span><span>Ejercicios completados</span><strong>{{ $completedCount }}</strong></div>
                </div>
            </div>
        </article>

        <article class="fq-card fq-profile-panel fq-profile-calendar-panel">
            <div class="fq-profile-panel-heading">
                <span class="fq-profile-panel-icon fq-profile-icon-teal" aria-hidden="true"><i class="fa-regular fa-calendar-check"></i></span>
                <div><p class="fq-eyebrow">TU HISTORIAL</p><h2>Calendario de misiones</h2></div>
            </div>
            <p class="fq-profile-month">{{ ucfirst($monthLabel) }}</p>
            <div class="fq-profile-calendar" role="grid" aria-label="Misiones completadas en {{ $monthLabel }}">
                @foreach($weekDays as $weekDay)
                    <span class="fq-profile-calendar-weekday" role="columnheader">{{ $weekDay }}</span>
                @endforeach
                @for($emptyDay = 0; $emptyDay < $monthOffset; $emptyDay++)
                    <span class="fq-profile-calendar-empty" aria-hidden="true"></span>
                @endfor
                @foreach($calendarDays as $calendarDay)
                    <span class="fq-profile-calendar-day {{ $calendarDay['completed'] ? 'is-completed' : '' }}" role="gridcell" aria-label="{{ $calendarDay['day'] }}{{ $calendarDay['completed'] ? ', misión completada' : '' }}">
                        {{ $calendarDay['day'] }}
                        @if($calendarDay['completed'])<i aria-hidden="true"></i>@endif
                    </span>
                @endforeach
            </div>
            <p class="fq-profile-calendar-legend"><span aria-hidden="true"></span> Día con misión completada</p>
        </article>
    </section>

    <section class="fq-card fq-profile-form-card" id="profileEditPanel" hidden>
        <div class="fq-card-heading">
            <div><p class="fq-eyebrow">INFORMACIÓN PERSONAL</p><h2>Datos de la cuenta</h2></div>
            <span class="fq-card-icon" aria-hidden="true">👤</span>
        </div>
        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="fq-form-grid">
                <div><label class="fq-field-label" for="profileName">Nombre</label><input class="fq-input" id="profileName" name="name" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required></div>
                <div><label class="fq-field-label" for="profileUsername">Nombre de usuario</label><input class="fq-input" id="profileUsername" name="username" value="{{ old('username', $user->username) }}" minlength="3" maxlength="20" pattern="[A-Za-z0-9_]+" autocomplete="username" required></div>
                <div class="{{ $bioAvailable ? '' : 'fq-form-wide' }}"><label class="fq-field-label" for="profileEmail">Correo electrónico</label><input class="fq-input" id="profileEmail" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="190" autocomplete="email" required></div>
                @if($bioAvailable)
                    <div class="fq-form-wide"><label class="fq-field-label" for="profileBio">Biografía</label><textarea class="fq-input fq-profile-bio-input" id="profileBio" name="bio" maxlength="300" rows="3">{{ old('bio', $profile?->bio) }}</textarea></div>
                @endif
            </div>
            <div class="fq-profile-form-actions">
                <button class="fq-button fq-button-primary" type="submit">Guardar perfil</button>
                <button class="fq-button fq-button-light" type="button" id="retakeTestToggle">Repetir test</button>
            </div>
        </form>

        <div class="fq-profile-additional">
            <div class="fq-card-heading"><div><p class="fq-eyebrow">INFORMACIÓN ADICIONAL</p></div></div>
            <div class="fq-form-grid">
                <div><label class="fq-field-label" for="testAge">Edad (años)</label><output class="fq-input fq-test-value" id="testAge" aria-readonly="true">{{ $testAge ?: '—' }}</output></div>
                <div><label class="fq-field-label" for="testWeight">Peso (kg)</label><output class="fq-input fq-test-value" id="testWeight" aria-readonly="true">{{ $testWeight ?: '—' }}</output></div>
                <div><label class="fq-field-label" for="testHeight">Altura (cm)</label><output class="fq-input fq-test-value" id="testHeight" aria-readonly="true">{{ $testHeight ?: '—' }}</output></div>
                <div><label class="fq-field-label" for="testGender">Género</label><output class="fq-input fq-test-value" id="testGender" aria-readonly="true">{{ $genderLabels[$testGender] ?? ($testGender ?: '—') }}</output></div>
                <div><label class="fq-field-label" for="testGoal">Objetivo principal</label><output class="fq-input fq-test-value" id="testGoal" aria-readonly="true">{{ $goalLabels[$testGoal] ?? ($testGoal ?: '—') }}</output></div>
                <div><label class="fq-field-label" for="testExperience">Experiencia</label><output class="fq-input fq-test-value" id="testExperience" aria-readonly="true">{{ $experienceLabels[$testExperience] ?? ($testExperience ?: '—') }}</output></div>
                <div class="fq-form-wide">
                    <span class="fq-field-label" id="testAvailabilityLabel">Disponibilidad semanal</span>
                    <div class="fq-week-chart" role="list" aria-labelledby="testAvailabilityLabel">
                        @foreach($dayLabels as $day => $dayLabel)
                            @php
                                $selectedSlots = $testAvailability[$day] ?? [];
                                $selectedSlots = is_array($selectedSlots) ? $selectedSlots : ($selectedSlots ? [$selectedSlots] : []);
                                $selectedSlots = array_values(array_intersect($selectedSlots, array_keys($slotLabels)));
                            @endphp
                            <div class="fq-week-day {{ $selectedSlots ? '' : 'is-inactive' }}" role="listitem" aria-label="{{ $dayLabel }}: {{ $selectedSlots ? implode(', ', array_map(fn ($slot) => $slotLabels[$slot], $selectedSlots)) : 'sin disponibilidad' }}">
                                <strong class="fq-week-day-name">{{ $dayLabel }}</strong>
                                @if($selectedSlots)
                                    <div class="fq-week-slots">
                                        @foreach($selectedSlots as $slot)
                                            <span class="fq-week-slot fq-slot-{{ $slotClasses[$slot] }}">{{ $slotLabels[$slot] }}</span>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="fq-week-inactive">Sin disponibilidad</span>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                <div><label class="fq-field-label" for="testInjury">Lesión o condición médica</label><output class="fq-input fq-test-value" id="testInjury" aria-readonly="true">{{ $injuryLabels[(string) $testInjury] ?? ($testInjury ?: '—') }}</output></div>
                <div><label class="fq-field-label" for="testInjuryDescription">Detalle de la lesión o condición</label><output class="fq-input fq-test-value" id="testInjuryDescription" aria-readonly="true">{{ $testInjuryDescription ?: '—' }}</output></div>
            </div>
        </div>
    </section>

    <div class="fq-profile-modal" id="retakeModal" aria-hidden="true">
        <div class="fq-profile-modal-box" role="dialog" aria-modal="true" aria-labelledby="retakeModalTitle">
            <span class="fq-profile-modal-icon" aria-hidden="true"><i class="fa-solid fa-clipboard-question"></i></span>
            <h2 id="retakeModalTitle">¿Volver a tomar el test?</h2>
            <p>¿Seguro que quieres volver a tomar tus datos del test inicial? Esto actualizará tu perfil actual y tu historial.</p>
            <div class="fq-profile-modal-actions">
                <button class="fq-button fq-button-light" type="button" id="retakeModalCancel">Cancelar</button>
                <a class="fq-button fq-button-primary" href="{{ route('onboarding', ['retake' => 1]) }}">Sí, continuar</a>
            </div>
        </div>
    </div>

    @push('scripts')
        <script src="{{ asset('js/perfil.js') }}" defer></script>
    @endpush
@endsection
