<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#F5F7FB">
    <title>Menú | FamFit Quest</title>
    <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/estilomenu.css') }}">
</head>
<body>
    <button
        class="fq-mobile-toggle"
        id="fqMobileToggle"
        type="button"
        aria-label="Abrir menú"
        aria-controls="fqSidebar"
        aria-expanded="false"
    >
        <svg viewBox="0 0 24 24" aria-hidden="true">
            <path d="M4 6h16M4 12h16M4 18h16"></path>
        </svg>
    </button>
    <div class="fq-overlay" id="fqOverlay"></div>

    <aside class="fq-sidebar" id="fqSidebar" aria-label="Menú principal">
        <a class="fq-brand" href="{{ route('menu') }}" aria-label="FamFit Quest, inicio">
            <span class="fq-logo" aria-hidden="true">F</span>
            <span class="fq-brand-text">
                <strong>FamFit Quest</strong>
                <small>BIENESTAR EN FAMILIA</small>
            </span>
        </a>

        <nav class="fq-navigation" aria-label="Secciones">
            <span class="fq-section-title">MI ESPACIO</span>

            <a class="fq-link is-active" href="{{ route('menu') }}" aria-current="page">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="m3 10 9-7 9 7v10a1 1 0 0 1-1 1h-5v-7H9v7H4a1 1 0 0 1-1-1z"></path></svg>
                </span>
                <span class="fq-link-text">Inicio</span>
                <span class="fq-tooltip" aria-hidden="true">Inicio</span>
            </a>

            <a class="fq-link" href="{{ route('bienestar') }}">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M20.8 4.6a5.5 5.5 0 0 0-7.8 0L12 5.7l-1.1-1.1a5.5 5.5 0 0 0-7.8 7.8l1.1 1.1L12 21l7.8-7.5 1.1-1.1a5.5 5.5 0 0 0-.1-7.8Z"></path><path d="M3.5 12h4l2-3 3 6 2-3h6"></path></svg>
                </span>
                <span class="fq-link-text">Bienestar</span>
                <span class="fq-tooltip" aria-hidden="true">Bienestar</span>
            </a>

            <a class="fq-link" href="#" data-pending="true" aria-disabled="true">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 19V5M4 19h17"></path><path d="m7 15 4-4 3 2 6-7"></path></svg>
                </span>
                <span class="fq-link-text">Mi progreso</span>
                <span class="fq-tooltip" aria-hidden="true">Mi progreso</span>
            </a>

            <a class="fq-link" href="#" data-pending="true" aria-disabled="true">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 3v3M5.6 5.6l2.1 2.1M3 12h3m-.4 6.4 2.1-2.1M12 18v3m6.4-2.6-2.1-2.1M18 12h3m-5.4-4.3 2.1-2.1"></path><circle cx="12" cy="12" r="5"></circle></svg>
                </span>
                <span class="fq-link-text">Hábitos</span>
                <span class="fq-tooltip" aria-hidden="true">Hábitos</span>
            </a>

            <a class="fq-link" href="#" data-pending="true" aria-disabled="true">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M4 7h16v13H4zM8 7V4h8v3M4 12h16M9 12v2h6v-2"></path></svg>
                </span>
                <span class="fq-link-text">Alimentación</span>
                <span class="fq-tooltip" aria-hidden="true">Alimentación</span>
            </a>

            <a class="fq-link" href="#" data-pending="true" aria-disabled="true">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><rect x="3" y="5" width="18" height="16" rx="2"></rect><path d="M16 3v4M8 3v4M3 10h18"></path></svg>
                </span>
                <span class="fq-link-text">Calendario</span>
                <span class="fq-tooltip" aria-hidden="true">Calendario</span>
            </a>

            <a class="fq-link" href="#" data-pending="true" aria-disabled="true">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><circle cx="9" cy="8" r="3"></circle><path d="M3 20v-1a6 6 0 0 1 12 0v1zM16 5.5a3 3 0 0 1 0 5.8M18 14a5 5 0 0 1 3 4.6v1"></path></svg>
                </span>
                <span class="fq-link-text">Comunidad</span>
                <span class="fq-tooltip" aria-hidden="true">Comunidad</span>
            </a>

            <span class="fq-section-title fq-preferences-title">PREFERENCIAS</span>
            <a class="fq-link" href="{{ route('configuracion') }}">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8Z"></path><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.6.9l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.6-.9l-1.7.7-1.4-2.4 1.4-1.1a7 7 0 0 1 0-1.9l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.6-.9l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.6.9l1.7-.7 1.4 2.4-1.4 1.1a7 7 0 0 1-.1 1.8Z"></path></svg>
                </span>
                <span class="fq-link-text">Configuración</span>
                <span class="fq-tooltip" aria-hidden="true">Configuración</span>
            </a>
        </nav>

        <div class="fq-profile">
            <button
                class="fq-profile-button"
                id="fqAvatarButton"
                type="button"
                aria-label="Personalizar perfil"
                aria-controls="fqAvatarPicker"
                aria-expanded="false"
            >
                <span class="fq-avatar" id="fqAvatarDisplay" aria-hidden="true">🦊</span>
                <span class="fq-profile-info">
                    <strong id="fqProfileName">{{ auth()->user()?->name ?? 'Mi perfil' }}</strong>
                    <small id="fqProfileLevel">Nivel 1</small>
                </span>
                <span class="fq-edit-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M12 20h9"></path><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z"></path></svg>
                </span>
            </button>

            <section class="fq-avatar-picker" id="fqAvatarPicker" aria-label="Personalizar perfil" hidden>
                <strong>Personaliza tu perfil</strong>
                <div class="fq-avatar-options" aria-label="Elige un avatar">
                    <button class="fq-avatar-option" type="button" data-avatar="🦊" aria-label="Zorro" aria-pressed="false">🦊</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🐼" aria-label="Panda" aria-pressed="false">🐼</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🐸" aria-label="Rana" aria-pressed="false">🐸</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🐯" aria-label="Tigre" aria-pressed="false">🐯</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🐨" aria-label="Koala" aria-pressed="false">🐨</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🐙" aria-label="Pulpo" aria-pressed="false">🐙</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🦁" aria-label="León" aria-pressed="false">🦁</button>
                    <button class="fq-avatar-option" type="button" data-avatar="🐢" aria-label="Tortuga" aria-pressed="false">🐢</button>
                </div>
                <label class="fq-profile-label" for="fqNameInput">Nombre del perfil</label>
                <input id="fqNameInput" type="text" maxlength="30" autocomplete="nickname">
                <label class="fq-profile-label" for="fqLevelInput">Nivel</label>
                <input id="fqLevelInput" type="number" min="1" max="999" step="1">
                <button class="fq-save-profile" id="fqSaveProfile" type="button">Guardar perfil</button>
            </section>
        </div>

        @auth
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button class="fq-link" type="submit" style="border: 0; background: transparent; cursor: pointer; font: inherit; text-align: left;">
                    <span class="fq-icon" aria-hidden="true">
                        <svg viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3"></path><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"></path></svg>
                    </span>
                    <span class="fq-link-text">Cerrar sesión</span>
                    <span class="fq-tooltip" aria-hidden="true">Cerrar sesión</span>
                </button>
            </form>
        @else
            <a class="fq-link" href="{{ route('login') }}">
                <span class="fq-icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M10 17l5-5-5-5M15 12H3"></path><path d="M12 3h7a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-7"></path></svg>
                </span>
                <span class="fq-link-text">Iniciar sesión</span>
                <span class="fq-tooltip" aria-hidden="true">Iniciar sesión</span>
            </a>
        @endauth
    </aside>

    <main class="fq-main">
        <div class="contenedor">
            <header class="encabezado-pagina">
                <div>
                    <p class="texto-azul">TU ESPACIO FAMFIT</p>
                    <h1>¡Hola, {{ auth()->user()?->name ?? 'campeón' }}! 👋</h1>
                    <p class="texto-secundario">
                        Pequeños hábitos, grandes logros. ¿Qué te gustaría hacer hoy?
                    </p>
                </div>
            </header>

            @if (session('status'))
                <div class="alerta alerta-exito" role="status">{{ session('status') }}</div>
            @endif

            <section aria-labelledby="titulo-accesos">
                <h2 id="titulo-accesos">Tu aventura saludable</h2>
                <div class="cuadricula cuadricula-2">
                    <article class="tarjeta tarjeta-principal">
                        <div class="tarjeta-titulo">
                            <span aria-hidden="true">💚</span>
                            <h3>Bienestar</h3>
                        </div>
                        <p class="texto-secundario">
                            Explora actividades de descanso y bienestar para cuidar de ti cada día.
                        </p>
                        <a class="boton boton-azul" href="{{ route('bienestar') }}">Ir a bienestar <span aria-hidden="true">→</span></a>
                    </article>

                    <article class="tarjeta">
                        <div class="tarjeta-titulo">
                            <span aria-hidden="true">⚙️</span>
                            <h3>Configuración</h3>
                        </div>
                        <p class="texto-secundario">
                            Consulta las opciones disponibles y personaliza tus preferencias.
                        </p>
                        <a class="boton boton-secundario" href="{{ route('configuracion') }}">Ver configuración <span aria-hidden="true">→</span></a>
                    </article>
                </div>
            </section>

            <footer style="margin-top: 32px; color: var(--texto-secundario);">
                FamFit Quest · Bienestar para toda la familia 💚
            </footer>
        </div>
    </main>
    
    <script src="{{ asset('js/menu.js') }}" defer></script>
</body>
</html>
