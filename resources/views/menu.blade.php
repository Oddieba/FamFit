@extends('layouts.quest')

@section('title', 'Inicio | FamFit Quest')

@section('content')
    <header class="fq-page-heading">
        <div>
            <p class="fq-eyebrow">TU ESPACIO FAMFIT</p>
            <h1>¡Hola, {{ auth()->user()->name }}! <span aria-hidden="true">👋</span></h1>
            <p class="fq-muted">Pequeños hábitos, grandes logros. ¿Qué te gustaría hacer hoy?</p>
        </div>
        <a class="fq-button fq-button-light" href="{{ route('perfil') }}">Ver mi perfil <span aria-hidden="true">↗</span></a>
    </header>

    <section class="fq-home-grid" aria-label="Resumen y avatar">
        <article class="fq-card fq-avatar-card">
            <div class="fq-card-heading">
                <div><p class="fq-eyebrow">TU AVENTURA</p><h2>Tu avatar</h2></div>
                <span class="fq-level-pill">Nivel {{ $level }}</span>
            </div>
            <div class="fq-avatar-stage">
                <div class="fq-avatar-art" id="fqAvatarPreview" style="--fq-outfit: {{ $avatar['outfit'] }}">
                    <span class="fq-avatar-character" id="fqAnimalPreview" aria-label="Avatar {{ $avatar['animal'] }}">{{ $avatar['animal'] }}</span>
                    @if($avatar['accessory_symbol'])
                        <span class="fq-avatar-accessory" id="fqAccessoryPreview">{{ $avatar['accessory_symbol'] }}</span>
                    @else
                        <span class="fq-avatar-accessory" id="fqAccessoryPreview" hidden></span>
                    @endif
                </div>
                <p class="fq-muted">¡Este eres tú en FamFit Quest!</p>
            </div>

            <div class="fq-xp-block">
                <div class="fq-xp-label"><strong>Experiencia</strong><span>{{ auth()->user()->xp ?? 0 }} XP</span></div>
                <div class="fq-progress-track" role="progressbar" aria-label="Progreso al siguiente nivel" aria-valuenow="{{ $xpProgress }}" aria-valuemin="0" aria-valuemax="100">
                    <span style="width: {{ $xpProgress }}%"></span>
                </div>
                <small class="fq-muted">{{ 100 - $xpProgress }} XP para el nivel {{ $level + 1 }}</small>
            </div>

            @if($avatarStorageAvailable)
                <button class="fq-button fq-button-light fq-customize-toggle" type="button" data-toggle-panel="fqAvatarForm" aria-expanded="false">Personalizar avatar</button>
                <form class="fq-customize-form" id="fqAvatarForm" action="{{ route('avatar.update') }}" method="POST" hidden>
                @csrf
                @method('PUT')
                <fieldset>
                    <legend>Elige tu personaje</legend>
                    <div class="fq-choice-row">
                        @foreach(['🦊', '🐼', '🐸', '🐯', '🐨', '🐙', '🦁', '🐢'] as $animal)
                            <label class="fq-avatar-choice">
                                <input type="radio" name="animal" value="{{ $animal }}" @checked(old('animal', $avatar['animal']) === $animal)>
                                <span aria-hidden="true">{{ $animal }}</span>
                            </label>
                        @endforeach
                    </div>
                </fieldset>
                <label class="fq-field-label" for="fqOutfit">Color de ropa</label>
                <select class="fq-input" id="fqOutfit" name="outfit">
                    @foreach(['#2864F0' => 'Azul', '#A7E66D' => 'Verde', '#F3A847' => 'Naranja', '#B58BEA' => 'Lila', '#F28BA8' => 'Rosa'] as $color => $name)
                        <option value="{{ $color }}" @selected(old('outfit', $avatar['outfit']) === $color)>{{ $name }}</option>
                    @endforeach
                </select>
                <label class="fq-field-label" for="fqAccessory">Accesorio</label>
                <select class="fq-input" id="fqAccessory" name="accessory">
                    @foreach(['Ninguno' => 'Sin accesorio', 'Gafas' => 'Gafas 🕶️', 'Gorra' => 'Gorra 🧢', 'Estrella' => 'Estrella ⭐'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('accessory', $avatar['accessory']) === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="fq-muted fq-preview-note">La vista previa se actualiza al elegir; guarda para conservarla en tu cuenta.</p>
                <button class="fq-button fq-button-primary" type="submit">Guardar cambios</button>
                </form>
            @endif
        </article>

        <div class="fq-home-side">
            <article class="fq-card fq-workout-summary">
                <div class="fq-card-heading">
                    <div><p class="fq-eyebrow">HOY</p><h2>Entreno</h2></div>
                    <span class="fq-card-icon" aria-hidden="true">🏃</span>
                </div>
                @if($routine)
                    <p class="fq-routine-title">{{ $routine->name }}</p>
                    @if($todaysExercises->isNotEmpty())
                        <p class="fq-muted">{{ $completedToday }} de {{ $todaysExercises->count() }} ejercicios completados</p>
                        <div class="fq-progress-track" role="progressbar" aria-label="Progreso de hoy" aria-valuenow="{{ (int) round($completedToday / $todaysExercises->count() * 100) }}" aria-valuemin="0" aria-valuemax="100">
                            <span style="width: {{ (int) round($completedToday / $todaysExercises->count() * 100) }}%"></span>
                        </div>
                    @else
                        <p class="fq-muted">Hoy es un día de descanso según tu rutina.</p>
                    @endif
                @elseif($routineStorageAvailable)
                    <p class="fq-muted">Aún no tienes una rutina. Puedes crear una a tu medida.</p>
                @else
                    <p class="fq-muted">La rutina aparecerá aquí cuando la base de datos tenga sus tablas listas.</p>
                @endif
                <a class="fq-text-link" href="{{ route('entreno') }}">{{ $routine ? 'Ver mi rutina' : 'Crear rutina' }} <span aria-hidden="true">→</span></a>
            </article>
            <article class="fq-card fq-calendar-summary">
                <div class="fq-card-heading">
                    <div><p class="fq-eyebrow">ORGANÍZATE</p><h2>Tu calendario</h2></div>
                    <span class="fq-card-icon fq-icon-green" aria-hidden="true">📅</span>
                </div>
                <p class="fq-muted">Consulta los días de entrenamiento y las actividades completadas.</p>
                <a class="fq-text-link" href="{{ route('calendario') }}">Abrir calendario <span aria-hidden="true">→</span></a>
            </article>
            <article class="fq-card fq-achievements">
                <div class="fq-card-heading">
                    <div><p class="fq-eyebrow">TUS LOGROS</p><h2>Un paso a la vez</h2></div>
                    <span class="fq-card-icon fq-icon-green" aria-hidden="true">✦</span>
                </div>
                <p class="fq-muted">Los logros se habilitarán cuando se incorpore el sistema de retos. Por ahora, cada ejercicio completado suma 10 XP.</p>
            </article>
            <article class="fq-card">
                <div class="fq-card-heading">
                    <div><p class="fq-eyebrow">BIENESTAR</p><h2>Haz una pausa</h2></div>
                    <span class="fq-card-icon fq-icon-green" aria-hidden="true">💚</span>
                </div>
                <p class="fq-muted">Explora las actividades de descanso y bienestar que ya forman parte de FamFit Quest.</p>
                <a class="fq-text-link" href="{{ route('bienestar') }}">Ir a bienestar <span aria-hidden="true">→</span></a>
            </article>
        </div>
    </section>
@endsection
