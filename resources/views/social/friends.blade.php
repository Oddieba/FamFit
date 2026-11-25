@extends('layouts.quest')
@section('title', 'Mis amigos | FamFit Quest')
@section('content')
<header class="fq-page-heading"><div><p class="fq-eyebrow">COMUNIDAD</p><h1>Mis amigos</h1><p class="fq-muted">Controla lo que compartes.</p></div></header>
@include('social.partials.nav')
<section class="fq-card fq-social-hero"><h2>Próximamente</h2><p class="fq-muted">Aquí aparecerán únicamente las amistades aceptadas. Todavía no se realizan consultas a la tabla friendships.</p><div class="fq-social-actions"><a class="fq-button fq-button-primary" href="{{ route('social') }}">Volver a Social</a></div></section>
@endsection
