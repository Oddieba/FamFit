@extends('layouts.quest')
@section('title', 'Explorar usuarios | FamFit Quest')
@section('content')
<header class="fq-page-heading"><div><p class="fq-eyebrow">COMUNIDAD</p><h1>Explorar usuarios</h1><p class="fq-muted">Controla lo que compartes.</p></div></header>
@include('social.partials.nav')
<section class="fq-card fq-social-hero"><h2>Próximamente</h2><p class="fq-muted">La búsqueda por nombre de usuario estará disponible en una siguiente etapa. No se consultan ni exponen perfiles por ahora.</p><div class="fq-social-actions"><a class="fq-button fq-button-primary" href="{{ route('social') }}">Volver a Social</a></div></section>
@endsection
