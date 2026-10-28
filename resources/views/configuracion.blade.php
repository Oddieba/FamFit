<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configuración | FamFit Quest</title>

    <script src="{{ asset('js/theme.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/Configuracion.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">
</head>

<body>

<div class="layout">

    <!-- MENÚ LATERAL -->
    <aside class="sidebar">

        <div class="logo">
            <span class="logo-icon">🌿</span>
            <span>FamFit Quest</span>
        </div>

        <p class="menu-label">MENÚ PRINCIPAL</p>

        <nav>
            <a href="{{ url('/inicio') }}" class="nav-link">🏠 Inicio</a>
            <a href="{{ url('/progreso') }}" class="nav-link">📈 Progreso</a>
            <a href="{{ url('/habitos') }}" class="nav-link">🎯 Hábitos</a>
            <a href="{{ url('/nutricion') }}" class="nav-link">🥗 Nutrición</a>
            <a href="{{ url('/calendario') }}" class="nav-link">📅 Calendario</a>
            <a href="{{ url('/social') }}" class="nav-link">👥 Social</a>
        </nav>

        <p class="menu-label">PREFERENCIAS</p>

        <nav>
            <a href="{{ url('/configuracion') }}" class="nav-link active">
                ⚙️ Configuración
            </a>
        </nav>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main">

        <header class="topbar">
            <div>
                <h1>Configuración ⚙️</h1>
                <p class="subtitle">
                    Personaliza FamFit Quest según tus necesidades.
                </p>
            </div>

            <div class="profile">
                <div class="avatar">👤</div>
                <span>Mi perfil</span>
            </div>
        </header>

        <div class="notice" id="notice" role="status" aria-live="polite">
            Preferencias guardadas en esta demostración.
        </div>

        <form id="settingsForm">

            <h2 class="section-title">Mi cuenta</h2>

            <div class="settings-grid cuadricula cuadricula-2">

                <!-- INFORMACIÓN PERSONAL -->
                <section class="card tarjeta">
                    <div class="card-header">
                        <div class="card-icon">👤</div>
                        <div>
                            <h3>Información personal</h3>
                            <p>Administra tus datos básicos</p>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Nombre de usuario</strong>
                            <span>Cómo aparecerás en la aplicación.</span>

                            <input class="field campo" type="text"
                                   name="nombre" placeholder="Tu nombre">
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Correo electrónico</strong>
                            <span>Tu correo de contacto.</span>

                            <input class="field campo" type="email"
                                   name="correo"
                                   placeholder="correo@ejemplo.com">
                        </div>
                    </div>
                </section>

                <!-- APARIENCIA -->
                <section class="card tarjeta">
                    <div class="card-header">
                        <div class="card-icon">🎨</div>
                        <div>
                            <h3>Apariencia</h3>
                            <p>Personaliza el diseño</p>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Modo oscuro</strong>
                            <span>Reduce el brillo de la interfaz.</span>
                        </div>

                        <label class="switch">
                            <input type="checkbox" id="darkMode">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Color principal</strong>
                            <span>Selecciona el color de la aplicación.</span>

                            <select class="field campo" name="color">
                                <option value="#22c55e">Verde natural</option>
                                <option value="#3b82f6">Azul</option>
                                <option value="#a855f7">Morado</option>
                                <option value="#f97316">Naranja</option>
                            </select>
                        </div>
                    </div>
                </section>

                <!-- NOTIFICACIONES -->
                <section class="card tarjeta">
                    <div class="card-header">
                        <div class="card-icon">🔔</div>
                        <div>
                            <h3>Notificaciones</h3>
                            <p>Organiza tus recordatorios</p>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Recordatorios de hábitos</strong>
                            <span>Recuerda completar tus actividades.</span>
                        </div>

                        <label class="switch">
                            <input type="checkbox"
                                   name="notificaciones_habitos" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Retos y logros</strong>
                            <span>Recibe avisos sobre tus desafíos.</span>
                        </div>

                        <label class="switch">
                            <input type="checkbox"
                                   name="notificaciones_retos" checked>
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Hora del recordatorio</strong>
                            <input class="field campo" type="time"
                                   name="hora_recordatorio" value="19:00">
                        </div>
                    </div>
                </section>

                <!-- OBJETIVOS -->
                <section class="card tarjeta">
                    <div class="card-header">
                        <div class="card-icon">🎯</div>
                        <div>
                            <h3>Objetivos y hábitos</h3>
                            <p>Define tus metas personales</p>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Meta diaria de agua</strong>
                            <span>Objetivo de hidratación en mililitros.</span>

                            <input class="field campo" type="number"
                                   name="meta_agua" min="1"
                                   max="10000" value="2000">
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Horas de sueño objetivo</strong>
                            <span>Tu meta de descanso por noche.</span>

                            <input class="field campo" type="number"
                                   name="meta_sueno" min="1"
                                   max="24" value="8">
                        </div>
                    </div>
                </section>

                <!-- PRIVACIDAD -->
                <section class="card tarjeta">
                    <div class="card-header">
                        <div class="card-icon">🔒</div>
                        <div>
                            <h3>Privacidad</h3>
                            <p>Controla lo que otros pueden ver</p>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Perfil público</strong>
                            <span>Permite que otros usuarios vean tu perfil.</span>
                        </div>

                        <label class="switch">
                            <input type="checkbox" name="perfil_publico">
                            <span class="slider"></span>
                        </label>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Compartir logros</strong>
                            <span>Permite mostrar tus logros a tus amigos.</span>
                        </div>

                        <label class="switch">
                            <input type="checkbox"
                                   name="compartir_logros" checked>
                            <span class="slider"></span>
                        </label>
                    </div>
                </section>

                <!-- SEGURIDAD -->
                <section class="card tarjeta">
                    <div class="card-header">
                        <div class="card-icon">🛡️</div>
                        <div>
                            <h3>Seguridad</h3>
                            <p>Protege tu cuenta</p>
                        </div>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Cambiar Contraseña</strong>
                            <span></span>
                        </div>

                        <a href="{{ url('/') }}"
                           class="btn btn-secondary boton boton-secundario">
                            Cambiar
                        </a>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Cerrar sesión</strong>
                            <span>Salir de tu cuenta actual.</span>
                        </div>

                        <button type="submit" form="logoutForm" class="btn btn-secondary boton boton-secundario">
                            Salir
                        </button>
                    </div>

                    <div class="setting-row">
                        <div class="setting-info">
                            <strong class="etiqueta">Eliminar cuenta 🔥</strong>
                            <span>Elimina tu cuenta y la información asociada.</span>
                        </div>

                        <button type="button" class="btn btn-danger boton boton-peligro"
                                id="openDeleteAccount">
                            Eliminar cuenta
                        </button>
                    </div>
                </section>

            </div>

            <div class="actions">
                <button type="button" class="btn btn-secondary boton boton-secundario"
                        id="resetButton">
                    Restablecer
                </button>

                <button type="submit" class="btn btn-primary boton boton-azul">
                    Guardar cambios
                </button>
            </div>

        </form>

        <form id="logoutForm" action="{{ route('logout') }}" method="POST" hidden>
            @csrf
        </form>

        <dialog class="account-delete-dialog" id="deleteAccountDialog"
                aria-labelledby="deleteAccountTitle">
            <div class="account-delete-content">
                <h2 id="deleteAccountTitle">¿En serio quieres eliminar la cuenta?</h2>
                <p>Se borrará:</p>
                <ul>
                    <li>Perfil</li>
                    <li>Correo</li>
                    <li>Progreso</li>
                    <li>Suscripciones</li>
                    <li>Historial</li>
                    <li>Contraseñas</li>
                </ul>

                <p class="delete-account-notice" id="deleteAccountNotice"
                   role="alert" hidden>
                    <strong>
                    La cuenta se eliminó correctamente. Serás redirigido al login.
                    </strong>
                </p>

                <div class="account-delete-actions">
                    <button type="button" class="btn btn-cancel boton boton-secundario"
                            id="cancelDeleteAccount">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-danger boton boton-peligro"
                            id="confirmDeleteAccount">
                        Confirmar
                    </button>
                </div>
            </div>
        </dialog>

        <footer class="footer">
            FamFit Quest © 2026 · Tu bienestar, tu progreso, tu aventura.
        </footer>

    </main>

</div>

<script>
    const settingsForm = document.getElementById('settingsForm');
    const settingsNotice = document.getElementById('notice');
    const settingsStorageKey = 'famfit-settings';
    const defaultSettings = Object.fromEntries(
        Array.from(settingsForm.elements)
            .filter((field) => field.matches('input, select, textarea'))
            .map((field) => [field.name || field.id, field.type === 'checkbox' ? field.checked : field.value])
    );

    function showSettingsNotice(message, isError) {
        settingsNotice.textContent = message;
        settingsNotice.classList.toggle('alerta-error', isError);
        settingsNotice.classList.toggle('alerta-exito', !isError);
        settingsNotice.style.display = 'block';
    }

    function applySettings(settings) {
        for (const field of settingsForm.elements) {
            const key = field.name || field.id;
            if (!key || !Object.prototype.hasOwnProperty.call(settings, key)) {
                continue;
            }

            if (field.type === 'checkbox') {
                field.checked = Boolean(settings[key]);
            } else {
                field.value = settings[key];
            }
        }

        window.setFamfitDarkMode(document.getElementById('darkMode').checked);
    }

    try {
        const savedSettings = localStorage.getItem(settingsStorageKey);
        if (savedSettings) {
            applySettings(JSON.parse(savedSettings));
        }
    } catch (error) {
        showSettingsNotice('No se pudieron cargar las preferencias guardadas.', true);
    }

    document.getElementById('darkMode').addEventListener('change', (event) => {
        const enabled = event.currentTarget.checked;
        window.setFamfitDarkMode(enabled);

        try {
            const savedSettings = localStorage.getItem(settingsStorageKey);
            const settings = savedSettings ? JSON.parse(savedSettings) : {};

            if (!settings || typeof settings !== 'object' || Array.isArray(settings)) {
                throw new Error('Las preferencias guardadas no tienen un formato válido.');
            }

            settings.darkMode = enabled;
            localStorage.setItem(settingsStorageKey, JSON.stringify(settings));
            showSettingsNotice('Preferencia de modo oscuro guardada.', false);
        } catch (error) {
            showSettingsNotice('No se pudo guardar la preferencia de modo oscuro.', true);
        }
    });

    settingsForm.addEventListener('submit', (event) => {
        event.preventDefault();
        const settings = {};

        for (const field of settingsForm.elements) {
            if (!field.matches('input, select, textarea')) {
                continue;
            }

            const key = field.name || field.id;
            if (key) {
                settings[key] = field.type === 'checkbox' ? field.checked : field.value;
            }
        }

        try {
            localStorage.setItem(settingsStorageKey, JSON.stringify(settings));
            showSettingsNotice('Preferencias guardadas en este navegador.', false);
        } catch (error) {
            showSettingsNotice('No se pudieron guardar las preferencias en este navegador.', true);
        }
    });

    document.getElementById('resetButton').addEventListener('click', () => {
        applySettings(defaultSettings);

        try {
            localStorage.removeItem(settingsStorageKey);
            showSettingsNotice('Preferencias restablecidas.', false);
        } catch (error) {
            showSettingsNotice('No se pudieron restablecer las preferencias guardadas.', true);
        }
    });

    const deleteAccountDialog = document.getElementById('deleteAccountDialog');
    const deleteAccountNotice = document.getElementById('deleteAccountNotice');

    document.getElementById('openDeleteAccount').addEventListener('click', () => {
        deleteAccountNotice.hidden = true;
        deleteAccountDialog.showModal();
    });

    document.getElementById('cancelDeleteAccount').addEventListener('click', () => {
        deleteAccountDialog.close();
    });

    document.getElementById('confirmDeleteAccount').addEventListener('click', () => {
        deleteAccountNotice.hidden = false;
        document.getElementById('confirmDeleteAccount').disabled = true;
        document.getElementById('cancelDeleteAccount').disabled = true;

        window.setTimeout(() => {
            window.location.href = @json(route('login'));
        }, 1500);
    });

    deleteAccountDialog.addEventListener('click', (event) => {
        if (event.target === deleteAccountDialog) {
            deleteAccountDialog.close();
        }
    });
</script>
</body>
</html>