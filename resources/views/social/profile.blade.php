@extends('layouts.quest')
@section('title', 'Mi perfil social | FamFit Quest')
@section('content')
<header class="fq-page-heading"><div><p class="fq-eyebrow">COMUNIDAD</p><h1>Mi perfil social</h1><p class="fq-muted">Controla lo que compartes.</p></div></header>
@include('social.partials.nav')
<section class="fq-card fq-social-hero"><h2>Próximamente</h2><p class="fq-muted">En esta sección podrás revisar la información pública de tu perfil y los permisos de visibilidad. Por ahora no se muestran datos personales ni se modifican preferencias.</p><div class="fq-social-actions"><a class="fq-button fq-button-primary" href="{{ route('social') }}">Volver a Social</a></div></section>
@endsection
