(() => {
    const root = document.getElementById('famfit-calendar');
    if (!root) return;

    const storageKey = 'famfit-calendar-activities';
    const weekdayNames = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
    const activityTypes = ['Ejercicio', 'Caminata', 'Ciclismo', 'Deporte', 'Yoga', 'Otro'];
    const today = new Date();
    today.setHours(0, 0, 0, 0);
    let selectedDate = new Date(today);
    let editingId = null;
    let returnFocusTo = null;

    root.innerHTML = `
        <div class="ff-app">
            <aside class="ff-sidebar">
                <a class="ff-brand" href="#"><span class="ff-brand-mark">F</span><span>FamFit</span></a>
                <div class="ff-side-label">MENÚ PRINCIPAL</div>
                <a class="ff-nav-link" href="#"><span>▦</span> Calendario</a>
                <div class="ff-side-note"><span class="ff-dot"></span><div><strong>Tu bienestar importa</strong><small>Un hábito a la vez.</small></div></div>
                <div class="ff-sidebar-bottom"><div class="ff-avatar">F</div><div class="ff-user-info"><strong>FamFit</strong><small>Calendario personal</small></div></div>
            </aside>
            <main class="ff-main">
                <header class="ff-topbar">
                    <div><span class="ff-eyebrow">FAMFIT / ORGANIZACIÓN</span><h1>Calendario inteligente</h1></div>
                    <button class="ff-btn ff-btn-primary" type="button" data-action="create">＋ Nueva actividad</button>
                </header>
                <section class="ff-stats" aria-label="Resumen de esta semana">
                    <article class="ff-stat-card"><span class="ff-stat-icon blue">◷</span><div><small>Horas esta semana</small><strong id="stat-hours">0 h</strong></div></article>
                    <article class="ff-stat-card"><span class="ff-stat-icon teal">▦</span><div><small>Actividades</small><strong id="stat-count">0</strong></div></article>
                    <article class="ff-stat-card"><span class="ff-stat-icon green">✓</span><div><small>Completadas</small><strong id="stat-completed">0</strong></div></article>
                    <article class="ff-stat-card"><span class="ff-stat-icon amber">★</span><div><small>Puntos ganados</small><strong id="stat-points">0</strong></div></article>
                </section>
                <section class="ff-calendar-panel">
                    <div class="ff-calendar-toolbar">
                        <div><span class="ff-eyebrow">VISTA SEMANAL</span><h2 id="week-title"></h2><p>Selecciona un horario para crear una actividad o elige una existente para editarla.</p></div>
                        <nav class="ff-calendar-actions" aria-label="Navegación del calendario">
                            <button class="ff-icon-btn" type="button" data-action="previous-year" aria-label="Año anterior" title="Año anterior">«</button>
                            <button class="ff-icon-btn" type="button" data-action="previous-month" aria-label="Mes anterior" title="Mes anterior">‹</button>
                            <button class="ff-today-btn" type="button" data-action="today">Hoy</button>
                            <button class="ff-icon-btn" type="button" data-action="next-month" aria-label="Mes siguiente" title="Mes siguiente">›</button>
                            <button class="ff-icon-btn" type="button" data-action="next-year" aria-label="Año siguiente" title="Año siguiente">»</button>
                        </nav>
                    </div>
                    <div class="ff-planner-controls">
                        <div class="ff-day-picker-panel">
                            <div class="ff-mini-heading"><strong id="month-title"></strong><span>Elige un día</span></div>
                            <div class="ff-day-picker" id="day-picker" aria-label="Selector de día"></div>
                        </div>
                        <div class="ff-week-planner">
                            <div class="ff-week-schedule">
                                <div class="ff-time-column"><div class="ff-time-header" aria-hidden="true"></div><div class="ff-time-ruler" id="time-ruler"></div></div>
                                <div class="ff-week-frame"><div class="ff-week-inner"><div class="ff-week-header" id="week-header"></div><div class="ff-week-grid" id="week-grid"></div></div></div>
                            </div>
                            <div class="ff-week-navigation">
                                <button class="ff-btn ff-btn-ghost" type="button" data-action="previous-week">‹ Semana anterior</button>
                                <span id="week-range"></span>
                                <button class="ff-btn ff-btn-ghost" type="button" data-action="next-week">Semana siguiente ›</button>
                            </div>
                        </div>
                    </div>
                    <div class="ff-legend"><span><i class="legend-teal"></i> Hábitos y movimiento</span><span><i class="legend-blue"></i> Actividad programada</span><span><i class="legend-green"></i> Actividad completada</span></div>
                </section>
                <footer class="ff-footer">FamFit · Cada pequeño hábito cuenta.</footer>
            </main>
        </div>
        <div class="ff-modal-backdrop" id="activityModal" aria-hidden="true">
            <section class="ff-modal" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
                <div class="ff-modal-head"><div><span class="ff-eyebrow">PLANIFICACIÓN FAMFIT</span><h2 id="modalTitle">Nueva actividad</h2></div><button type="button" class="ff-close" data-action="close" aria-label="Cerrar">×</button></div>
                <form id="activityForm">
                    <div class="ff-form-grid">
                        <label class="ff-field ff-field-full">Nombre de la actividad<input name="title" id="activityTitle" maxlength="100" placeholder="Ej. Caminata matutina" required></label>
                        <label class="ff-field">Tipo de actividad<select name="activity_type" id="activityType" required>${activityTypes.map(type => `<option>${type}</option>`).join('')}</select></label>
                        <label class="ff-field">Fecha<input type="date" name="activity_date" id="activityDate" required></label>
                        <label class="ff-field">Hora de inicio<input type="time" name="start_time" id="startTime" required></label>
                        <label class="ff-field">Hora de finalización<input type="time" name="end_time" id="endTime" required></label>
                        <label class="ff-field ff-field-full">Estado<select name="status" id="activityStatus"><option value="planned">Programada</option><option value="completed">Completada</option></select></label>
                        <label class="ff-field ff-field-full">Notas (opcional)<textarea name="notes" id="activityNotes" rows="3" maxlength="1000" placeholder="Agrega detalles de tu actividad..."></textarea></label>
                    </div>
                    <div class="ff-modal-actions"><button type="button" class="ff-btn ff-btn-ghost" data-action="close">Cancelar</button><button type="submit" class="ff-btn ff-btn-primary" id="saveActivityBtn">Guardar actividad</button></div>
                </form>
                <div class="ff-delete-form" id="deleteArea" hidden><button type="button" class="ff-delete-link" data-action="delete">Eliminar actividad</button></div>
            </section>
        </div>`;

    const modal = root.querySelector('#activityModal');
    const form = root.querySelector('#activityForm');
    const fields = {
        title: root.querySelector('#activityTitle'),
        type: root.querySelector('#activityType'),
        date: root.querySelector('#activityDate'),
        start: root.querySelector('#startTime'),
        end: root.querySelector('#endTime'),
        status: root.querySelector('#activityStatus'),
        notes: root.querySelector('#activityNotes'),
    };

    const dateKey = date => `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}-${String(date.getDate()).padStart(2, '0')}`;
    const parseDate = value => {
        const [year, month, day] = value.split('-').map(Number);
        return new Date(year, month - 1, day);
    };
    const addDays = (date, amount) => {
        const result = new Date(date);
        result.setDate(result.getDate() + amount);
        return result;
    };
    const shiftMonth = (date, amount) => {
        const shifted = new Date(date.getFullYear(), date.getMonth() + amount, 1);
        shifted.setDate(Math.min(date.getDate(), new Date(shifted.getFullYear(), shifted.getMonth() + 1, 0).getDate()));
        return shifted;
    };
    const mondayOf = date => addDays(date, -((date.getDay() + 6) % 7));
    const sameDay = (left, right) => dateKey(left) === dateKey(right);
    const formatDate = (date, options) => new Intl.DateTimeFormat('es', options).format(date);
    const escapeHtml = value => String(value).replace(/[&<>"']/g, character => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    })[character]);
    const loadActivities = () => {
        const stored = localStorage.getItem(storageKey);
        const activities = stored ? JSON.parse(stored) : [];
        if (!Array.isArray(activities)) throw new Error('Los datos guardados del calendario no tienen un formato válido.');
        return activities;
    };
    const saveActivities = activities => localStorage.setItem(storageKey, JSON.stringify(activities));
    const minutes = value => {
        const [hour, minute] = value.split(':').map(Number);
        return hour * 60 + minute;
    };

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
    const resetForm = (date = selectedDate) => {
        form.reset();
        editingId = null;
        fields.date.value = dateKey(date);
        fields.start.value = '09:00';
        fields.end.value = '10:00';
        fields.end.min = fields.start.value;
        fields.status.value = 'planned';
        root.querySelector('#modalTitle').textContent = 'Nueva actividad';
        root.querySelector('#saveActivityBtn').textContent = 'Guardar actividad';
        root.querySelector('#deleteArea').hidden = true;
    };
    const editActivity = activity => {
        resetForm(parseDate(activity.activity_date));
        editingId = activity.id;
        fields.title.value = activity.title;
        fields.type.value = activity.activity_type;
        fields.date.value = activity.activity_date;
        fields.start.value = activity.start_time;
        fields.end.value = activity.end_time;
        fields.status.value = activity.status;
        fields.notes.value = activity.notes || '';
        root.querySelector('#modalTitle').textContent = 'Editar actividad';
        root.querySelector('#saveActivityBtn').textContent = 'Guardar cambios';
        root.querySelector('#deleteArea').hidden = false;
        openModal();
    };

    const render = () => {
        const activities = loadActivities();
        const weekStart = mondayOf(selectedDate);
        const weekEnd = addDays(weekStart, 6);
        const monthStart = new Date(selectedDate.getFullYear(), selectedDate.getMonth(), 1);
        const pickerStart = mondayOf(monthStart);
        const pickerEnd = addDays(mondayOf(new Date(selectedDate.getFullYear(), selectedDate.getMonth() + 1, 0)), 6);
        const weekActivities = activities.filter(activity => {
            const date = parseDate(activity.activity_date);
            return date >= weekStart && date <= weekEnd;
        });

        root.querySelector('#week-title').textContent = `${formatDate(weekStart, { day: 'numeric', month: 'short' })} – ${formatDate(weekEnd, { day: 'numeric', month: 'short', year: 'numeric' })}`;
        root.querySelector('#week-range').textContent = `${formatDate(weekStart, { day: 'numeric', month: 'short' })} – ${formatDate(weekEnd, { day: 'numeric', month: 'short' })}`;
        root.querySelector('#month-title').textContent = formatDate(selectedDate, { month: 'long', year: 'numeric' });

        const totalMinutes = weekActivities.reduce((sum, activity) => sum + Math.max(0, minutes(activity.end_time) - minutes(activity.start_time)), 0);
        const completed = weekActivities.filter(activity => activity.status === 'completed');
        root.querySelector('#stat-hours').textContent = `${(totalMinutes / 60).toFixed(1)} h`;
        root.querySelector('#stat-count').textContent = String(weekActivities.length);
        root.querySelector('#stat-completed').textContent = String(completed.length);
        root.querySelector('#stat-points').textContent = String(completed.reduce((sum, activity) => sum + Math.floor((minutes(activity.end_time) - minutes(activity.start_time)) / 30) * 10, 0));

        const picker = root.querySelector('#day-picker');
        picker.innerHTML = weekdayNames.map(day => `<span class="ff-mini-weekday">${day}</span>`).join('');
        const activityDates = new Set(activities.map(activity => activity.activity_date));
        for (let day = new Date(pickerStart); day <= pickerEnd; day = addDays(day, 1)) {
            const key = dateKey(day);
            const classes = ['ff-mini-day'];
            if (day.getMonth() !== selectedDate.getMonth()) classes.push('is-outside');
            if (sameDay(day, today)) classes.push('is-today');
            if (sameDay(day, selectedDate)) classes.push('is-selected');
            picker.insertAdjacentHTML('beforeend', `<button type="button" class="${classes.join(' ')}" data-select-date="${key}" aria-label="${formatDate(day, { day: 'numeric', month: 'long', year: 'numeric' })}"${sameDay(day, selectedDate) ? ' aria-current="date"' : ''}>${day.getDate()}${activityDates.has(key) ? '<i aria-hidden="true"></i>' : ''}</button>`);
        }

        root.querySelector('#time-ruler').innerHTML = Array.from({ length: 24 }, (_, hour) => `<span class="ff-time-label" style="top: ${hour * 60}px">${String(hour).padStart(2, '0')}:00</span>`).join('');
        root.querySelector('#week-header').innerHTML = Array.from({ length: 7 }, (_, index) => {
            const day = addDays(weekStart, index);
            const classes = ['ff-week-heading'];
            if (sameDay(day, today)) classes.push('is-today');
            if (sameDay(day, selectedDate)) classes.push('is-selected');
            return `<button class="${classes.join(' ')}" type="button" data-day-create="${dateKey(day)}"><span>${formatDate(day, { weekday: 'short' })}</span><strong>${day.getDate()}</strong></button>`;
        }).join('');

        const grid = root.querySelector('#week-grid');
        grid.dataset.today = dateKey(today);
        grid.innerHTML = Array.from({ length: 7 }, (_, index) => {
            const day = addDays(weekStart, index);
            const key = dateKey(day);
            const dayActivities = weekActivities.filter(activity => activity.activity_date === key);
            const events = dayActivities.map(activity => {
                const start = minutes(activity.start_time);
                const duration = Math.max(15, minutes(activity.end_time) - start);
                const typeClass = activity.activity_type.toLowerCase();
                return `<button type="button" class="ff-event event-${escapeHtml(typeClass)}${activity.status === 'completed' ? ' is-completed' : ''}" style="top: ${start}px; height: ${duration}px" data-edit-id="${escapeHtml(activity.id)}" title="${escapeHtml(activity.title)} · ${escapeHtml(activity.start_time)}–${escapeHtml(activity.end_time)}"><strong>${escapeHtml(activity.title)}</strong><span>${escapeHtml(activity.start_time)} – ${escapeHtml(activity.end_time)}</span></button>`;
            }).join('');
            return `<div class="ff-day-column${sameDay(day, today) ? ' is-today' : ''}" data-day-column data-date="${key}" aria-label="Agregar actividad el ${formatDate(day, { day: 'numeric', month: 'long' })}">${events}</div>`;
        }).join('');
        updateCurrentTime();
    };

    const updateCurrentTime = () => {
        root.querySelectorAll('.ff-current-time').forEach(line => line.remove());
        const grid = root.querySelector('#week-grid');
        const todayColumn = [...grid.querySelectorAll('[data-day-column]')].find(column => column.dataset.date === grid.dataset.today);
        if (!todayColumn) return;
        const now = new Date();
        const line = document.createElement('div');
        line.className = 'ff-current-time';
        line.style.top = `${now.getHours() * 60 + now.getMinutes()}px`;
        todayColumn.appendChild(line);
    };

    root.addEventListener('click', event => {
        const action = event.target.closest('[data-action]')?.dataset.action;
        if (action) {
            if (action === 'create') {
                resetForm();
                openModal();
            } else if (action === 'close') {
                closeModal();
            } else if (action === 'delete') {
                if (editingId && window.confirm('¿Seguro que deseas eliminar esta actividad?')) {
                    saveActivities(loadActivities().filter(activity => activity.id !== editingId));
                    closeModal();
                    render();
                }
            } else {
                const date = new Date(selectedDate);
                if (action === 'today') selectedDate = new Date(today);
                if (action === 'previous-year') selectedDate = shiftMonth(date, -12);
                if (action === 'next-year') selectedDate = shiftMonth(date, 12);
                if (action === 'previous-month') selectedDate = shiftMonth(date, -1);
                if (action === 'next-month') selectedDate = shiftMonth(date, 1);
                if (action === 'previous-week') date.setDate(date.getDate() - 7);
                if (action === 'next-week') date.setDate(date.getDate() + 7);
                if (action !== 'today') selectedDate = date;
                render();
            }
            return;
        }

        const dateButton = event.target.closest('[data-select-date]');
        if (dateButton) {
            selectedDate = parseDate(dateButton.dataset.selectDate);
            render();
            return;
        }
        const dayButton = event.target.closest('[data-day-create]');
        if (dayButton) {
            resetForm(parseDate(dayButton.dataset.dayCreate));
            openModal();
            return;
        }
        const activityButton = event.target.closest('[data-edit-id]');
        if (activityButton) {
            const activity = loadActivities().find(item => item.id === activityButton.dataset.editId);
            if (activity) editActivity(activity);
            return;
        }
        if (event.target === modal) closeModal();
    });

    root.querySelector('#week-grid').addEventListener('click', event => {
        const column = event.target.closest('[data-day-column]');
        if (!column || event.target.closest('.ff-event')) return;
        const bounds = column.getBoundingClientRect();
        const elapsed = Math.max(0, Math.min(event.clientY - bounds.top, 24 * 60));
        const startMinutes = Math.min(23 * 60 + 45, Math.floor(elapsed / 15) * 15);
        const formatTime = value => `${String(Math.floor(value / 60)).padStart(2, '0')}:${String(value % 60).padStart(2, '0')}`;
        resetForm(parseDate(column.dataset.date));
        fields.start.value = formatTime(startMinutes);
        fields.end.value = formatTime(Math.min(startMinutes + 60, 23 * 60 + 59));
        openModal();
    });

    fields.start.addEventListener('input', () => {
        fields.end.setCustomValidity('');
        fields.end.min = fields.start.value;
    });
    fields.end.addEventListener('input', () => fields.end.setCustomValidity(''));
    form.addEventListener('submit', event => {
        event.preventDefault();
        if (fields.end.value <= fields.start.value) {
            fields.end.setCustomValidity('La hora de finalización debe ser posterior a la de inicio.');
            fields.end.reportValidity();
            return;
        }
        const activities = loadActivities();
        const data = {
            id: editingId || (window.crypto?.randomUUID ? window.crypto.randomUUID() : `${Date.now()}-${Math.random().toString(16).slice(2)}`),
            title: fields.title.value.trim(),
            activity_type: fields.type.value,
            activity_date: fields.date.value,
            start_time: fields.start.value,
            end_time: fields.end.value,
            status: fields.status.value,
            notes: fields.notes.value.trim(),
        };
        const updated = editingId
            ? activities.map(activity => activity.id === editingId ? data : activity)
            : [...activities, data];
        saveActivities(updated);
        selectedDate = parseDate(data.activity_date);
        closeModal();
        render();
    });

    document.addEventListener('keydown', event => {
        if (event.key === 'Escape' && modal.classList.contains('is-open')) closeModal();
    });
    render();
    window.setInterval(updateCurrentTime, 60_000);
})();
