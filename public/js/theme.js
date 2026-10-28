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
            setDarkMode(false);
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

    try {
        readDarkMode(localStorage.getItem(settingsStorageKey));
    } catch (error) {
        console.warn('No se pudo acceder a la preferencia de tema guardada.');
    }

    window.addEventListener('storage', function (event) {
        if (event.key === settingsStorageKey) {
            readDarkMode(event.newValue);
        }
    });
})();
