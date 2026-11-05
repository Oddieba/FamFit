/* ========================================
   FAMFIT QUEST - MENÚ Y PERFIL
   ======================================== */

document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('fqSidebar');
    const mobileToggle = document.getElementById('fqMobileToggle');
    const overlay = document.getElementById('fqOverlay');

    const avatarButton = document.getElementById('fqAvatarButton');
    const avatarDisplay = document.getElementById('fqAvatarDisplay');
    const avatarPicker = document.getElementById('fqAvatarPicker');

    const nameInput = document.getElementById('fqNameInput');
    const levelInput = document.getElementById('fqLevelInput');
    const saveButton = document.getElementById('fqSaveProfile');

    const profileName = document.getElementById('fqProfileName');
    const profileLevel = document.getElementById('fqProfileLevel');

    if (!sidebar) return;

    const STORAGE_KEY = 'famfitQuestProfile';

    const defaultProfile = {
        avatar: '🦊',
        name: profileName?.textContent.trim() || 'Mi perfil',
        level: 1
    };

    let profile = { ...defaultProfile };

    // Leer las preferencias guardadas.
    try {
        const saved = localStorage.getItem(STORAGE_KEY);

        if (saved) {
            const parsed = JSON.parse(saved);

            if (
                parsed &&
                typeof parsed.avatar === 'string' &&
                typeof parsed.name === 'string' &&
                Number.isInteger(parsed.level) &&
                parsed.level >= 1 &&
                parsed.level <= 999
            ) {
                profile = {
                    avatar: parsed.avatar,
                    name: parsed.name.slice(0, 30),
                    level: parsed.level
                };
            }
        }
    } catch (error) {
        console.warn('No se pudieron recuperar las preferencias del perfil.');
    }

    function renderProfile() {
        if (avatarDisplay) avatarDisplay.textContent = profile.avatar;
        if (profileName) profileName.textContent = profile.name;
        if (profileLevel) profileLevel.textContent = `Nivel ${profile.level}`;

        if (nameInput) nameInput.value = profile.name;
        if (levelInput) levelInput.value = profile.level;

        document.querySelectorAll('.fq-avatar-option').forEach(function (option) {
            const selected = option.dataset.avatar === profile.avatar;

            option.classList.toggle('selected', selected);
            option.setAttribute('aria-pressed', String(selected));
        });
    }

    function saveProfile() {
        try {
            localStorage.setItem(STORAGE_KEY, JSON.stringify(profile));
            return true;
        } catch (error) {
            console.warn('No se pudo guardar el perfil en este navegador.');
            return false;
        }
    }

    renderProfile();

    // El selector del avatar se abre desde el perfil.
    if (avatarButton && avatarPicker) {
        avatarButton.addEventListener('click', function () {
            const willOpen = avatarPicker.hidden;

            avatarPicker.hidden = !willOpen;
            avatarButton.setAttribute('aria-expanded', String(willOpen));

            if (willOpen) {
                nameInput?.focus();
            }
        });
    }

    // Elegir un avatar.
    document.querySelectorAll('.fq-avatar-option').forEach(function (option) {
        option.addEventListener('click', function () {
            profile.avatar = option.dataset.avatar || defaultProfile.avatar;

            renderProfile();
            saveProfile();
        });
    });

    // Guardar nombre y nivel personalizados.
    if (saveButton) {
        saveButton.addEventListener('click', function () {
            const newName = nameInput.value.trim();
            const newLevel = Number(levelInput.value);

            if (!newName) {
                nameInput.setCustomValidity('Escribe un nombre para tu perfil.');
                nameInput.reportValidity();
                nameInput.setCustomValidity('');
                nameInput.focus();
                return;
            }

            if (
                !Number.isInteger(newLevel) ||
                newLevel < 1 ||
                newLevel > 999
            ) {
                levelInput.setCustomValidity('El nivel debe estar entre 1 y 999.');
                levelInput.reportValidity();
                levelInput.setCustomValidity('');
                levelInput.focus();
                return;
            }

            profile.name = newName.slice(0, 30);
            profile.level = newLevel;

            renderProfile();
            saveProfile();

            avatarPicker.hidden = true;
            avatarButton?.setAttribute('aria-expanded', 'false');
        });
    }

    // Abrir y cerrar la barra lateral en móviles.
    function openMobileMenu() {
        sidebar.classList.add('is-mobile-open');
        overlay?.classList.add('is-visible');
        document.body.classList.add('fq-menu-open');

        mobileToggle?.setAttribute('aria-expanded', 'true');
    }

    function closeMobileMenu() {
        sidebar.classList.remove('is-mobile-open');
        overlay?.classList.remove('is-visible');
        document.body.classList.remove('fq-menu-open');

        mobileToggle?.setAttribute('aria-expanded', 'false');
    }

    mobileToggle?.addEventListener('click', function () {
        if (sidebar.classList.contains('is-mobile-open')) {
            closeMobileMenu();
        } else {
            openMobileMenu();
        }
    });

    overlay?.addEventListener('click', closeMobileMenu);

    // Cerrar el selector al pulsar fuera de él.
    document.addEventListener('click', function (event) {
        if (
            avatarPicker &&
            !avatarPicker.hidden &&
            !avatarPicker.contains(event.target) &&
            !avatarButton?.contains(event.target)
        ) {
            avatarPicker.hidden = true;
            avatarButton?.setAttribute('aria-expanded', 'false');
        }
    });

    // Cerrar ventanas con Escape.
    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            if (avatarPicker && !avatarPicker.hidden) {
                avatarPicker.hidden = true;
                avatarButton?.setAttribute('aria-expanded', 'false');
            } else {
                closeMobileMenu();
            }
        }
    });

    // Evitar que los enlaces pendientes naveguen a "#".
    sidebar.querySelectorAll('[data-pending="true"]').forEach(function (link) {
        link.addEventListener('click', function (event) {
            event.preventDefault();
        });
    });

    // Si se vuelve a escritorio, cerrar el estado móvil.
    window.addEventListener('resize', function () {
        if (window.innerWidth > 768) {
            closeMobileMenu();
        }
    });
});