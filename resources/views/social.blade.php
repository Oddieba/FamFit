@extends('layouts.quest')

@section('title', 'Comunidad | FamFit Quest')
@push('styles')
    <link rel="stylesheet" href="{{ asset('estilos/comunidad.css') }}">
@endpush

@section('content')
<section class="community" id="community" data-people="{{ route('social.people') }}" data-friendship="{{ route('social.friendship') }}" data-token="{{ csrf_token() }}">
    <svg class="cm-symbols" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
        <symbol id="cm-compass" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m16 8-2.4 5.6L8 16l2.4-5.6z"/></symbol>
        <symbol id="cm-team" viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"/><path d="M2.5 20v-2a6.5 6.5 0 0 1 13 0v2M16 5a3 3 0 0 1 0 6m2 3a5 5 0 0 1 3.5 5"/></symbol>
        <symbol id="cm-mail" viewBox="0 0 24 24"><rect x="2.5" y="5" width="19" height="14" rx="2.5"/><path d="m4 7 8 6 8-6"/></symbol>
        <symbol id="cm-search" viewBox="0 0 24 24"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5"/></symbol>
        <symbol id="cm-plus" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></symbol>
        <symbol id="cm-shield" viewBox="0 0 24 24"><path d="m12 2 8 3v6c0 5-4 8.5-8 11-4-2.5-8-6-8-11V5z"/><path d="m8.5 11.5 2.5 2.5 4.5-5"/></symbol>
        <symbol id="cm-sliders" viewBox="0 0 24 24"><path d="M3 7h4m6 0h8M3 17h10m6 0h2"/><circle cx="10" cy="7" r="3"/><circle cx="16" cy="17" r="3"/></symbol>
    </svg>
    <header class="cm-top"><span><b>FamFit Quest</b><i aria-hidden="true">/</i>Social</span>
        <a href="{{ route('configuracion') }}"><svg aria-hidden="true"><use href="#cm-sliders"/></svg>Configuración</a>
    </header>
    <div class="cm-hero">
        <div class="cm-hero-copy">
            <div class="cm-category"><span aria-hidden="true"></span>Comunidad FamFit</div>
            <h1>Encuentra a<br><em>tu equipo.</em></h1>
            <p>Busca personas y conecta con quienes te acompañan a entrenar.</p>
        </div>
    </div>
    <nav class="cm-tabs" aria-label="Secciones de comunidad">
        <button type="button" data-list="discover" aria-current="page"><svg aria-hidden="true"><use href="#cm-compass"/></svg>Descubrir</button>
        <button type="button" data-list="friends"><svg aria-hidden="true"><use href="#cm-team"/></svg>Amistades</button>
        <button type="button" data-list="incoming"><svg aria-hidden="true"><use href="#cm-mail"/></svg>Solicitudes <span class="cm-count" data-count="incoming" hidden></span></button>
        <button type="button" data-list="blocked"><svg aria-hidden="true"><use href="#cm-shield"/></svg>Bloqueados</button>
    </nav>
    <noscript>Activa JavaScript para buscar personas y gestionar tus solicitudes de amistad.</noscript>
    <div class="cm-layout">
        <section class="cm-directory" aria-labelledby="cm-title">
            <h2 id="cm-title">Descubrir personas</h2>
            <div class="cm-request-filters" role="group" aria-label="Tipo de solicitud" hidden>
                <button type="button" data-request="incoming">Recibidas</button>
                <button type="button" data-request="outgoing">Enviadas</button>
            </div>
            <form class="cm-search" role="search">
                <label class="cm-search-field"><svg aria-hidden="true"><use href="#cm-search"/></svg>
                    <input type="search" name="q" maxlength="60" aria-label="Buscar por nombre de usuario" placeholder="Buscar por nombre de usuario" autocomplete="off">
                </label>
                <button type="submit">Buscar</button>
            </form>
            <p class="cm-notice" role="status" aria-live="polite"></p>
            <div class="cm-results" aria-busy="true">
                <p class="cm-loading">Cargando comunidad…</p>
            </div>
            <nav class="cm-paging" aria-label="Páginas de resultados" hidden>
                <button type="button" data-page="-1">Anterior</button><span></span><button type="button" data-page="1">Siguiente</button>
            </nav>
        </section>
        <aside class="cm-aside" aria-label="Resumen personal">
            <section class="cm-circle">
                <div class="cm-circle-title"><svg aria-hidden="true"><use href="#cm-team"/></svg><h2>Tu círculo</h2></div>
                <div class="cm-stats">
                    <button type="button" data-shortcut="friends"><b data-count="friends">—</b><span>Amistades</span></button>
                    <button type="button" data-shortcut="incoming"><b data-count="incoming">—</b><span>Por responder</span></button>
                </div>
            </section>
            <section class="cm-privacy">
                <svg aria-hidden="true"><use href="#cm-shield"/></svg>
                <div><h3>Tu visibilidad</h3>
                    <p>{{ $account->profile_visible ? 'En Comunidad compartes tu alias, tu nombre público y tu avatar.' : 'En Comunidad pueden encontrarte por tu alias. Tu nombre público y tu avatar están ocultos.' }}</p>
                    <a href="{{ route('configuracion') }}#settingPublic">Ajustar privacidad</a>
                </div>
            </section>
        </aside>
    </div>
    <dialog class="cm-confirm" aria-labelledby="cm-confirm-title" aria-describedby="cm-confirm-detail">
        <form method="dialog">
            <h2 id="cm-confirm-title"></h2>
            <p id="cm-confirm-detail"></p>
            <div><button value="cancel" autofocus>Volver</button><button value="confirm"></button></div>
        </form>
    </dialog>
    <dialog class="cm-confirm cm-report" id="cm-report" aria-labelledby="cm-report-title">
    <form method="post" action="{{ route('social.report') }}">
        @csrf
        <input type="hidden" name="person">

        <h2 id="cm-report-title">Reportar a <span></span></h2>

        <label>
            Motivo
            <select name="reason" required autofocus>
                <option value="">Elige un motivo</option>
                @foreach ($reportReasons as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
        </label>

        <label>
            ¿Qué ocurrió? (opcional, hasta 500 caracteres)
            <textarea name="description" rows="3" maxlength="500"></textarea>
        </label>

        <p role="alert"></p>

        <div>
            <button type="button">Cancelar</button>
            <button type="submit">Enviar reporte</button>
        </div>
    </form>
</dialog>
</section>
@endsection

@push('scripts')
    <script type="module" src="{{ asset('js/comunidad.js') }}"></script>
@endpush
