/* ==========================================================
   FamFit · Nutrición
   Registro manual y por foto, historial, edición, borrado,
   restricciones (alergias), agua y avisos.
   ========================================================== */
(function () {
    'use strict';

    const root = document.getElementById('nutricion');
    if (!root) return;

    /* ---------- Configuración ---------- */
    const API = root.dataset.api || '/nutricion';
    const CSRF = root.dataset.csrf || '';
    const META_AGUA = parseInt(root.dataset.metaAgua, 10) || 2000;
    const MAX_FOTO_MB = 5;
    const TIPOS = { desayuno: 'Desayuno', almuerzo: 'Almuerzo', cena: 'Cena', snack: 'Snack' };

    /* ---------- Fecha en hora LOCAL (YYYY-MM-DD) ---------- */
    const hoyISO = () => {
        const ahora = new Date();
        const anio = ahora.getFullYear();
        const mes = String(ahora.getMonth() + 1).padStart(2, '0');
        const dia = String(ahora.getDate()).padStart(2, '0');
        return `${anio}-${mes}-${dia}`;
    };

    /* ---------- Estado ---------- */
    const estado = {
        fecha: hoyISO(),
        filtro: 'todas',
        comidas: [],
        restricciones: [],
        editandoId: null,
        fotoArchivo: null,
        itemsFoto: [],
        itemsManual: [],
        pendienteBorrarId: null,
        aguaHoy: 0,
        ultimaPeticionDia: 0,   // evita mostrar respuestas viejas
        guardando: false,       // evita doble envío
    };

    /* ---------- Elementos del DOM ---------- */
    const el = {
        fecha: document.getElementById('nut-fecha'),
        diaTexto: document.getElementById('nut-dia-texto'),
        statComidas: document.getElementById('nut-stat-comidas'),
        statFotos: document.getElementById('nut-stat-fotos'),
        racha: document.getElementById('nut-racha'),
        agua: document.getElementById('nut-agua'),
        aguaMeta: document.getElementById('nut-agua-meta'),
        aguaBarra: document.getElementById('nut-agua-barra'),
        historial: document.getElementById('nut-historial'),
        restricciones: document.getElementById('nut-restricciones'),
        formManual: document.getElementById('nut-form-manual'),
        formFoto: document.getElementById('nut-form-foto'),
        formTitulo: document.getElementById('nut-form-titulo'),
        comidaId: document.getElementById('nut-comida-id'),
        tipo: document.getElementById('nut-tipo'),
        hora: document.getElementById('nut-hora'),
        descripcion: document.getElementById('nut-descripcion'),
        notas: document.getElementById('nut-notas'),
        itemsManual: document.getElementById('nut-items-manual'),
        alertasBorrador: document.getElementById('nut-alertas-borrador'),
        fotoInput: document.getElementById('nut-foto-input'),
        fotoPreview: document.getElementById('nut-foto-preview'),
        fotoTexto: document.getElementById('nut-foto-texto'),
        dropzone: root.querySelector('.nut-dropzone'),
        btnAnalizar: document.getElementById('nut-btn-analizar'),
        resultadoFoto: document.getElementById('nut-resultado-foto'),
        itemsFoto: document.getElementById('nut-items-foto'),
        alertasFoto: document.getElementById('nut-alertas-foto'),
        tipoFoto: document.getElementById('nut-tipo-foto'),
        restriccionForm: document.getElementById('nut-form-restriccion'),
        restriccionTexto: document.getElementById('nut-restriccion-texto'),
        restriccionTipo: document.getElementById('nut-restriccion-tipo'),
        modal: document.getElementById('nut-modal'),
        toast: document.getElementById('nut-toast'),
        btnCancelarEdicion: root.querySelector('[data-action="cancelar-edicion"]'),
        btnQuitarFoto: root.querySelector('[data-action="quitar-foto"]'),
    };

    /* ---------- Utilidades ---------- */
    const escapar = (texto) => String(texto ?? '').replace(/[&<>"']/g, (c) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[c]));

    // Minúsculas, sin tildes, sin espacios sobrantes
    const normalizar = (t) => String(t || '')
        .toLowerCase()
        .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
        .replace(/\s+/g, ' ')
        .trim();

    const formatoFecha = (iso) => {
        const [y, m, d] = iso.split('-').map(Number);
        const texto = new Date(y, m - 1, d).toLocaleDateString('es-CO', {
            weekday: 'long', day: 'numeric', month: 'long',
        });
        return texto.charAt(0).toUpperCase() + texto.slice(1);
    };

    const quitarHora = (hora) => (hora ? String(hora).slice(0, 5) : '');

    /* ---------- Mensajes flotantes ---------- */
    let toastTimer = null;
    function mostrarToast(mensaje, esError = false) {
        el.toast.textContent = mensaje;
        el.toast.classList.toggle('is-error', esError);
        el.toast.hidden = false;
        clearTimeout(toastTimer);
        toastTimer = setTimeout(() => { el.toast.hidden = true; }, 3200);
    }

    /* Pone un botón en estado "cargando" y devuelve una función para restaurarlo */
    function ponerCargando(boton, texto) {
        const original = boton.textContent;
        boton.disabled = true;
        boton.textContent = texto;
        return () => {
            boton.disabled = false;
            boton.textContent = original;
        };
    }

    /* ---------- API ---------- */
    async function api(ruta, { metodo = 'GET', cuerpo = null, esFormData = false } = {}) {
        const cabeceras = {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
        };
        if (CSRF) cabeceras['X-CSRF-TOKEN'] = CSRF;

        const opciones = {
            method: metodo,
            headers: cabeceras,
            credentials: 'same-origin',
        };

        if (cuerpo) {
            if (esFormData) {
                opciones.body = cuerpo; // el navegador pone el Content-Type solo
            } else {
                cabeceras['Content-Type'] = 'application/json';
                opciones.body = JSON.stringify(cuerpo);
            }
        }

        const respuesta = await fetch(`${API}${ruta}`, opciones);

        let datos = null;
        try { datos = await respuesta.json(); } catch (_) { /* respuesta sin JSON */ }

        if (!respuesta.ok) {
            // Laravel devuelve errores de validación en datos.errors
            const primerError = datos?.errors ? Object.values(datos.errors)[0]?.[0] : null;
            throw new Error(primerError || datos?.message || 'Ocurrió un error. Inténtalo de nuevo.');
        }
        return datos ?? {};
    }

    /* ---------- Carga de datos ---------- */
    async function cargarDia() {
        const peticion = ++estado.ultimaPeticionDia;
        el.diaTexto.textContent = formatoFecha(estado.fecha);

        try {
            const datos = await api(`/comidas?fecha=${encodeURIComponent(estado.fecha)}`);
            if (peticion !== estado.ultimaPeticionDia) return; // respuesta vieja, la ignoramos

            estado.comidas = datos.comidas ?? [];
            estado.aguaHoy = datos.agua_ml ?? 0;
            el.racha.innerHTML = `${Number(datos.racha ?? 0)} <small>días</small>`;
        } catch (err) {
            if (peticion !== estado.ultimaPeticionDia) return;
            estado.comidas = [];
            mostrarToast(err.message, true);
        }

        renderEstadisticas();
        renderHistorial();
    }

    async function cargarRestricciones() {
        try {
            const datos = await api('/restricciones');
            estado.restricciones = datos.restricciones ?? [];
        } catch (err) {
            estado.restricciones = [];
            mostrarToast(err.message, true);
        }
        renderRestricciones();
        revisarAlertas();
    }

    /* ---------- Render: estadísticas ---------- */
    function renderEstadisticas() {
        const fotos = estado.comidas.filter((c) => c.foto_url).length;
        el.statComidas.textContent = estado.comidas.length;
        el.statFotos.textContent = fotos;

        el.agua.textContent = estado.aguaHoy;
        el.aguaMeta.textContent = META_AGUA;
        const porcentaje = Math.min(100, Math.round((estado.aguaHoy / META_AGUA) * 100));
        el.aguaBarra.style.width = `${porcentaje}%`;
    }

    /* ---------- Render: historial ---------- */
    function renderHistorial() {
        const lista = estado.filtro === 'todas'
            ? estado.comidas
            : estado.comidas.filter((c) => c.tipo === estado.filtro);

        if (lista.length === 0) {
            el.historial.innerHTML = `
                <div class="nut-empty">
                    <div class="nut-empty-icon">🥗</div>
                    <p><strong>Aún no hay comidas en este filtro.</strong></p>
                    <p>Registra tu primera comida con el formulario de arriba.</p>
                </div>`;
            return;
        }

        el.historial.innerHTML = lista.map(plantillaComida).join('');
    }

    function plantillaComida(comida) {
        const hora = quitarHora(comida.hora);

        const items = (comida.alimentos ?? []).map((a) =>
            `<li>${escapar(a.nombre)}${a.porcion ? ` (${escapar(a.porcion)})` : ''}</li>`
        ).join('');

        const miniatura = comida.foto_url
            ? `<img class="nut-meal-thumb" src="${escapar(comida.foto_url)}" alt="Foto de ${escapar(comida.descripcion || 'la comida')}" loading="lazy">`
            : `<div class="nut-meal-thumb nut-meal-thumb-empty" aria-hidden="true">🍽</div>`;

        // Estimaciones nutricionales: SOLO si el backend entrega una fuente fiable
        const nutricion = comida.nutricion && comida.nutricion.fuente
            ? `<p class="nut-meal-note">≈ ${Math.round(Number(comida.nutricion.kcal))} kcal · Fuente: ${escapar(comida.nutricion.fuente)}</p>`
            : `<span class="nut-nutri-nd">Sin datos nutricionales verificados</span>`;

        return `
            <article class="nut-meal" data-id="${escapar(comida.id)}">
                ${miniatura}
                <div>
                    <div class="nut-meal-head">
                        <span class="nut-badge">${escapar(TIPOS[comida.tipo] || comida.tipo)}</span>
                        ${hora ? `<span class="nut-muted" style="margin:0">${escapar(hora)}</span>` : ''}
                        ${comida.foto_url ? '<span class="nut-badge nut-badge-photo">Foto</span>' : ''}
                    </div>
                    <p class="nut-meal-title">${escapar(comida.descripcion || 'Sin descripción')}</p>
                    ${items ? `<ul class="nut-meal-items">${items}</ul>` : ''}
                    ${comida.notas ? `<p class="nut-meal-note">${escapar(comida.notas)}</p>` : ''}
                    ${nutricion}
                </div>
                <div class="nut-meal-actions">
                    <button type="button" class="nut-btn nut-btn-secondary nut-btn-sm" data-action="editar" data-id="${escapar(comida.id)}">Editar</button>
                    <button type="button" class="nut-btn nut-btn-danger nut-btn-sm" data-action="eliminar" data-id="${escapar(comida.id)}">Eliminar</button>
                </div>
            </article>`;
    }

    /* ---------- Render: restricciones ---------- */
    const etiquetaRestriccion = { alergia: 'Alergia', intolerancia: 'Intolerancia', evita: 'Evito' };

    function renderRestricciones() {
        if (estado.restricciones.length === 0) {
            el.restricciones.innerHTML = `<li class="nut-muted" style="margin:0">Aún no has añadido restricciones.</li>`;
            return;
        }

        el.restricciones.innerHTML = estado.restricciones.map((r) => `
            <li class="nut-restriction">
                ${escapar(r.texto)} <small>${escapar(etiquetaRestriccion[r.tipo] || r.tipo)}</small>
                <button type="button" data-action="quitar-restriccion" data-id="${escapar(r.id)}" aria-label="Quitar ${escapar(r.texto)}">×</button>
            </li>`).join('');
    }

    /* ---------- Alergias: detección de advertencias ---------- */
    // Compara palabras completas (mínimo 3 letras) para evitar falsos positivos:
    // así "maní" no coincide con "a", ni "leche" con "lechuga".
    function coincide(alimento, restriccion) {
        const palabrasAlimento = normalizar(alimento).split(' ').filter((p) => p.length >= 3);
        const restr = normalizar(restriccion);
        if (restr.length < 3) return false;

        return palabrasAlimento.some((p) => p === restr || p.startsWith(restr) || restr.startsWith(p) && p.length >= 4)
            || normalizar(alimento) === restr;
    }

    function alertasParaAlimentos(nombres) {
        const avisos = new Set();

        nombres.forEach((nombre) => {
            if (!normalizar(nombre)) return;

            estado.restricciones.forEach((r) => {
                if (!coincide(nombre, r.texto)) return;

                const etiqueta = r.tipo === 'alergia'
                    ? 'Alergia'
                    : r.tipo === 'intolerancia' ? 'Intolerancia' : 'Evitas';

                avisos.add(`⚠️ ${etiqueta}: "${nombre}" coincide con "${r.texto}". Revisa la composición antes de comer.`);
            });
        });

        return [...avisos];
    }

    function pintarAlertas(contenedor, nombres) {
        const avisos = alertasParaAlimentos(nombres);
        contenedor.innerHTML = avisos.map((a) => `<div class="nut-alert">${escapar(a)}</div>`).join('');
    }

    function revisarAlertas() {
        pintarAlertas(el.alertasBorrador, estado.itemsManual.map((i) => i.nombre));
        pintarAlertas(el.alertasFoto, estado.itemsFoto.map((i) => i.nombre));
    }

    /* ---------- Ítems editables (manual y foto) ---------- */
    function plantillaItem(item, origen, index) {
        const confianza = typeof item.confianza === 'number'
            ? `<span class="nut-conf">${Math.round(item.confianza * 100)}% orientativo</span>`
            : '';

        return `
            <div class="nut-item-wrap">
                <div class="nut-item" data-index="${index}" data-origen="${origen}">
                    <input type="text" class="nut-input" value="${escapar(item.nombre)}" placeholder="Alimento" maxlength="80" aria-label="Nombre del alimento" data-campo="nombre">
                    <input type="text" class="nut-input" value="${escapar(item.porcion || '')}" placeholder="Porción (ej. 1 taza)" maxlength="40" aria-label="Porción" data-campo="porcion">
                    <button type="button" class="nut-item-remove" data-action="quitar-item" aria-label="Quitar alimento">✕</button>
                </div>
                ${confianza}
            </div>`;
    }

    function renderItemsManual() {
        if (estado.itemsManual.length === 0) {
            estado.itemsManual.push({ nombre: '', porcion: '' });
        }
        el.itemsManual.innerHTML = estado.itemsManual.map((i, idx) => plantillaItem(i, 'manual', idx)).join('');
        revisarAlertas();
    }

    function renderItemsFoto() {
        el.itemsFoto.innerHTML = estado.itemsFoto.map((i, idx) => plantillaItem(i, 'foto', idx)).join('');
        revisarAlertas();
    }

    // Lee solo las filas con nombre escrito
    function leerItems(contenedor) {
        return [...contenedor.querySelectorAll('.nut-item')]
            .map((fila) => ({
                nombre: fila.querySelector('[data-campo="nombre"]').value.trim(),
                porcion: fila.querySelector('[data-campo="porcion"]').value.trim(),
            }))
            .filter((i) => i.nombre);
    }

    /* ---------- Pestañas Manual / Foto ---------- */
    function cambiarPestana(nombre) {
        root.querySelectorAll('.nut-tab').forEach((t) => {
            const activa = t.dataset.tab === nombre;
            t.classList.toggle('is-active', activa);
            t.setAttribute('aria-selected', String(activa));
        });
        el.formManual.hidden = nombre !== 'manual';
        el.formFoto.hidden = nombre !== 'foto';
    }

    /* ---------- Guardar comida manual (crear o editar) ---------- */
    async function guardarManual(evento) {
        evento.preventDefault();
        if (estado.guardando) return;

        const items = leerItems(el.itemsManual);
        const descripcion = el.descripcion.value.trim();

        if (items.length === 0 && !descripcion) {
            mostrarToast('Escribe una descripción o al menos un alimento.', true);
            return;
        }

        const cuerpo = {
            fecha: estado.fecha,
            tipo: el.tipo.value,
            hora: el.hora.value || null,
            descripcion: descripcion || null,
            notas: el.notas.value.trim() || null,
            alimentos: items,
            origen: 'manual',
        };

        const boton = el.formManual.querySelector('button[type="submit"]');
        const restaurar = ponerCargando(boton, 'Guardando…');
        estado.guardando = true;

        try {
            if (estado.editandoId) {
                await api(`/comidas/${estado.editandoId}`, { metodo: 'PUT', cuerpo });
                mostrarToast('Comida actualizada');
            } else {
                await api('/comidas', { metodo: 'POST', cuerpo });
                mostrarToast('Comida guardada');
            }
            limpiarFormularioManual();
            await cargarDia();
        } catch (err) {
            mostrarToast(err.message, true);
        } finally {
            estado.guardando = false;
            restaurar();
        }
    }

    function limpiarFormularioManual() {
        el.formManual.reset();
        estado.itemsManual = [];
        estado.editandoId = null;
        el.comidaId.value = '';
        el.formTitulo.textContent = 'Nueva comida';
        el.btnCancelarEdicion.hidden = true;
        renderItemsManual();
    }

    function cargarEnFormulario(comida) {
        estado.editandoId = comida.id;
        el.comidaId.value = comida.id;
        el.tipo.value = comida.tipo;
        el.hora.value = quitarHora(comida.hora);
        el.descripcion.value = comida.descripcion || '';
        el.notas.value = comida.notas || '';
        estado.itemsManual = (comida.alimentos ?? []).map((a) => ({
            nombre: a.nombre || '',
            porcion: a.porcion || '',
        }));
        el.formTitulo.textContent = 'Editar comida';
        el.btnCancelarEdicion.hidden = false;
        cambiarPestana('manual');
        renderItemsManual();
        root.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    /* ---------- Foto: selección, análisis y guardado ---------- */
    function validarArchivo(archivo) {
        if (!archivo.type.startsWith('image/')) return 'El archivo debe ser una imagen.';
        if (archivo.size > MAX_FOTO_MB * 1024 * 1024) return `La imagen supera ${MAX_FOTO_MB} MB.`;
        return null;
    }

    function liberarVistaPrevia() {
        if (el.fotoPreview.src && el.fotoPreview.src.startsWith('blob:')) {
            URL.revokeObjectURL(el.fotoPreview.src);
        }
    }

    function seleccionarFoto(archivo) {
        const error = validarArchivo(archivo);
        if (error) {
            mostrarToast(error, true);
            el.fotoInput.value = '';
            return;
        }

        liberarVistaPrevia();
        estado.fotoArchivo = archivo;
        estado.itemsFoto = [];

        el.fotoPreview.src = URL.createObjectURL(archivo);
        el.fotoPreview.alt = 'Vista previa de la foto de la comida';
        el.fotoPreview.hidden = false;
        el.fotoTexto.hidden = true;
        el.btnAnalizar.disabled = false;
        el.btnQuitarFoto.hidden = false;
        el.resultadoFoto.hidden = true;
    }

    function quitarFoto() {
        liberarVistaPrevia();
        estado.fotoArchivo = null;
        estado.itemsFoto = [];
        el.fotoInput.value = '';
        el.fotoPreview.hidden = true;
        el.fotoPreview.removeAttribute('src');
        el.fotoTexto.hidden = false;
        el.btnAnalizar.disabled = true;
        el.btnQuitarFoto.hidden = true;
        el.resultadoFoto.hidden = true;
    }

    async function analizarFoto() {
        if (!estado.fotoArchivo) return;

        const restaurar = ponerCargando(el.btnAnalizar, 'Analizando…');

        const formData = new FormData();
        formData.append('foto', estado.fotoArchivo);

        try {
            // Respuesta esperada: { alimentos: [{ nombre, porcion, confianza }] }
            const datos = await api('/fotos/identificar', { metodo: 'POST', cuerpo: formData, esFormData: true });

            estado.itemsFoto = (datos.alimentos ?? []).map((a) => ({
                nombre: a.nombre || '',
                porcion: a.porcion || '',
                confianza: typeof a.confianza === 'number' ? a.confianza : undefined,
            }));

            if (estado.itemsFoto.length === 0) {
                estado.itemsFoto.push({ nombre: '', porcion: '' });
                mostrarToast('No se identificaron alimentos. Escríbelos manualmente.');
            }
        } catch (err) {
            estado.itemsFoto = [{ nombre: '', porcion: '' }];
            mostrarToast(`${err.message} Puedes escribir los alimentos manualmente.`, true);
        } finally {
            el.resultadoFoto.hidden = false;
            renderItemsFoto();
            restaurar();
        }
    }

    async function guardarFoto() {
        if (estado.guardando) return;

        if (!estado.fotoArchivo) {
            mostrarToast('Primero selecciona una foto.', true);
            return;
        }

        const items = leerItems(el.itemsFoto);
        if (items.length === 0) {
            mostrarToast('Confirma al menos un alimento antes de guardar.', true);
            return;
        }

        const formData = new FormData();
        formData.append('foto', estado.fotoArchivo);
        formData.append('fecha', estado.fecha);
        formData.append('tipo', el.tipoFoto.value);
        formData.append('origen', 'foto');
        formData.append('alimentos', JSON.stringify(items));

        const boton = root.querySelector('[data-action="guardar-foto"]');
        const restaurar = ponerCargando(boton, 'Guardando…');
        estado.guardando = true;

        try {
            await api('/comidas', { metodo: 'POST', cuerpo: formData, esFormData: true });
            mostrarToast('Comida con foto guardada');
            quitarFoto();
            await cargarDia();
        } catch (err) {
            mostrarToast(err.message, true);
        } finally {
            estado.guardando = false;
            restaurar();
        }
    }

    /* ---------- Eliminar (con confirmación) ---------- */
    let botonQueAbrioModal = null;

    function abrirModal(id, boton) {
        estado.pendienteBorrarId = id;
        botonQueAbrioModal = boton;
        el.modal.hidden = false;
        el.modal.querySelector('[data-action="modal-confirmar"]').focus();
    }

    function cerrarModal() {
        estado.pendienteBorrarId = null;
        el.modal.hidden = true;
        if (botonQueAbrioModal) botonQueAbrioModal.focus();
        botonQueAbrioModal = null;
    }

    async function confirmarEliminar() {
        const id = estado.pendienteBorrarId;
        if (!id) return;

        const boton = el.modal.querySelector('[data-action="modal-confirmar"]');
        const restaurar = ponerCargando(boton, 'Eliminando…');

        try {
            await api(`/comidas/${id}`, { metodo: 'DELETE' });

            // Si estabas editando esa misma comida, limpia el formulario
            if (String(estado.editandoId) === String(id)) limpiarFormularioManual();

            mostrarToast('Comida eliminada');
            cerrarModal();
            await cargarDia();
        } catch (err) {
            mostrarToast(err.message, true);
        } finally {
            restaurar();
        }
    }

    /* ---------- Restricciones ---------- */
    async function guardarRestriccion(evento) {
        evento.preventDefault();

        const texto = el.restriccionTexto.value.trim();
        if (texto.length < 3) {
            mostrarToast('Escribe al menos 3 letras.', true);
            return;
        }

        const repetida = estado.restricciones.some((r) => normalizar(r.texto) === normalizar(texto));
        if (repetida) {
            mostrarToast('Ya tienes esa restricción registrada.', true);
            return;
        }

        const boton = el.restriccionForm.querySelector('button[type="submit"]');
        const restaurar = ponerCargando(boton, '…');

        try {
            await api('/restricciones', {
                metodo: 'POST',
                cuerpo: { texto, tipo: el.restriccionTipo.value },
            });
            el.restriccionTexto.value = '';
            mostrarToast('Restricción añadida');
            await cargarRestricciones();
        } catch (err) {
            mostrarToast(err.message, true);
        } finally {
            restaurar();
        }
    }

    async function quitarRestriccion(id) {
        try {
            await api(`/restricciones/${id}`, { metodo: 'DELETE' });
            mostrarToast('Restricción eliminada');
            await cargarRestricciones();
        } catch (err) {
            mostrarToast(err.message, true);
        }
    }

    /* ---------- Agua ---------- */
    async function sumarAgua(ml) {
        const anterior = estado.aguaHoy;
        estado.aguaHoy = anterior + ml; // respuesta inmediata en pantalla
        renderEstadisticas();

        try {
            const datos = await api('/agua', { metodo: 'POST', cuerpo: { fecha: estado.fecha, ml } });
            if (typeof datos.agua_ml === 'number') {
                estado.aguaHoy = datos.agua_ml; // valor real del servidor
                renderEstadisticas();
            }
        } catch (err) {
            estado.aguaHoy = anterior; // revierte si no se guardó
            renderEstadisticas();
            mostrarToast(err.message, true);
        }
    }

    /* ---------- Arrastrar y soltar foto ---------- */
    function enlazarDropzone() {
        if (!el.dropzone) return;

        let contador = 0; // evita parpadeo al pasar sobre elementos hijos

        el.dropzone.addEventListener('dragenter', (e) => {
            e.preventDefault();
            contador++;
            el.dropzone.classList.add('is-dragover');
        });

        el.dropzone.addEventListener('dragover', (e) => e.preventDefault());

        el.dropzone.addEventListener('dragleave', () => {
            contador--;
            if (contador <= 0) {
                contador = 0;
                el.dropzone.classList.remove('is-dragover');
            }
        });

        el.dropzone.addEventListener('drop', (e) => {
            e.preventDefault();
            contador = 0;
            el.dropzone.classList.remove('is-dragover');
            const archivo = e.dataTransfer.files[0];
            if (archivo) seleccionarFoto(archivo);
        });
    }

    /* ---------- Eventos ---------- */
    function enlazarEventos() {
        el.fecha.addEventListener('change', (e) => {
            estado.fecha = e.target.value || hoyISO();
            cargarDia();
        });

        // Delegación de clics: un solo listener para todos los botones
        root.addEventListener('click', (e) => {
            const boton = e.target.closest('[data-action], [data-tab], [data-filtro], [data-agua]');
            if (!boton || !root.contains(boton)) return;

            if (boton.dataset.tab) return cambiarPestana(boton.dataset.tab);

            if (boton.dataset.filtro) {
                estado.filtro = boton.dataset.filtro;
                root.querySelectorAll('[data-filtro]').forEach((b) => {
                    const activo = b === boton;
                    b.classList.toggle('is-active', activo);
                    b.setAttribute('aria-pressed', String(activo));
                });
                return renderHistorial();
            }

            if (boton.dataset.agua) return sumarAgua(parseInt(boton.dataset.agua, 10));

            switch (boton.dataset.action) {
                case 'hoy':
                    estado.fecha = hoyISO();
                    el.fecha.value = estado.fecha;
                    return cargarDia();

                case 'add-item':
                    estado.itemsManual.push({ nombre: '', porcion: '' });
                    return renderItemsManual();

                case 'add-item-foto':
                    estado.itemsFoto.push({ nombre: '', porcion: '' });
                    return renderItemsFoto();

                case 'quitar-item': {
                    const fila = boton.closest('.nut-item');
                    const origen = fila.dataset.origen;
                    const idx = parseInt(fila.dataset.index, 10);
                    if (origen === 'manual') {
                        estado.itemsManual.splice(idx, 1);
                        renderItemsManual();
                    } else {
                        estado.itemsFoto.splice(idx, 1);
                        renderItemsFoto();
                    }
                    return;
                }

                case 'editar': {
                    const comida = estado.comidas.find((c) => String(c.id) === boton.dataset.id);
                    if (comida) cargarEnFormulario(comida);
                    return;
                }

                case 'eliminar':
                    return abrirModal(boton.dataset.id, boton);

                case 'modal-cancelar':
                    return cerrarModal();

                case 'modal-confirmar':
                    return confirmarEliminar();

                case 'cancelar-edicion':
                    return limpiarFormularioManual();

                case 'quitar-foto':
                    return quitarFoto();

                case 'guardar-foto':
                    return guardarFoto();

                case 'quitar-restriccion':
                    return quitarRestriccion(boton.dataset.id);
            }
        });

        // Al escribir en un alimento se actualizan las alertas en vivo
        root.addEventListener('input', (e) => {
            const fila = e.target.closest('.nut-item');
            if (!fila) return;

            const idx = parseInt(fila.dataset.index, 10);
            const campo = e.target.dataset.campo;
            const lista = fila.dataset.origen === 'manual' ? estado.itemsManual : estado.itemsFoto;

            if (lista[idx]) lista[idx][campo] = e.target.value;
            revisarAlertas();
        });

        el.formManual.addEventListener('submit', guardarManual);
        el.restriccionForm.addEventListener('submit', guardarRestriccion);

        el.fotoInput.addEventListener('change', (e) => {
            const archivo = e.target.files[0];
            if (archivo) seleccionarFoto(archivo);
        });

        el.btnAnalizar.addEventListener('click', analizarFoto);

        // Cerrar el modal con Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && !el.modal.hidden) cerrarModal();
        });

        // Al cerrar la página con una vista previa abierta, libera la memoria
        window.addEventListener('beforeunload', liberarVistaPrevia);
    }

    /* ---------- Inicio ---------- */
    function iniciar() {
        el.fecha.value = estado.fecha;
        renderItemsManual();
        enlazarDropzone();
        enlazarEventos();
        cargarRestricciones();
        cargarDia();
    }

    iniciar();
})();