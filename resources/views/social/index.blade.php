@extends('layouts.quest')
@section('title', 'Social | FamFit Quest')
@section('content')
<header class="fq-page-heading"><div><p class="fq-eyebrow">AVANZAR EN COMPAÑÍA</p><h1>Social</h1><p class="fq-muted">Comparte solo lo que tú decidas.</p></div></header>
@include('social.partials.nav')
<section class="fq-card fq-social-hero">
    <div class="fq-social-illustration" aria-hidden="true"><span>🤝</span><span>💚</span><span>🏆</span></div>
    <p class="fq-eyebrow">TU COMUNIDAD</p><h2>Conecta a tu ritmo</h2>
    <p class="fq-muted">Este módulo está en preparación. Por ahora, las secciones son de navegación y no muestran información personal de otros usuarios.</p>
    <div class="fq-social-actions"><a class="fq-button fq-button-primary" href="{{ route('social.discover') }}">Descubrir personas</a><a class="fq-button fq-button-light" href="{{ route('configuracion') }}">Configuración</a></div>
</section>
<section class="fq-social-grid" aria-label="Secciones sociales">
    <article class="fq-card"><span class="fq-card-icon" aria-hidden="true">🔎</span><h3>Explorar</h3><p class="fq-muted">Próximamente podrás buscar usuarios por nombre de usuario.</p><a href="{{ route('social.explore') }}">Ir a explorar</a></article>
    <article class="fq-card"><span class="fq-card-icon" aria-hidden="true">👥</span><h3>Amigos</h3><p class="fq-muted">Aquí se mostrarán las amistades aceptadas.</p><a href="{{ route('social.friends') }}">Ver sección</a></article>
    <article class="fq-card"><span class="fq-card-icon" aria-hidden="true">✉️</span><h3>Solicitudes</h3><p class="fq-muted">Más adelante podrás gestionar solicitudes recibidas y enviadas.</p><a href="{{ route('social.requests') }}">Ver sección</a></article>
</section>
@endsection
