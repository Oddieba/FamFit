(() => {
    const modal = document.getElementById('activityModal');
    const form = document.getElementById('activityForm');
    if (!modal || !form) return;

    const fields = {
        method: document.getElementById('formMethod'),
        title: document.getElementById('activityTitle'),
        type: document.getElementById('activityType'),
        date: document.getElementById('activityDate'),
        start: document.getElementById('startTime'),
        end: document.getElementById('endTime'),
        status: document.getElementById('activityStatus'),
        notes: document.getElementById('activityNotes'),
        titleModal: document.getElementById('modalTitle'),
        save: document.getElementById('saveActivityBtn'),
        availabilityFeedback: document.getElementById('availabilityFeedback'),
        deleteForm: document.getElementById('deleteForm'),
    };
    const defaultDate = form.dataset.today;
    const availability = JSON.parse(form.dataset.availability || '{}');
    const availabilitySlots = JSON.parse(form.dataset.availabilitySlots || '{}');
    const availabilityDayKeys = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
    const hasConfiguredAvailability = Object.values(availability).some(slots => Array.isArray(slots) && slots.length > 0);
    let returnFocusTo = null;
    let originalSchedule = null;

    const openModal = () => {
        returnFocusTo = document.activeElement;
        modal.classList.add('is-open');
        modal.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        window.setTimeout(() => fields.title.focus(), 50);
    };
    const closeModal = () => {
        modal.classList.remove('is-open');
        modal.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
        if (returnFocusTo instanceof HTMLElement) returnFocusTo.focus();
    };
    const scheduleIsUnchanged = () => originalSchedule
        && fields.date.value === originalSchedule.date
        && fields.start.value === originalSchedule.start
        && fields.end.value === originalSchedule.end;
    const scheduleIsAvailable = () => {
        if (!hasConfiguredAvailability) return true;

        const [year, month, day] = fields.date.value.split('-').map(Number);
        const dayKey = availabilityDayKeys[new Date(year, month - 1, day, 12).getDay()];
        const selectedSlots = availability[dayKey] || [];
        const [startHour, startMinute] = fields.start.value.split(':').map(Number);
        const [endHour, endMinute] = fields.end.value.split(':').map(Number);
        const start = startHour * 60 + startMinute;
        const end = endHour * 60 + endMinute;
        let cursor = start;

        for (const [slotKey, slot] of Object.entries(availabilitySlots)) {
            if (!selectedSlots.includes(slotKey) || slot.end <= cursor) continue;
            if (slot.start > cursor) break;

            cursor = Math.max(cursor, slot.end);
            if (cursor >= end) return true;
        }

        return false;
    };
    const scheduleHasConflict = () => {
        if (!fields.date.value || !fields.start.value || !fields.end.value) return false;

        return [...document.querySelectorAll('[data-activity]')].some(button => {
            const activity = JSON.parse(button.dataset.activity);
            if (activity.activity_date.substring(0, 10) !== fields.date.value) return false;
            if (originalSchedule
                && activity.activity_date.substring(0, 10) === originalSchedule.date
                && activity.start_time.substring(0, 5) === originalSchedule.start
                && activity.end_time.substring(0, 5) === originalSchedule.end) {
                return false;
            }

            return fields.start.value < activity.end_time.substring(0, 5)
                && fields.end.value > activity.start_time.substring(0, 5);
        });
    };
    const updateAvailabilityFeedback = () => {
        const conflict = scheduleHasConflict();

        if (conflict) {
            fields.availabilityFeedback.textContent = 'Ya tienes otra actividad en ese horario. Cambia la hora antes de guardar.';
            return false;
        }

        fields.availabilityFeedback.textContent = 'Puedes programar la actividad en cualquier día y horario.';
        return true;
    };
    const resetForm = (date = defaultDate) => {
        form.reset();
        form.action = form.dataset.storeUrl;
        originalSchedule = null;
        fields.method.value = 'POST';
        fields.date.value = date;
        fields.start.value = '09:00';
        fields.end.value = '10:00';
        fields.end.min = fields.start.value;
        fields.status.value = 'planned';
        fields.titleModal.textContent = 'Nueva actividad';
        fields.save.textContent = 'Guardar actividad';
        fields.deleteForm.hidden = true;
        fields.deleteForm.removeAttribute('action');
        updateAvailabilityFeedback();
    };
    document.querySelectorAll('[data-open-create]').forEach(button => {
        button.addEventListener('click', () => {
            resetForm();
            openModal();
        });
    });
    document.querySelectorAll('[data-close-modal]').forEach(button => {
        button.addEventListener('click', closeModal);
    });
    modal.addEventListener('click', event => {
        if (event.target === modal) closeModal();
    });
    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });

    document.querySelectorAll('[data-activity][data-update-url]').forEach(button => {
        button.addEventListener('click', () => {
            const activity = JSON.parse(button.dataset.activity);
            resetForm(activity.activity_date.substring(0, 10));
            form.action = button.dataset.updateUrl;
            fields.method.value = 'PUT';
            fields.title.value = activity.title;
            fields.type.value = activity.activity_type;
            fields.date.value = activity.activity_date.substring(0, 10);
            fields.start.value = activity.start_time.substring(0, 5);
            fields.end.value = activity.end_time.substring(0, 5);
            originalSchedule = {
                date: fields.date.value,
                start: fields.start.value,
                end: fields.end.value,
            };
            fields.end.min = fields.start.value;
            fields.status.value = activity.status;
            fields.notes.value = activity.notes || '';
            fields.titleModal.textContent = 'Editar actividad';
            fields.save.textContent = 'Guardar cambios';
            fields.deleteForm.action = button.dataset.deleteUrl;
            fields.deleteForm.hidden = false;
            updateAvailabilityFeedback();
            openModal();
        });
    });

    const clearEndTimeError = () => {
        fields.end.setCustomValidity('');
        fields.end.min = fields.start.value;
    };
    fields.start.addEventListener('input', clearEndTimeError);
    fields.end.addEventListener('input', clearEndTimeError);
    [fields.date, fields.start, fields.end].forEach(field => {
        field.addEventListener('input', updateAvailabilityFeedback);
        field.addEventListener('change', updateAvailabilityFeedback);
    });
    form.addEventListener('submit', event => {
        if (fields.end.value <= fields.start.value) {
            fields.end.setCustomValidity('La hora de finalización debe ser posterior a la de inicio.');
            fields.end.reportValidity();
            event.preventDefault();
            return;
        }
    });

    const zoomOut = document.querySelector('[data-calendar-zoom="out"]');
    const zoomIn = document.querySelector('[data-calendar-zoom="in"]');
    const zoomReset = document.querySelector('[data-calendar-zoom-reset]');
    const zoomLevel = document.getElementById('calendarZoomLevel');
    const baseHourHeight = Number.parseFloat(
        getComputedStyle(document.documentElement).getPropertyValue('--ff-hour-height')
    );
    const minHourHeight = 12;
    const maxHourHeight = 40;
    const zoomStep = 4;
    let hourHeight = baseHourHeight;

    const updateZoom = () => {
        document.documentElement.style.setProperty('--ff-hour-height', `${hourHeight}px`);
        zoomLevel.value = `${Math.round(hourHeight / baseHourHeight * 100)}%`;
        zoomOut.disabled = hourHeight <= minHourHeight;
        zoomIn.disabled = hourHeight >= maxHourHeight;
    };

    zoomOut.addEventListener('click', () => {
        hourHeight = Math.max(minHourHeight, hourHeight - zoomStep);
        updateZoom();
    });
    zoomIn.addEventListener('click', () => {
        hourHeight = Math.min(maxHourHeight, hourHeight + zoomStep);
        updateZoom();
    });
    zoomReset.addEventListener('click', () => {
        hourHeight = baseHourHeight;
        updateZoom();
    });

    updateZoom();
})();
