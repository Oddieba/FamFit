(() => {
    const editToggle = document.getElementById('profileEditToggle');
    const editPanel = document.getElementById('profileEditPanel');
    const retakeToggle = document.getElementById('retakeTestToggle');
    const modal = document.getElementById('retakeModal');
    const modalCancel = document.getElementById('retakeModalCancel');

    if (!editToggle || !editPanel || !retakeToggle || !modal || !modalCancel) {
        return;
    }

    let previousFocus = null;

    editToggle.addEventListener('click', () => {
        const isExpanded = editToggle.getAttribute('aria-expanded') === 'true';
        editToggle.setAttribute('aria-expanded', String(!isExpanded));
        editPanel.hidden = isExpanded;

        if (!isExpanded) {
            editPanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
    });

    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        if (previousFocus) {
            previousFocus.focus();
        }
    };

    retakeToggle.addEventListener('click', () => {
        previousFocus = document.activeElement;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        modalCancel.focus();
    });

    modalCancel.addEventListener('click', closeModal);
    modal.addEventListener('click', (event) => {
        if (event.target === modal) {
            closeModal();
        }
    });
    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) {
            closeModal();
        }
    });
})();
