<!DOCTYPE html>
<html lang="es" data-fq-dark-mode="{{ is_bool(auth()->user()->preferences['dark_mode'] ?? null) ? (auth()->user()->preferences['dark_mode'] ? 'true' : 'false') : '' }}" @class(['theme-dark' => (bool) (auth()->user()->preferences['dark_mode'] ?? false)])>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#F5F7FB">
    <title>@yield('title', 'FamFit Quest')</title>
    <script src="{{ asset('js/theme.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/quest.css') }}">
</head>
<body class="fq-app">
    <button class="fq-mobile-toggle" id="fqMobileToggle" type="button" aria-label="Abrir menú" aria-controls="fqSidebar" aria-expanded="false">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
    </button>
    <div class="fq-overlay" id="fqOverlay"></div>
    <aside class="fq-sidebar" id="fqSidebar" aria-label="Menú principal">
        <div class="fq-sidebar-top">
            <a class="fq-brand" href="{{ route('menu') }}" aria-label="FamFit Quest, Inicio">
                <span class="fq-brand-mark" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M6 19V5h13v3H9v3h8v3H9v5z"></path></svg>
                </span>
                <span class="fq-brand-name">FamFit <b>Quest</b></span>
            </a>
            <button class="fq-menu-toggle" id="fqMenuToggle" type="button" aria-label="Abrir menú" aria-controls="fqSidebar" aria-expanded="false">
                <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
        </div>

        <nav class="fq-navigation" aria-label="Secciones principales">
            <a class="fq-nav-link {{ request()->routeIs('menu') ? 'is-active' : '' }}" href="{{ route('menu') }}" @if(request()->routeIs('menu')) aria-current="page" @endif>
                <span class="fq-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"></path></svg></span><span class="fq-nav-label">Inicio</span>
            </a>
            <a class="fq-nav-link {{ request()->routeIs('entreno*') ? 'is-active' : '' }}" href="{{ route('entreno') }}" @if(request()->routeIs('entreno*')) aria-current="page" @endif>
                <span class="fq-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M6 8v8m12-8v8M3 10v4m18-4v4M6 12h12"></path></svg></span><span class="fq-nav-label">Entreno</span>
            </a>
            <a class="fq-nav-link {{ request()->routeIs('calendario') ? 'is-active' : '' }}" href="{{ route('calendario') }}" @if(request()->routeIs('calendario')) aria-current="page" @endif>
                <span class="fq-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg></span><span class="fq-nav-label">Calendario</span>
            </a>
            <a class="fq-nav-link {{ request()->routeIs('social') ? 'is-active' : '' }}" href="{{ route('social') }}" @if(request()->routeIs('social')) aria-current="page" @endif>
                <span class="fq-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"></circle><path d="M3 20v-1a6 6 0 0 1 12 0v1zM16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.6v1"></path></svg></span><span class="fq-nav-label">Social</span>
            </a>
            <a class="fq-nav-link {{ request()->routeIs('configuracion') ? 'is-active' : '' }}" href="{{ route('configuracion') }}" @if(request()->routeIs('configuracion')) aria-current="page" @endif>
                <span class="fq-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"></path><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.6.9l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.6-.9l-1.7.7-1.4-2.4 1.4-1.1a7 7 0 0 1 0-1.9l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.6-.9l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.6.9l1.7-.7 1.4 2.4-1.4 1.1a7 7 0 0 1-.1 1.8Z"></path></svg></span><span class="fq-nav-label">Configuración</span>
            </a>
            <a class="fq-nav-link {{ request()->routeIs('perfil*') ? 'is-active' : '' }}" href="{{ route('perfil') }}" @if(request()->routeIs('perfil*')) aria-current="page" @endif>
                <span class="fq-nav-icon" aria-hidden="true"><svg viewBox="0 0 24 24"><circle cx="12" cy="8" r="4"></circle><path d="M4 21a8 8 0 0 1 16 0"></path></svg></span><span class="fq-nav-label">Perfil</span>
            </a>
        </nav>

        <div class="fq-sidebar-bottom">
            <a class="fq-user-link" href="{{ route('perfil') }}" aria-label="Ver perfil de {{ auth()->user()->name }}">
                <span class="fq-mini-avatar">{{ auth()->user()->avatar_settings['animal'] ?? '🦊' }}</span>
                <span class="fq-user-label"><b>{{ auth()->user()->name }}</b><small>{{ auth()->user()->xp ?? 0 }} XP</small></span>
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="fq-logout" type="submit"><span aria-hidden="true">↪</span><span class="fq-nav-label">Cerrar sesión</span></button>
            </form>
        </div>
    </aside>

    <main class="fq-main">
        <div class="fq-page">
            @if (session('status'))
                <div class="fq-alert fq-alert-success" role="status">{{ session('status') }}</div>
            @endif
            @if ($errors->any())
                <div class="fq-alert fq-alert-error" role="alert">
                    <strong>Revisa estos datos:</strong>
                    <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>
            @endif
            @yield('content')
        </div>
    </main>
    <script src="{{ asset('js/quest.js') }}" defer></script>
</body>
</html>
