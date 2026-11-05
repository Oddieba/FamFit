@extends('layouts.quest')

@section('title', 'Perfil | FamFit Quest')

@section('content')
    <header class="fq-page-heading"><div><p class="fq-eyebrow">TU CUENTA</p><h1>Perfil</h1><p class="fq-muted">Administra tus datos y consulta tu progreso.</p></div></header>
    <section class="fq-profile-overview">
        <article class="fq-card fq-profile-card">
            <div class="fq-avatar-art fq-profile-avatar" style="--fq-outfit: {{ $avatar['outfit'] }}"><span class="fq-avatar-character">{{ $avatar['animal'] }}</span>@if($avatar['accessory_symbol'])<span class="fq-avatar-accessory">{{ $avatar['accessory_symbol'] }}</span>@endif</div>
            <div><p class="fq-eyebrow">NIVEL {{ $level }}</p><h2>{{ $user->name }}</h2><p class="fq-muted">{{ $user->username ? '@'.$user->username : $user->email }}</p><a class="fq-text-link" href="{{ route('menu') }}">Personalizar avatar →</a></div>
        </article>
        <article class="fq-card fq-stat-card"><span class="fq-card-icon fq-icon-green" aria-hidden="true">✦</span><strong>{{ $user->xp ?? 0 }}</strong><span class="fq-muted">XP acumulados</span><div class="fq-progress-track" role="progressbar" aria-label="Progreso de nivel" aria-valuenow="{{ $xpProgress }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $xpProgress }}%"></span></div><small class="fq-muted">{{ $xpProgress }} / 100 XP al siguiente nivel</small></article>
        <article class="fq-card fq-stat-card"><span class="fq-card-icon" aria-hidden="true">✓</span><strong>{{ $completedCount }}</strong><span class="fq-muted">Ejercicios completados</span><small class="fq-muted">{{ $routine ? 'Rutina: '.$routine->name : 'Aún no tienes una rutina' }}</small></article>
    </section>
    <section class="fq-card fq-profile-form-card">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">INFORMACIÓN PERSONAL</p><h2>Datos de la cuenta</h2></div><span class="fq-card-icon" aria-hidden="true">👤</span></div>
        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="fq-form-grid">
                <div><label class="fq-field-label" for="profileName">Nombre</label><input class="fq-input" id="profileName" name="name" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required></div>
                <div><label class="fq-field-label" for="profileUsername">Nombre de usuario</label><input class="fq-input" id="profileUsername" name="username" value="{{ old('username', $user->username) }}" minlength="3" maxlength="20" pattern="[A-Za-z0-9_]+" autocomplete="username" required></div>
                <div class="fq-form-wide"><label class="fq-field-label" for="profileEmail">Correo electrónico</label><input class="fq-input" id="profileEmail" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="190" autocomplete="email" required></div>
            </div>
            <button class="fq-button fq-button-primary" type="submit">Guardar perfil</button>
        </form>
    </section>
@endsection
