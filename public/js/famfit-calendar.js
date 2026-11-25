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
        reminder: document.getElementById('activityReminder'),
        notes: document.getElementById('activityNotes'),
        scheduleGoal: document.getElementById('scheduleGoal'),
        scheduleDuration: document.getElementById('scheduleDuration'),
        scheduleExperience: document.getElementById('scheduleExperience'),
        suggestSchedule: document.getElementById('suggestScheduleBtn'),
        scheduleSuggestions: document.getElementById('scheduleSuggestions'),
        titleModal: document.getElementById('modalTitle'),
        save: document.getElementById('saveActivityBtn'),
        availabilityFeedback: document.getElementById('availabilityFeedback'),
        deleteForm: document.getElementById('deleteForm'),
    };
    const defaultDate = form.dataset.today;
    const availability = JSON.parse(form.dataset.availability || '{}');
    const availabilitySlots = JSON.parse(form.dataset.availabilitySlots || '{}');
    const reminderActivities = JSON.parse(form.dataset.reminders || '[]');
    const notificationPreferences = JSON.parse(form.dataset.notificationPreferences || '{}');
    const reminderPermissionStatus = document.getElementById('reminderPermissionStatus');
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
        fields.reminder.value = '';
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
            fields.reminder.value = activity.reminder_minutes_before?.toString() || '';
            fields.notes.value = activity.notes || '';
            fields.titleModal.textContent = 'Editar actividad';
            fields.save.textContent = 'Guardar cambios';
            fields.deleteForm.action = button.dataset.deleteUrl;
            fields.deleteForm.hidden = false;
            updateAvailabilityFeedback();
            openModal();
        });
    });

    const toTime = minutes => `${String(Math.floor(minutes / 60)).padStart(2, '0')}:${String(minutes % 60).padStart(2, '0')}`;
    const goalConfig = {
        strength: { type: 'Ejercicio', title: 'Entrenamiento de fuerza' },
        endurance: { type: 'Caminata', title: 'Actividad para quemar grasa' },
        cardio: { type: 'Ciclismo', title: 'Entrenamiento cardiovascular' },
        mobility: { type: 'Yoga', title: 'Sesión de movilidad' },
        general_fitness: { type: 'Ejercicio', title: 'Actividad física general' },
    };
    const getExistingIntervals = date => [...document.querySelectorAll('[data-activity]')]
        .map(button => JSON.parse(button.dataset.activity))
        .filter(activity => activity.activity_date.substring(0, 10) === date)
        .filter(activity => !(originalSchedule
            && activity.activity_date.substring(0, 10) === originalSchedule.date
            && activity.start_time.substring(0, 5) === originalSchedule.start
            && activity.end_time.substring(0, 5) === originalSchedule.end))
        .map(activity => ({
            start: Number(activity.start_time.substring(0, 2)) * 60 + Number(activity.start_time.substring(3, 5)),
            end: Number(activity.end_time.substring(0, 2)) * 60 + Number(activity.end_time.substring(3, 5)),
        }));

    fields.suggestSchedule.addEventListener('click', () => {
        const duration = Number(fields.scheduleDuration.value);
        const experience = fields.scheduleExperience.value;
        const config = goalConfig[fields.scheduleGoal.value] || goalConfig.general_fitness;
        const today = new Date(`${defaultDate}T12:00:00`);
        const suggestions = [];
        const dayKeys = ['domingo', 'lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado'];
        const preferredStart = experience === 'beginner'
            ? ['mañana', 'tarde', 'noche']
            : experience === 'advanced'
                ? ['tarde', 'noche', 'mañana']
                : ['mañana', 'tarde', 'noche'];

        for (let offset = 0; offset < 14 && suggestions.length < 4; offset++) {
            const date = new Date(today);
            date.setDate(today.getDate() + offset);
            const dateValue = `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
            const dayKey = dayKeys[date.getDay()];
            const selectedSlots = availability[dayKey] || [];
            let windows = preferredStart
                .filter(slot => selectedSlots.includes(slot))
                .map(slot => availabilitySlots[slot])
                .filter(Boolean);

            if (windows.length === 0) {
                windows = experience === 'beginner'
                    ? [{ start: 360, end: 720 }, { start: 720, end: 1080 }]
                    : [{ start: 360, end: 720 }, { start: 720, end: 1080 }, { start: 1080, end: 1440 }];
            }

            const occupied = getExistingIntervals(dateValue);
            for (const window of windows) {
                for (let start = Math.max(window.start, 360); start + duration <= window.end && suggestions.length < 4; start += 15) {
                    const end = start + duration;
                    const isFree = occupied.every(interval => end <= interval.start || start >= interval.end);
                    if (isFree) {
                        suggestions.push({ dateValue, date, start, end, config });
                        break;
                    }
                }
            }
        }

        if (suggestions.length === 0) {
            fields.scheduleSuggestions.innerHTML = '<p class="ff-no-suggestions">No hay espacio libre para esta duración en los próximos 14 días.</p>';
            return;
        }

        fields.scheduleSuggestions.innerHTML = suggestions.map((suggestion, index) => {
            const dateLabel = suggestion.date.toLocaleDateString('es-ES', { weekday: 'short', day: 'numeric', month: 'short' });
            return `<button type="button" class="ff-schedule-option" data-suggestion="${index}">
                <strong>${dateLabel}</strong>
                <span>${toTime(suggestion.start)} – ${toTime(suggestion.end)} · ${duration} min</span>
            </button>`;
        }).join('');

        fields.scheduleSuggestions.querySelectorAll('[data-suggestion]').forEach(button => {
            button.addEventListener('click', () => {
                const suggestion = suggestions[Number(button.dataset.suggestion)];
                fields.date.value = suggestion.dateValue;
                fields.start.value = toTime(suggestion.start);
                fields.end.value = toTime(suggestion.end);
                fields.end.min = fields.start.value;
                fields.type.value = suggestion.config.type;
                if (fields.method.value === 'POST' || fields.title.value.trim() === '') {
                    fields.title.value = suggestion.config.title;
                }
                fields.scheduleSuggestions.querySelectorAll('[data-suggestion]').forEach(option => option.classList.remove('is-selected'));
                button.classList.add('is-selected');
                updateAvailabilityFeedback();
            });
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

    const notificationStorageKey = 'famfit-calendar-sent-reminders';
    let reminderStorageUnavailable = false;
    const getSentReminders = () => {
        try {
            const sentReminders = JSON.parse(localStorage.getItem(notificationStorageKey) || '[]');
            return new Set(Array.isArray(sentReminders) ? sentReminders : []);
        } catch (error) {
            reminderStorageUnavailable = true;
            return new Set();
        }
    };
    const sentReminders = getSentReminders();
    let toastContainer = document.getElementById('famfitReminderToasts');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'famfitReminderToasts';
        toastContainer.className = 'ff-reminder-toasts';
        toastContainer.setAttribute('aria-live', 'polite');
        toastContainer.setAttribute('aria-atomic', 'true');
        document.body.appendChild(toastContainer);
    }

    const showInAppReminder = activity => {
        const toast = document.createElement('article');
        toast.className = 'ff-reminder-toast';
        const icon = document.createElement('span');
        icon.className = 'ff-reminder-toast-icon';
        icon.setAttribute('aria-hidden', 'true');
        icon.textContent = '🔔';
        const copy = document.createElement('div');
        const title = document.createElement('strong');
        title.textContent = `Recordatorio: ${activity.title}`;
        const detail = document.createElement('span');
        detail.textContent = `Tu actividad comienza a las ${activity.start_time.substring(0, 5)}.`;
        copy.append(title, detail);
        toast.append(icon, copy);
        toastContainer.appendChild(toast);
        window.setTimeout(() => toast.remove(), 10000);
    };

    const setReminderStatus = (message, state = 'active') => {
        reminderPermissionStatus.className = `ff-reminder-status is-${state}`;
        reminderPermissionStatus.innerHTML = `<span class="ff-reminder-status-dot" aria-hidden="true"></span><span>${message}</span>`;
    };

    const updateReminderStatus = () => {
        if (!notificationPreferences.habits_enabled) {
            setReminderStatus('Recordatorios de actividades desactivados en Configuración.', 'paused');
            return;
        }
        if (!('Notification' in window)) {
            setReminderStatus('Usando avisos dentro de FamFit en este navegador.', 'app');
            return;
        }
        if (!window.isSecureContext) {
            setReminderStatus('Usando avisos dentro de FamFit; el navegador requiere HTTPS.', 'app');
            return;
        }
        if (Notification.permission === 'granted') {
            setReminderStatus(reminderStorageUnavailable
                ? 'Notificaciones automáticas activas.'
                : 'Notificaciones automáticas activas; canal disponible.');
            return;
        }
        if (Notification.permission === 'denied') {
            setReminderStatus('El canal del sistema está bloqueado; FamFit mostrará avisos internos.', 'app');
            return;
        }
        setReminderStatus('Notificaciones automáticas listas; se solicitará permiso al usar la página.', 'app');
    };
    const checkReminders = () => {
        if (!notificationPreferences.habits_enabled) return;

        const now = Date.now();
        reminderActivities.forEach(activity => {
            const activityDate = activity.activity_date.substring(0, 10);
            const startTime = activity.start_time.substring(0, 5);
            const activityTime = new Date(`${activityDate}T${startTime}:00`).getTime();
            const reminderTime = activityTime - Number(activity.reminder_minutes_before) * 60 * 1000;
            const reminderKey = `${activity.id}:${activityDate}:${startTime}:${activity.reminder_minutes_before}`;

            if (now < reminderTime || now >= activityTime + 60_000 || sentReminders.has(reminderKey)) return;

            showInAppReminder(activity);
            if ('Notification' in window && window.isSecureContext && Notification.permission === 'granted') {
                try {
                    new Notification(`Recordatorio: ${activity.title}`, {
                        body: `Tu actividad comienza a las ${startTime}.`,
                        tag: `famfit-${reminderKey}`,
                    });
                } catch (error) {
                    setReminderStatus('Usando avisos internos: el canal del sistema no respondió.', 'app');
                }
            }
            sentReminders.add(reminderKey);
            try {
                localStorage.setItem(notificationStorageKey, JSON.stringify([...sentReminders]));
            } catch (error) {
                reminderStorageUnavailable = true;
                setReminderStatus('Aviso enviado; no se pudo guardar el historial en este navegador.', 'app');
            }
        });
    };
    let permissionPromptAttempted = false;
    const requestNotificationPermission = async () => {
        if (permissionPromptAttempted
            || !notificationPreferences.habits_enabled
            || !('Notification' in window)
            || !window.isSecureContext
            || Notification.permission !== 'default') {
            return;
        }
        permissionPromptAttempted = true;
        try {
            await Notification.requestPermission();
        } catch (error) {
            setReminderStatus('Usando avisos internos; no se pudo habilitar el canal del navegador.', 'app');
        }
        updateReminderStatus();
        checkReminders();
    };
    ['pointerdown', 'keydown', 'touchstart'].forEach(eventName => {
        document.addEventListener(eventName, requestNotificationPermission, { once: true, passive: true });
    });
    document.addEventListener('visibilitychange', () => {
        if (!document.hidden) checkReminders();
    });
    updateReminderStatus();
    checkReminders();
    window.setInterval(checkReminders, 15000);
})();
