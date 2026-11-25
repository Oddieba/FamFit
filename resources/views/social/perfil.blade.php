@extends('layouts.quest')

@section('title', 'Perfil de @'.$person['username'].' | FamFit Quest')

@push('styles')
    <link rel="stylesheet" href="{{ asset('estilos/comunidad.css') }}">
@endpush

@section('content')
@php
    $alias = '@'.$person['username'];
    $hasName = $person['display'] !== $person['username'];
    $initials = mb_strtoupper(mb_substr($person['display'], 0, 2));
@endphp

<article class="community cm-profile" aria-labelledby="profile-name">
    <a class="cm-back" href="{{ route('social') }}#{{ $section }}">
        <span aria-hidden="true">←</span> Volver a Comunidad
    </a>

    <header class="cm-profile-sheet">
        <div class="cm-profile-cover" aria-hidden="true"></div>

        <div class="cm-profile-info">
            <span class="cm-avatar"
                  data-tone="{{ $person['id'] % 4 }}"
                  aria-hidden="true">
                {{ $person['avatar'] ?? $initials }}
            </span>

            <h1 id="profile-name">
                {{ $hasName ? $person['display'] : $alias }}
            </h1>

            @if ($hasName)
                <p>{{ $alias }}</p>
            @elseif (! $person['avatar'])
                <p>Esta cuenta comparte solo su alias.</p>
            @endif
        </div>
    </header>
</article>
@endsection