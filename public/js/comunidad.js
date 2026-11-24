const sectionTitles = {
    discover: 'Descubrir personas', friends: 'Mis amistades',
    incoming: 'Solicitudes de amistad', outgoing: 'Solicitudes de amistad',
};
const actions = {
    discover: [['send', 'Agregar']],
    friends: [['remove', 'Eliminar amistad']],
    incoming: [['accept', 'Aceptar'], ['reject', 'Rechazar']],
    outgoing: [['cancel', 'Cancelar solicitud']],
};
const confirmations = {
    send: 'Solicitud enviada.', accept: 'Ya son amigos.', reject: 'Solicitud rechazada.',
    cancel: 'Solicitud cancelada.', remove: 'Amistad eliminada.',
};
const empty = {
    discover: ['Por ahora no hay personas nuevas', 'Aquí aparecerán las personas que hagan visible su perfil.'],
    friends: ['Tu círculo empieza aquí', 'Busca a alguien en Descubrir y envíale una solicitud.'],
    incoming: ['Estás al día', 'Aquí podrás aceptar o rechazar las solicitudes que recibas.'],
    outgoing: ['Sin solicitudes enviadas', 'Las invitaciones pendientes de respuesta aparecerán aquí.'],
};
const fragments = {discover: 'descubrir', friends: 'amistades', incoming: 'recibidas', outgoing: 'enviadas'};

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
    let removal = null;

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
                if (action === 'remove') {
                    removal = person;
                    dialog.querySelector('p').textContent = 'Dejarás de tener a @' + person.username + ' en tu círculo.';
                    dialog.showModal();
                } else update(person, action);
            });
            buttons.append(button);
        });
        card.append(avatar, identity, buttons);
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
                ? 'La solicitud o la visibilidad del perfil cambió. Actualizamos la lista.'
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
        if (dialog.returnValue === 'remove' && removal) update(removal, 'remove');
        removal = null; dialog.returnValue = '';
    });
    select(fromLocation());
}

const root = document.querySelector('#community[data-people]');
if (root) mountCommunity(root);
