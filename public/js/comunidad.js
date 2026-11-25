const sectionTitles = {
    discover: 'Descubrir personas', friends: 'Mis amistades',
    incoming: 'Solicitudes de amistad', outgoing: 'Solicitudes de amistad',
    blocked: 'Personas que bloqueaste',
};
const actions = {
    discover: [['send', 'Agregar'], ['block', 'Bloquear']],
    friends: [['remove', 'Eliminar amistad'], ['block', 'Bloquear']],
    incoming: [['accept', 'Aceptar'], ['reject', 'Rechazar'], ['block', 'Bloquear']],
    outgoing: [['cancel', 'Cancelar solicitud'], ['block', 'Bloquear']],
    blocked: [['unblock', 'Desbloquear']],
};
const confirmations = {
    send: 'Solicitud enviada.', accept: 'Ya son amigos.', reject: 'Solicitud rechazada.',
    cancel: 'Solicitud cancelada.', remove: 'Amistad eliminada.',
    block: 'Cuenta bloqueada. Puedes gestionarla en Bloqueados.', unblock: 'Cuenta desbloqueada.',
};
const confirmDetails = {
    remove: 'Esta persona dejará de aparecer en tus amistades.',
    block: 'Se eliminará cualquier amistad o solicitud pendiente entre ustedes. Mientras dure el bloqueo, no podrán encontrarse en Descubrir ni enviarse solicitudes.',
    unblock: 'Si no queda otro bloqueo, podrán encontrarse de nuevo por su alias. Para volver a ser amigos tendrán que enviar una nueva solicitud.',
};
const empty = {
    discover: ['Por ahora no hay personas nuevas', 'Puedes consultar tus contactos en Amistades y tus invitaciones en Solicitudes.'],
    friends: ['Tu círculo empieza aquí', 'Busca a alguien en Descubrir y envíale una solicitud.'],
    incoming: ['Estás al día', 'Aquí podrás aceptar o rechazar las solicitudes que recibas.'],
    outgoing: ['Sin solicitudes enviadas', 'Las invitaciones pendientes de respuesta aparecerán aquí.'],
    blocked: ['No has bloqueado a nadie', 'Cuando bloquees una cuenta, podrás desbloquearla aquí.'],
};
const fragments = {discover: 'descubrir', friends: 'amistades', incoming: 'recibidas', outgoing: 'enviadas', blocked: 'bloqueados'};

function element(tag, className, text) {
    const node = document.createElement(tag);
    if (className) node.className = className;
    if (text !== undefined) node.textContent = text;
    return node;
}

function backend(root) {
    async function request(url, options = {}) {
        const response = await fetch(url, {
            credentials: 'same-origin', ...options,
            headers: {Accept: 'application/json', ...options.headers},
        });
        if (!response.ok) {
            const error = new Error(response.status === 419 || response.status === 401
                ? 'Tu sesión venció. Recarga la página para continuar.'
                : 'No se pudo completar la operación.');
            error.status = response.status;
            throw error;
        }
        return response.json();
    }
    return {
        list({list, q, page}) { 
            const url = new URL(root.dataset.people, location.href);
            url.search = new URLSearchParams({list, q, page});
            return request(url);
        }, report(form) {
    return request(form.action, {
        method: 'POST',
        body: new FormData(form),   
    });
    },
        update(person, action) {
            return request(root.dataset.friendship, {
                method: 'POST', headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': root.dataset.token},
                body: JSON.stringify({person, action}),
            });
        },
    };
}

export function mountCommunity(root, api = backend(root)) {
    const results = root.querySelector('.cm-results');
    const notice = root.querySelector('.cm-notice');
    const form = root.querySelector('.cm-search');
    const paging = root.querySelector('.cm-paging');
    const dialog = root.querySelector('dialog');
    let list = 'discover', q = '', page = 1, lastRead = 0, saving = false;
    let pendingAction = null;
    const reportDialog = root.querySelector('#cm-report');
const reportForm = reportDialog.querySelector('form');
const reportError = reportForm.querySelector('[role="alert"]');
const sendReport = reportForm.querySelector('[type="submit"]');
const cancelReport = reportForm.querySelector('[type="button"]');

function openReport(person) {
    reportForm.reset();
    reportForm.elements.person.value = person.id;
    reportForm.querySelector('h2 span').textContent = '@' + person.username;
    reportError.textContent = '';
    reportDialog.showModal();
}

cancelReport.addEventListener('click', () => reportDialog.close());

reportDialog.addEventListener('cancel', event => {
    if (sendReport.disabled) event.preventDefault();
});

reportForm.addEventListener('submit', async event => {
    event.preventDefault();
    if (sendReport.disabled) return;

    sendReport.disabled = true;
    cancelReport.disabled = true;
    reportError.textContent = '';

    try {
        const result = await api.report(reportForm);
        reportDialog.close();
        notice.textContent = result.message;
    } catch (error) {
        if (error.status === 422) {
            reportError.textContent = 'Revisa el motivo y el límite de 500 caracteres.';
        } else if (error.status === 404) {
            reportError.textContent = 'La cuenta ya no está disponible.';
        } else {
            reportError.textContent = 'No pudimos enviar el reporte. Revisa tu conexión y tu sesión.';
        }
    } finally {
        sendReport.disabled = false;
        cancelReport.disabled = false;
    }
});

    function showEmpty(title, detail, buttonText, onClick) {
        const card = element('div', 'cm-empty');
        card.append(element('h3', '', title), element('p', '', detail));
        if (buttonText) {
            const button = element('button', '', buttonText);
            button.type = 'button';
            button.addEventListener('click', onClick);
            card.append(button);
        }
        results.replaceChildren(card);
    }

    function personCard(person) {
        const card = element('article', 'cm-person');
        const avatar = element('span', 'cm-avatar', person.avatar || person.display.slice(0, 2).toLocaleUpperCase('es'));
        avatar.dataset.tone = String(person.id % 4);
        if (person.avatar) avatar.dataset.animal = '';
        avatar.setAttribute('aria-hidden', 'true');
        const identity = element('div', 'cm-identity');
        identity.append(element('h3', '', person.display === person.username ? '@' + person.username : person.display));
        if (person.display !== person.username) identity.append(element('small', '', '@' + person.username));
        const buttons = element('div', 'cm-actions');
        actions[list].forEach(([action, label]) => {
            const primary = ['accept', 'send'].includes(action);
            const button = element('button', primary ? 'cm-primary' : '', label);
            if (action === 'send') button.prepend(element('span', 'cm-add-sign', '+ '));
            button.type = 'button';
            button.disabled = saving;
            button.setAttribute('aria-label', label + ' · @' + person.username);
            button.addEventListener('click', () => {
                if (confirmDetails[action]) {
                    pendingAction = {person, action};
                    dialog.querySelector('h2').textContent = '¿' + label + (action === 'remove' ? ' con @' : ' a @') + person.username + '?';
                    dialog.querySelector('p').textContent = confirmDetails[action];
                    dialog.querySelector('[value="confirm"]').textContent = label;
                    dialog.showModal();
                } else update(person, action);
            });
            buttons.append(button);
        });
        const reportButton = element('button', 'cm-report-link');
reportButton.type = 'button';
reportButton.title = 'Reportar a @' + person.username;
reportButton.setAttribute('aria-label', reportButton.title);

reportButton.innerHTML = `
    <svg viewBox="0 0 24 24" aria-hidden="true">
        <path d="M5 21V4m0 1c5-3 8 3 14 0v9c-6 3-9-3-14 0"/>
    </svg>
    <span>Reportar</span>
`;

reportButton.addEventListener('click', () => openReport(person));
card.append(avatar, identity, reportButton, buttons);
if (list !== 'blocked') {
    const link = element('a', 'cm-profile-link');

    link.href = root.dataset.people + '/' + person.id;
    link.setAttribute('aria-label', 'Ver perfil de @' + person.username);

    card.prepend(link);
    link.append(avatar, identity);
}
        return card;
    }

    async function refresh() {
        const version = ++lastRead;
        results.setAttribute('aria-busy', 'true');
        results.replaceChildren(element('p', 'cm-loading', 'Cargando comunidad…'));
        paging.hidden = true;
        try {
            const data = await api.list({list, q, page});
            if (version !== lastRead) return;
            if (page > data.pages) { page = data.pages; return refresh(); }
            root.querySelectorAll('[data-count]').forEach(node => {
                const count = data.counts[node.dataset.count];
                node.textContent = count;
                if (node.classList.contains('cm-count')) node.hidden = !count;
            });
            if (data.people.length) {
                results.replaceChildren(...data.people.map(personCard));
            } else if (q) {
                showEmpty('Sin coincidencias', 'Prueba con otro nombre de usuario.', 'Limpiar búsqueda', () => {
                    form.reset(); q = ''; page = 1; refresh(); form.elements.q.focus();
                });
            } else {
                showEmpty(...empty[list], list === 'friends' || list === 'outgoing' ? 'Descubrir personas' : null,
                    () => navigate('discover'));
            }
            paging.hidden = data.pages <= 1;
            paging.querySelector('span').textContent = 'Página ' + page + ' de ' + data.pages;
            paging.querySelector('[data-page="-1"]').disabled = page <= 1;
            paging.querySelector('[data-page="1"]').disabled = page >= data.pages;
        } catch (error) {
            if (version === lastRead) showEmpty('No pudimos cargar esta lista', error.message || 'Revisa tu conexión.', 'Reintentar', refresh);
        } finally {
            if (version === lastRead) results.setAttribute('aria-busy', 'false');
        }
    }

    async function update(person, action) {
        if (saving) return;
        saving = true;
        results.querySelectorAll('button').forEach(button => { button.disabled = true; });
        try {
            await api.update(person.id, action);
            notice.textContent = confirmations[action];
        } catch (error) {
            notice.textContent = [404, 409].includes(error.status)
                ? 'Esta acción ya no está disponible. Actualizamos la lista.'
                : (error.message || 'Revisa tu conexión y vuelve a intentarlo.');
        } finally {
            saving = false;
            await refresh();
        }
    }

    function select(next) {
        list = next; q = ''; page = 1;
        form.reset(); notice.textContent = '';
        root.querySelector('#cm-title').textContent = sectionTitles[list];
        root.querySelectorAll('[data-list]').forEach(button => {
            if (button.dataset.list === (list === 'outgoing' ? 'incoming' : list)) button.setAttribute('aria-current', 'page');
            else button.removeAttribute('aria-current');
        });
        root.querySelector('.cm-request-filters').hidden = !['incoming', 'outgoing'].includes(list);
        root.querySelectorAll('[data-request]').forEach(button => button.setAttribute('aria-pressed', String(button.dataset.request === list)));
        return refresh();
    }

    function fromLocation() {
        const fragment = location.hash.slice(1);
        return Object.keys(fragments).find(key => fragments[key] === fragment) || 'discover';
    }
    function navigate(next) {
        if (fromLocation() === next) select(next);
        else location.hash = fragments[next];
    }
    window.addEventListener('hashchange', () => select(fromLocation()));
    root.querySelectorAll('[data-list],[data-request],[data-shortcut]').forEach(button => {
        button.addEventListener('click', () => navigate(button.dataset.list || button.dataset.request || button.dataset.shortcut));
    });
    form.addEventListener('submit', event => {
        event.preventDefault(); q = form.elements.q.value.trim(); page = 1; notice.textContent = ''; refresh();
    });
    paging.querySelectorAll('button').forEach(button => button.addEventListener('click', () => {
        page += Number(button.dataset.page); refresh();
    }));
    dialog.addEventListener('close', () => {
        if (dialog.returnValue === 'confirm' && pendingAction) update(pendingAction.person, pendingAction.action);
        pendingAction = null; dialog.returnValue = '';
    });
    select(fromLocation());
}

const root = document.querySelector('#community[data-people]');
if (root) mountCommunity(root);
