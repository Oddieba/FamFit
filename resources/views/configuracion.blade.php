@extends('layouts.quest')

@section('title', 'Configuración | FamFit Quest')

@section('content')
    <header class="fq-page-heading"><div><p class="fq-eyebrow">AJUSTA TU EXPERIENCIA</p><h1>Configuración</h1><p class="fq-muted">Preferencias, notificaciones, privacidad y seguridad.</p></div></header>

    @if($settingsAvailable)
    <section class="fq-card fq-settings-card">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">PREFERENCIAS GENERALES</p><h2>Tu experiencia</h2></div><span class="fq-card-icon" aria-hidden="true">⚙️</span></div>
        <form action="{{ route('configuracion.update') }}" method="POST" id="fqSettingsForm">
            @csrf
            @method('PUT')
            <div class="fq-settings-list">
                <input type="hidden" name="notifications_habits" value="0">
                <label class="fq-setting-row" for="settingHabits"><span><strong>Recordatorios de actividades</strong><small>Preferencia para futuros recordatorios de hábitos.</small></span><span class="fq-switch"><input id="settingHabits" type="checkbox" name="notifications_habits" value="1" @checked(old('notifications_habits', $preferences['notifications_habits']))><span aria-hidden="true"></span></span></label>
                <input type="hidden" name="notifications_challenges" value="0">
                <label class="fq-setting-row" for="settingChallenges"><span><strong>Retos y logros</strong><small>Preferencia para avisos de retos cuando estén disponibles.</small></span><span class="fq-switch"><input id="settingChallenges" type="checkbox" name="notifications_challenges" value="1" @checked(old('notifications_challenges', $preferences['notifications_challenges']))><span aria-hidden="true"></span></span></label>
                <div class="fq-setting-row"><label for="reminderTime"><strong>Hora preferida</strong><small>Se usará cuando se habiliten notificaciones.</small></label><input class="fq-input fq-time-input" id="reminderTime" type="time" name="reminder_time" value="{{ old('reminder_time', $preferences['reminder_time']) }}" required></div>
                <div class="fq-setting-row"><label for="waterGoal"><strong>Meta diaria de agua (ml)</strong><small>Preferencia personal de hidratación.</small></label><input class="fq-input fq-time-input" id="waterGoal" type="number" name="water_goal_ml" min="1" max="10000" value="{{ old('water_goal_ml', $preferences['water_goal_ml']) }}" required></div>
                <div class="fq-setting-row"><label for="sleepGoal"><strong>Meta de sueño (horas)</strong><small>Preferencia personal de descanso.</small></label><input class="fq-input fq-time-input" id="sleepGoal" type="number" name="sleep_goal_hours" min="1" max="24" value="{{ old('sleep_goal_hours', $preferences['sleep_goal_hours']) }}" required></div>
                <input type="hidden" name="profile_visible" value="0">
                <label class="fq-setting-row" for="settingPublic"><span><strong>Perfil visible</strong><small>Controla si tu perfil puede mostrarse públicamente en funciones compatibles.</small></span><span class="fq-switch"><input id="settingPublic" type="checkbox" name="profile_visible" value="1" @checked(old('profile_visible', $user->profile_visible))><span aria-hidden="true"></span></span></label>
            </div>
            <p class="fq-settings-note">La visibilidad no comparte datos de salud o actividad automáticamente. El espacio social aún no publica contenido.</p>
            <button class="fq-button fq-button-primary" type="submit">Guardar preferencias</button>
        </form>
    </section>
    @endif

    <section class="fq-card fq-settings-card">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">APARIENCIA</p><h2>Modo oscuro</h2></div><span class="fq-card-icon" aria-hidden="true">🌙</span></div>
        <form action="{{ route('configuracion.theme') }}" method="POST" id="fqThemeForm">
            @csrf
            @method('PUT')
            <input type="hidden" name="dark_mode" value="0">
            <label class="fq-setting-row" for="settingDarkMode">
                <span><strong>Activar modo oscuro</strong><small>Se aplica en todas las páginas de FamFit Quest.</small></span>
                <span class="fq-switch"><input id="settingDarkMode" class="fq-theme-toggle" type="checkbox" name="dark_mode" value="1" @checked($settingsAvailable ? $preferences['dark_mode'] : false)><span aria-hidden="true"></span></span>
            </label>
            @unless($settingsAvailable)
                <p class="fq-settings-note">El tema se conservará en este navegador. Para guardarlo en tu cuenta, instala primero las migraciones pendientes.</p>
            @endunless
        </form>
    </section>

    <section class="fq-card fq-settings-card">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">SEGURIDAD</p><h2>Cambiar contraseña</h2></div><span class="fq-card-icon" aria-hidden="true">🔒</span></div>
        <form action="{{ route('configuracion.password') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="fq-form-grid">
                <div class="fq-form-wide"><label class="fq-field-label" for="currentPassword">Contraseña actual</label><input class="fq-input" id="currentPassword" name="current_password" type="password" autocomplete="current-password" required></div>
                <div><label class="fq-field-label" for="newPassword">Nueva contraseña</label><input class="fq-input" id="newPassword" name="password" type="password" minlength="8" autocomplete="new-password" required></div>
                <div><label class="fq-field-label" for="confirmPassword">Confirmar nueva contraseña</label><input class="fq-input" id="confirmPassword" name="password_confirmation" type="password" minlength="8" autocomplete="new-password" required></div>
            </div>
            <button class="fq-button fq-button-primary" type="submit">Actualizar contraseña</button>
        </form>
    </section>

    <section class="fq-card fq-settings-card fq-danger-zone">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">CUENTA</p><h2>Sesión y cuenta</h2></div><span class="fq-card-icon" aria-hidden="true">🛡️</span></div>
        <div class="fq-account-actions">
            <form action="{{ route('logout') }}" method="POST">@csrf<button class="fq-button fq-button-light" type="submit">Cerrar sesión</button></form>
            <button class="fq-button fq-button-danger" id="openDeleteAccount" type="button">Eliminar cuenta</button>
        </div>
        <p class="fq-muted">La eliminación requiere confirmar tu contraseña y no se puede deshacer.</p>
    </section>

    <dialog class="fq-dialog" id="deleteAccountDialog" aria-labelledby="deleteAccountTitle">
        <form action="{{ route('cuenta.destroy') }}" method="POST">
            @csrf
            @method('DELETE')
            <h2 id="deleteAccountTitle">¿Eliminar tu cuenta?</h2>
            <p class="fq-muted">Se eliminará tu cuenta y la información asociada. Esta acción no se puede deshacer.</p>
            <label class="fq-field-label" for="deleteAccountPassword">Confirma tu contraseña</label>
            <input class="fq-input" id="deleteAccountPassword" name="current_password" type="password" autocomplete="current-password" required>
            @error('current_password')<p class="fq-field-error" role="alert">{{ $message }}</p>@enderror
            <div class="fq-dialog-actions"><button class="fq-button fq-button-light" id="cancelDeleteAccount" type="button">Cancelar</button><button class="fq-button fq-button-danger" type="submit">Confirmar eliminación</button></div>
        </form>
    </dialog>
@endsection
