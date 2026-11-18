@extends('layouts.quest')
@section('title', 'Descubrir personas | FamFit Quest')
@section('content')
<header class="fq-page-heading"><div><p class="fq-eyebrow">COMUNIDAD</p><h1>Descubrir personas</h1><p class="fq-muted">Controla lo que compartes.</p></div></header>
@include('social.partials.nav')
<section class="fq-card fq-social-hero"><h2>Próximamente</h2><p class="fq-muted">Aquí aparecerán tarjetas de usuarios que hayan permitido ser descubiertos. La función de deslizar y enviar solicitudes todavía no está habilitada.</p><div class="fq-social-actions"><a class="fq-button fq-button-primary" href="{{ route('social') }}">Volver a Social</a></div></section>
@endsection
