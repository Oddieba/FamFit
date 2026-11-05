(function () {
    const settingsStorageKey = 'famfit-settings';
    const root = document.documentElement;

    function setDarkMode(enabled) {
        root.classList.toggle('theme-dark', enabled);
        root.classList.toggle('dark', enabled);

        if (document.body) {
            document.body.classList.toggle('dark', enabled);
        }

        const themeColor = document.querySelector('meta[name="theme-color"]');
        if (themeColor) {
            themeColor.setAttribute('content', enabled ? '#0f172a' : '#F5F7FB');
        }
    }

    function readDarkMode(value) {
        if (value === null) {
            setDarkMode(root.classList.contains('theme-dark'));
            return;
        }

        try {
            const settings = JSON.parse(value);
            setDarkMode(Boolean(settings && settings.darkMode === true));
        } catch (error) {
            console.warn('No se pudo leer la preferencia de tema guardada.');
        }
    }

    window.setFamfitDarkMode = setDarkMode;
    window.saveFamfitDarkMode = function (enabled) {
        try {
            const saved = localStorage.getItem(settingsStorageKey);
            const settings = saved ? JSON.parse(saved) : {};

            if (!settings || typeof settings !== 'object' || Array.isArray(settings)) {
                throw new Error('Las preferencias guardadas no tienen un formato válido.');
            }

            settings.darkMode = Boolean(enabled);
            localStorage.setItem(settingsStorageKey, JSON.stringify(settings));
            setDarkMode(Boolean(enabled));
            return true;
        } catch (error) {
            console.error('No se pudo guardar la preferencia de modo oscuro.', error);
            return false;
        }
    };

    try {
        const serverPreference = root.dataset.fqDarkMode;
        if (serverPreference === 'true' || serverPreference === 'false') {
            window.saveFamfitDarkMode(serverPreference === 'true');
        } else {
            readDarkMode(localStorage.getItem(settingsStorageKey));
        }
    } catch (error) {
        console.warn('No se pudo acceder a la preferencia de tema guardada.');
    }

    window.addEventListener('storage', function (event) {
        if (event.key === settingsStorageKey) {
            readDarkMode(event.newValue);
        }
    });

    document.addEventListener('DOMContentLoaded', function () {
        const toggle = document.querySelector('.fq-theme-toggle');
        const form = document.getElementById('fqThemeForm');

        if (!toggle) return;

        try {
            const saved = localStorage.getItem(settingsStorageKey);
            if (saved) {
                const settings = JSON.parse(saved);
                if (settings && typeof settings.darkMode === 'boolean') {
                    toggle.checked = settings.darkMode;
                } else {
                    toggle.checked = root.classList.contains('theme-dark');
                }
            } else {
                toggle.checked = root.classList.contains('theme-dark');
            }
        } catch (error) {
            console.error('No se pudo cargar la preferencia de modo oscuro.', error);
            toggle.checked = root.classList.contains('theme-dark');
        }

        toggle.addEventListener('change', function () {
            if (!window.saveFamfitDarkMode(toggle.checked)) {
                toggle.checked = !toggle.checked;
                return;
            }

            if (form) form.requestSubmit();
        });
    });
})();
