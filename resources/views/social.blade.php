@extends('layouts.quest')

@section('title', 'Social | FamFit Quest')

@section('content')
    <header class="fq-page-heading">
        <div><p class="fq-eyebrow">AVANZAR EN COMPAÑÍA</p><h1>Social</h1><p class="fq-muted">Comparte solo lo que tú decidas.</p></div>
    </header>
    <section class="fq-card fq-social-hero">
        <div class="fq-social-illustration" aria-hidden="true"><span>🤝</span><span>💚</span><span>🏆</span></div>
        <p class="fq-eyebrow">TU COMUNIDAD</p><h2>Un espacio para conectar</h2>
        <p class="fq-muted">Amigos, grupos familiares y retos compartidos todavía no están disponibles en esta versión. No se publica automáticamente ningún dato personal ni de actividad.</p>
        <div class="fq-social-actions"><a class="fq-button fq-button-primary" href="{{ route('perfil') }}">Revisar mi perfil</a><a class="fq-button fq-button-light" href="{{ route('configuracion') }}">Preferencias de privacidad</a></div>
    </section>
    <section class="fq-social-grid" aria-label="Funciones sociales">
        <article class="fq-card"><span class="fq-card-icon" aria-hidden="true">👥</span><h3>Amigos y grupos</h3><p class="fq-muted">La conexión y gestión de grupos aún no está configurada.</p></article>
        <article class="fq-card"><span class="fq-card-icon fq-icon-green" aria-hidden="true">🎯</span><h3>Retos compartidos</h3><p class="fq-muted">No hay retos grupales guardados en el proyecto todavía.</p></article>
        <article class="fq-card"><span class="fq-card-icon" aria-hidden="true">🔒</span><h3>Tú decides</h3><p class="fq-muted">Tu perfil es privado por defecto. Puedes revisar esa preferencia en Configuración.</p></article>
    </section>
@endsection
