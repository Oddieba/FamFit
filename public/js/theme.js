(function () {
    const themes = {
        'classic-white': '#FFFFFF',
        'light-gray': '#F0F0F0',
        'pearl-gray': '#E8EAED',
        'sky-blue': '#EAF4FF',
        'ice-blue': '#E8F1F8',
        'mint-green': '#E8F5EC',
        lavender: '#F0ECFA',
        cream: '#F7F1E7',
        'soft-gray': '#343434',
        'deep-gray': '#242424',
        'classic-black': '#121212',
        amoled: '#000000',
        'night-blue': '#17212B',
        'deep-blue': '#101B2D',
        'dark-purple': '#1C1825',
        'forest-green': '#17231D',
        'dark-red': '#251B1B',
        'dark-coffee': '#24201A',
        graphite: '#20252A',
        slate: '#171B20',
        'famfit-original': '#F5F7FB',
    };
    const darkThemes = new Set([
        'soft-gray', 'deep-gray', 'classic-black', 'amoled', 'night-blue', 'deep-blue',
        'dark-purple', 'forest-green', 'dark-red', 'dark-coffee', 'graphite', 'slate',
    ]);
    const root = document.documentElement;
    let currentTheme = resolveTheme(root.dataset.fqTheme);

    function resolveTheme(theme) {
        return Object.prototype.hasOwnProperty.call(themes, theme) ? theme : 'famfit-original';
    }

    function applyTheme(theme) {
        const selectedTheme = resolveTheme(theme);
        root.dataset.fqTheme = selectedTheme;
        root.classList.toggle('theme-dark', darkThemes.has(selectedTheme));
        currentTheme = selectedTheme;

        const themeColor = document.querySelector('meta[name="theme-color"]');
        if (themeColor) {
            themeColor.setAttribute('content', themes[selectedTheme]);
        }

        return selectedTheme;
    }

    function updateThemeOptions(theme) {
        const selector = document.getElementById('fqThemeSelector');
        if (!selector) return;

        selector.dataset.themeCurrent = theme;
        const livePreview = document.getElementById('fqThemeLivePreview');
        const selectedOption = Array.from(selector.querySelectorAll('[data-theme-option]'))
            .find(function (option) { return option.dataset.themeOption === theme; });
        if (selectedOption && livePreview) {
            livePreview.style.setProperty('--preview-bg', selectedOption.dataset.previewBg);
            livePreview.style.setProperty('--preview-surface', selectedOption.dataset.previewSurface);
            livePreview.style.setProperty('--preview-accent', selectedOption.dataset.previewAccent);
            livePreview.style.setProperty('--preview-text', selectedOption.dataset.previewText);
            livePreview.style.setProperty('--preview-muted', selectedOption.dataset.previewMuted);
            livePreview.style.setProperty('--preview-border', selectedOption.dataset.previewBorder);
            livePreview.style.setProperty('--preview-button-text', selectedOption.dataset.previewButtonText);
            livePreview.setAttribute('aria-label', 'Vista previa del tema: ' + selectedOption.querySelector('strong').textContent);
        }

        selector.querySelectorAll('[data-theme-option]').forEach(function (option) {
            const isSelected = option.dataset.themeOption === theme;
            option.classList.toggle('is-selected', isSelected);
            const button = option.querySelector('[data-theme-apply]');
            if (button) {
                button.setAttribute('aria-pressed', String(isSelected));
                button.textContent = isSelected ? 'Actual' : 'Aplicar';
            }
        });
    }

    function showStatus(message, isError) {
        const status = document.getElementById('fqThemeStatus');
        if (!status) return;

        status.hidden = !message;
        status.textContent = message;
        status.classList.toggle('is-error', Boolean(isError));
    }

    async function saveTheme(theme) {
        if (!Object.prototype.hasOwnProperty.call(themes, theme)) {
            return false;
        }

        const selector = document.getElementById('fqThemeSelector');
        if (!selector || selector.dataset.themeAvailable !== 'true') {
            showStatus('No se puede guardar el tema hasta instalar la migración correspondiente.', true);
            return false;
        }

        const previousTheme = currentTheme;
        applyTheme(theme);
        updateThemeOptions(theme);
        showStatus('Guardando tema…', false);

        const buttons = Array.from(selector.querySelectorAll('[data-theme-apply]'));
        buttons.forEach(function (button) {
            button.disabled = true;
        });

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
            if (!csrfToken) {
                throw new Error('No se encontró el token de seguridad de la sesión.');
            }

            const response = await fetch(selector.dataset.themeUrl, {
                method: 'PUT',
                credentials: 'same-origin',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ theme: theme }),
            });
            let result = {};
            try {
                result = await response.json();
            } catch (error) {
                result = {};
            }
            if (!response.ok) {
                throw new Error(result.message || 'No se pudo guardar el tema. Inténtalo de nuevo.');
            }

            applyTheme(result.theme);
            updateThemeOptions(result.theme);
            showStatus('Tema guardado en tu cuenta.', false);
            return true;
        } catch (error) {
            applyTheme(previousTheme);
            updateThemeOptions(previousTheme);
            showStatus(error.message || 'No se pudo guardar el tema. Inténtalo de nuevo.', true);
            console.error('No se pudo guardar la preferencia de tema.', error);
            return false;
        } finally {
            buttons.forEach(function (button) {
                button.disabled = false;
            });
        }
    }

    window.setFamfitTheme = function (theme) {
        if (!Object.prototype.hasOwnProperty.call(themes, theme)) return false;
        applyTheme(theme);
        updateThemeOptions(theme);
        return true;
    };
    window.saveFamfitTheme = saveTheme;
    window.setFamfitDarkMode = function (enabled) {
        applyTheme(enabled ? 'classic-black' : 'famfit-original');
        updateThemeOptions(currentTheme);
    };
    window.saveFamfitDarkMode = function (enabled) {
        return saveTheme(enabled ? 'classic-black' : 'famfit-original');
    };

    applyTheme(currentTheme);
    document.addEventListener('DOMContentLoaded', function () {
        const selector = document.getElementById('fqThemeSelector');
        if (!selector) return;

        updateThemeOptions(currentTheme);
        if (selector.dataset.themeAvailable !== 'true') {
            selector.querySelectorAll('[data-theme-apply]').forEach(function (button) {
                button.disabled = true;
            });
            return;
        }

        selector.addEventListener('click', function (event) {
            const button = event.target.closest('[data-theme-apply]');
            if (button && selector.contains(button)) {
                saveTheme(button.dataset.themeApply);
            }
        });
    });
})();
