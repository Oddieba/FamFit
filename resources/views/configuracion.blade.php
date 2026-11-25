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
            <p class="fq-settings-note">Al activar «Perfil visible», otras personas pueden encontrarte en Comunidad por tu alias y ver tu nombre público y avatar. Tu horario y tus datos de salud siguen siendo privados.</p>
            <button class="fq-button fq-button-primary" type="submit">Guardar preferencias</button>
        </form>
    </section>
    @endif

    <details class="fq-card fq-settings-card fq-theme-settings-card" id="fqThemeSelector" data-theme-url="{{ route('configuracion.theme') }}" data-theme-current="{{ $theme }}" data-theme-available="{{ $themeAvailable ? 'true' : 'false' }}">
        <summary class="fq-card-heading fq-theme-settings-summary">
            <div><p class="fq-eyebrow">TEMAS DISPONIBLES</p><h2>Apariencia</h2><small>Despliega para ver las 21 opciones y elegir una.</small></div>
            <span class="fq-theme-settings-count">{{ count($themes) }} temas</span>
            <span class="fq-card-icon" aria-hidden="true">🎨</span>
            <i class="fa-solid fa-chevron-down fq-theme-settings-chevron" aria-hidden="true"></i>
        </summary>
        <div class="fq-theme-settings-content">
            <p class="fq-theme-description">Elige una de las 21 apariencias. El cambio se aplica al instante y se guarda en tu cuenta.</p>
            <div class="fq-theme-picker">
                @foreach($themeCategories as $categoryKey => $category)
                    <section class="fq-theme-category fq-theme-category-{{ $categoryKey }}">
                        <div class="fq-theme-category-heading"><h3>{{ $category['name'] }}</h3><p>{{ $category['description'] }}</p></div>
                        <div class="fq-theme-grid">
                            @foreach($category['themes'] as $themeKey)
                                @php
                                    $themeOption = $themes[$themeKey];
                                @endphp
                                <article class="fq-theme-option {{ $theme === $themeKey ? 'is-selected' : '' }}" data-theme-option="{{ $themeKey }}" data-preview-bg="{{ $themeOption['background'] }}" data-preview-surface="{{ $themeOption['surface'] }}" data-preview-accent="{{ $themeOption['accent'] }}" data-preview-text="{{ $themeOption['text'] }}" data-preview-muted="{{ $themeOption['muted'] }}" data-preview-border="{{ $themeOption['border'] }}" data-preview-button-text="{{ \App\Support\AppearanceThemes::isDark($themeKey) ? '#10151D' : '#FFFFFF' }}">
                                    <div class="fq-theme-preview" style="--preview-bg: {{ $themeOption['background'] }}; --preview-surface: {{ $themeOption['surface'] }}; --preview-accent: {{ $themeOption['accent'] }}" aria-hidden="true">
                                        <span class="fq-theme-preview-sidebar"></span>
                                        <span class="fq-theme-preview-content"><i></i><b></b><small></small></span>
                                    </div>
                                    <div class="fq-theme-option-footer">
                                        <div><strong>{{ $themeOption['name'] }}</strong><small>{{ $themeOption['background'] }}</small></div>
                                        <button class="fq-theme-apply" type="button" data-theme-apply="{{ $themeKey }}" aria-pressed="{{ $theme === $themeKey ? 'true' : 'false' }}" @disabled(! $themeAvailable)>
                                            {{ $theme === $themeKey ? 'Actual' : 'Aplicar' }}
                                        </button>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endforeach
            </div>
            <div class="fq-theme-live-preview" id="fqThemeLivePreview" style="--preview-bg: {{ $themes[$theme]['background'] }}; --preview-surface: {{ $themes[$theme]['surface'] }}; --preview-accent: {{ $themes[$theme]['accent'] }}; --preview-text: {{ $themes[$theme]['text'] }}; --preview-muted: {{ $themes[$theme]['muted'] }}; --preview-border: {{ $themes[$theme]['border'] }}; --preview-button-text: {{ \App\Support\AppearanceThemes::isDark($theme) ? '#10151D' : '#FFFFFF' }}" role="img" aria-label="Vista previa del tema actual">
                <span class="fq-theme-live-sidebar" aria-hidden="true"></span>
                <div class="fq-theme-live-content"><small>FAMFIT QUEST</small><strong>Tu próxima aventura</strong><span>Progreso semanal</span><i aria-hidden="true"></i><button type="button" tabindex="-1" aria-hidden="true">Continuar</button></div>
            </div>
            @unless($themeAvailable)
                <p class="fq-settings-note" role="status">Instala las migraciones pendientes para guardar una preferencia por cuenta.</p>
            @endunless
            <p class="fq-settings-note" id="fqThemeStatus" role="status" aria-live="polite" hidden></p>
        </div>
    </details>

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
            
          <a href="{{ route('password.email') }}">¿Olvidaste tu contraseña?</a>
        

        
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
