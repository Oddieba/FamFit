document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('fqSidebar');
    const menuToggle = document.getElementById('fqMenuToggle');
    const mobileToggle = document.getElementById('fqMobileToggle');
    const overlay = document.getElementById('fqOverlay');

    function setMenuOpen(open) {
        if (!sidebar) return;
        const mobile = window.matchMedia('(max-width: 760px)').matches;
        sidebar.classList.toggle(mobile ? 'is-mobile-open' : 'is-expanded', open);
        overlay?.classList.toggle('is-visible', mobile && open);
        menuToggle?.setAttribute('aria-expanded', String(open));
        mobileToggle?.setAttribute('aria-expanded', String(open));
        mobileToggle?.setAttribute('aria-label', open ? 'Cerrar menú' : 'Abrir menú');
    }

    menuToggle?.addEventListener('click', () => {
        const expanded = sidebar?.classList.contains('is-expanded') ?? false;
        setMenuOpen(!expanded);
    });
    mobileToggle?.addEventListener('click', () => {
        const open = sidebar?.classList.contains('is-mobile-open') ?? false;
        setMenuOpen(!open);
    });
    overlay?.addEventListener('click', () => setMenuOpen(false));
    sidebar?.querySelectorAll('.fq-nav-link').forEach((link) => {
        link.addEventListener('click', () => {
            if (window.matchMedia('(max-width: 760px)').matches) setMenuOpen(false);
        });
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') setMenuOpen(false);
    });
    window.addEventListener('resize', () => {
        if (!window.matchMedia('(max-width: 760px)').matches) {
            sidebar?.classList.remove('is-mobile-open');
            overlay?.classList.remove('is-visible');
            mobileToggle?.setAttribute('aria-expanded', 'false');
        } else {
            sidebar?.classList.remove('is-expanded');
            menuToggle?.setAttribute('aria-expanded', 'false');
        }
    });

    document.querySelectorAll('[data-toggle-panel]').forEach((button) => {
        button.addEventListener('click', () => {
            const panel = document.getElementById(button.dataset.togglePanel);
            if (!panel) return;
            const open = panel.hidden;
            panel.hidden = !open;
            button.setAttribute('aria-expanded', String(open));
            if (open) panel.querySelector('input, select')?.focus();
        });
    });

    const avatarForm = document.getElementById('fqAvatarForm');
    if (avatarForm) {
        const animalPreview = document.getElementById('fqAnimalPreview');
        const accessoryPreview = document.getElementById('fqAccessoryPreview');
        const avatarArt = document.getElementById('fqAvatarPreview');
        const accessorySymbols = { Ninguno: '', Gafas: '🕶️', Gorra: '🧢', Estrella: '⭐' };

        avatarForm.addEventListener('change', (event) => {
            const field = event.target;
            if (field.name === 'animal' && animalPreview) {
                animalPreview.textContent = field.value;
                animalPreview.setAttribute('aria-label', `Avatar ${field.value}`);
            }
            if (field.name === 'outfit' && avatarArt) {
                avatarArt.style.setProperty('--fq-outfit', field.value);
            }
            if (field.name === 'accessory' && accessoryPreview) {
                accessoryPreview.textContent = accessorySymbols[field.value] ?? '';
                accessoryPreview.hidden = !accessoryPreview.textContent;
            }
        });
    }

    const exerciseFields = document.querySelector('[data-exercise-fields]');
    const exerciseTemplate = document.getElementById('fqExerciseTemplate');
    const addExerciseButton = document.querySelector('[data-add-exercise]');
    if (exerciseFields && exerciseTemplate && addExerciseButton) {
        addExerciseButton.addEventListener('click', () => {
            const count = exerciseFields.querySelectorAll('.fq-exercise-editor').length;
            if (count >= 5) return;
            const editor = exerciseTemplate.content.firstElementChild.cloneNode(true);
            editor.querySelectorAll('[data-field]').forEach((field) => {
                field.name = `exercises[${count}][${field.dataset.field}]`;
            });
            exerciseFields.append(editor);
            addExerciseButton.disabled = count + 1 >= 5;
            editor.querySelector('select')?.focus();
        });

        exerciseFields.addEventListener('click', (event) => {
            const removeButton = event.target.closest('[data-remove-exercise]');
            if (!removeButton) return;
            removeButton.closest('.fq-exercise-editor').remove();
            exerciseFields.querySelectorAll('.fq-exercise-editor').forEach((editor, index) => {
                editor.querySelectorAll('[data-field]').forEach((field) => {
                    field.name = `exercises[${index}][${field.dataset.field}]`;
                });
            });
            addExerciseButton.disabled = false;
        });
    }

    const deleteDialog = document.getElementById('deleteAccountDialog');
    document.getElementById('openDeleteAccount')?.addEventListener('click', () => deleteDialog?.showModal());
    document.getElementById('cancelDeleteAccount')?.addEventListener('click', () => deleteDialog?.close());
    deleteDialog?.addEventListener('click', (event) => {
        if (event.target === deleteDialog) deleteDialog.close();
    });
    if (deleteDialog && document.querySelector('.fq-field-error')) deleteDialog.showModal();
});
