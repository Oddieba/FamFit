{{-- Ajusta 'layouts.famfit' al nombre del layout que usa tu proyecto (el que trae la barra lateral) --}}
@extends('layouts.quest')

@section('title', 'Nutrición · FamFit')

@push('styles')
    {{-- Ajusta la ruta si tu CSS está en public/css en lugar de public/estilos --}}
    <link rel="stylesheet" href="{{ asset('estilos/nutricion.css') }}">
@endpush

@section('content')
<div class="nut-page"
     id="nutricion"
     data-api="{{ url('/nutricion') }}"
     data-csrf="{{ csrf_token() }}">

    {{-- ENCABEZADO --}}
    <header class="nut-header">
        <p class="nut-eyebrow">Tu alimentación</p>
        <h1 class="nut-title">Nutrición</h1>
        <p class="nut-subtitle">Registra lo que comes por foto o manual, revisa tu historial y recibe advertencias según tus alergias.</p>
    </header>

    {{-- BARRA DE FECHA --}}
    <section class="nut-card nut-day-bar">
        <div>
            <p class="nut-label">Día seleccionado</p>
            <h2 class="nut-day" id="nut-dia-texto">—</h2>
        </div>
        <div class="nut-day-controls">
            <input type="date" id="nut-fecha" class="nut-input" value="{{ now()->toDateString() }}" aria-label="Elegir fecha">
            <button type="button" class="nut-btn nut-btn-ghost" data-action="hoy">Hoy</button>
        </div>
    </section>

    {{-- RESUMEN --}}
    <section class="nut-stats" aria-label="Resumen del día">
        <article class="nut-stat">
            <span class="nut-stat-icon nut-icon-blue">🍽</span>
            <div>
                <p class="nut-stat-label">Comidas registradas</p>
                <p class="nut-stat-value" id="nut-stat-comidas">0</p>
            </div>
        </article>

        <article class="nut-stat">
            <span class="nut-stat-icon nut-icon-teal">📷</span>
            <div>
                <p class="nut-stat-label">Con fotografía</p>
                <p class="nut-stat-value" id="nut-stat-fotos">0</p>
            </div>
        </article>

        <article class="nut-stat">
            <span class="nut-stat-icon nut-icon-amber">🔥</span>
            <div>
                <p class="nut-stat-label">Racha de registro</p>
                <p class="nut-stat-value" id="nut-racha">0 <small>días</small></p>
            </div>
        </article>
    </section>

    <div class="nut-grid">

        {{-- COLUMNA IZQUIERDA: REGISTRO --}}
        <div class="nut-col-main">

            <section class="nut-card">
                <p class="nut-label">Registrar comida</p>
                <h2 class="nut-card-title" id="nut-form-titulo">Nueva comida</h2>

                <div class="nut-tabs" role="tablist">
                    <button type="button" class="nut-tab is-active" data-tab="manual" role="tab" aria-selected="true">✏️ Manual</button>
                    <button type="button" class="nut-tab" data-tab="foto" role="tab" aria-selected="false">📷 Foto</button>
                </div>

                <div class="nut-banner nut-banner-warning" role="note">
                    <strong>Aviso:</strong> el reconocimiento por imagen puede fallar. No confirma ingredientes, alérgenos ni contaminación cruzada. Revisa siempre antes de guardar.
                </div>

                {{-- Panel manual --}}
                <form id="nut-form-manual" class="nut-form" novalidate>
                    <input type="hidden" id="nut-comida-id" value="">

                    <div class="nut-row">
                        <div class="nut-field">
                            <label for="nut-tipo">Tipo de comida</label>
                            <select id="nut-tipo" class="nut-input" required>
                                <option value="desayuno">Desayuno</option>
                                <option value="almuerzo">Almuerzo</option>
                                <option value="cena">Cena</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>
                        <div class="nut-field">
                            <label for="nut-hora">Hora</label>
                            <input type="time" id="nut-hora" class="nut-input">
                        </div>
                    </div>

                    <div class="nut-field">
                        <label for="nut-descripcion">Descripción del plato</label>
                        <input type="text" id="nut-descripcion" class="nut-input" placeholder="Ej. Arepa con huevo y café" maxlength="120">
                    </div>

                    <div class="nut-field">
                        <div class="nut-field-head">
                            <label>Alimentos y porciones</label>
                            <button type="button" class="nut-btn nut-btn-ghost nut-btn-sm" data-action="add-item">+ Añadir alimento</button>
                        </div>
                        <div id="nut-items-manual" class="nut-items"></div>
                    </div>

                    <div id="nut-alertas-borrador" class="nut-alerts" aria-live="polite"></div>

                    <div class="nut-field">
                        <label for="nut-notas">Notas (opcional)</label>
                        <textarea id="nut-notas" class="nut-input" rows="2" maxlength="300" placeholder="Ej. Comí fuera de casa"></textarea>
                    </div>

                    <div class="nut-actions">
                        <button type="button" class="nut-btn nut-btn-secondary" data-action="cancelar-edicion" hidden>Cancelar edición</button>
                        <button type="submit" class="nut-btn nut-btn-primary">Guardar comida</button>
                    </div>
                </form>

                {{-- Panel foto --}}
                <div id="nut-form-foto" class="nut-form" hidden>
                    <label class="nut-dropzone" for="nut-foto-input">
                        <input type="file" id="nut-foto-input" accept="image/*" capture="environment" hidden>
                        <img id="nut-foto-preview" class="nut-preview" alt="" hidden>
                        <span id="nut-foto-texto">
                            <strong>Toca para tomar o cargar una foto</strong><br>
                            <small>JPG, PNG o WEBP · máximo 5 MB</small>
                        </span>
                    </label>

                    <div class="nut-actions nut-actions-left">
                        <button type="button" class="nut-btn nut-btn-primary" id="nut-btn-analizar" disabled>Identificar alimentos</button>
                        <button type="button" class="nut-btn nut-btn-ghost" data-action="quitar-foto" hidden>Quitar foto</button>
                    </div>

                    <div id="nut-resultado-foto" hidden>
                        <p class="nut-label">Identificación orientativa</p>
                        <p class="nut-muted">Revisa, corrige nombres y ajusta porciones antes de guardar.</p>

                        <div id="nut-items-foto" class="nut-items"></div>
                        <button type="button" class="nut-btn nut-btn-ghost nut-btn-sm" data-action="add-item-foto">+ Añadir alimento</button>

                        <div id="nut-alertas-foto" class="nut-alerts" aria-live="polite"></div>

                        <div class="nut-field">
                            <label for="nut-tipo-foto">Tipo de comida</label>
                            <select id="nut-tipo-foto" class="nut-input">
                                <option value="desayuno">Desayuno</option>
                                <option value="almuerzo">Almuerzo</option>
                                <option value="cena">Cena</option>
                                <option value="snack">Snack</option>
                            </select>
                        </div>

                        <div class="nut-actions">
                            <button type="button" class="nut-btn nut-btn-primary" data-action="guardar-foto">Guardar comida con foto</button>
                        </div>
                    </div>
                </div>
            </section>

            {{-- HISTORIAL --}}
            <section class="nut-card">
                <div class="nut-card-head">
                    <div>
                        <p class="nut-label">Historial</p>
                        <h2 class="nut-card-title">Comidas del día</h2>
                    </div>
                    <div class="nut-filter" role="group" aria-label="Filtrar por tipo">
                        <button type="button" class="nut-chip is-active" data-filtro="todas">Todas</button>
                        <button type="button" class="nut-chip" data-filtro="desayuno">Desayuno</button>
                        <button type="button" class="nut-chip" data-filtro="almuerzo">Almuerzo</button>
                        <button type="button" class="nut-chip" data-filtro="cena">Cena</button>
                        <button type="button" class="nut-chip" data-filtro="snack">Snack</button>
                    </div>
                </div>
                <div id="nut-historial" class="nut-historial" aria-live="polite"></div>
            </section>
        </div>

        {{-- COLUMNA DERECHA --}}
        <aside class="nut-col-side">
            <section class="nut-card">
                <p class="nut-label">Tus restricciones</p>
                <h2 class="nut-card-title">Alergias e intolerancias</h2>
                <p class="nut-muted">Se usan para mostrarte advertencias al registrar comidas.</p>

                <form id="nut-form-restriccion" class="nut-inline-form">
                    <input type="text" id="nut-restriccion-texto" class="nut-input" placeholder="Ej. maní, lactosa, cilantro" maxlength="60" required>
                    <select id="nut-restriccion-tipo" class="nut-input">
                        <option value="alergia">Alergia</option>
                        <option value="intolerancia">Intolerancia</option>
                        <option value="evita">Evito</option>
                    </select>
                    <button type="submit" class="nut-btn nut-btn-primary nut-btn-sm">Añadir</button>
                </form>

                <ul id="nut-restricciones" class="nut-chips-list"></ul>
            </section>

            <section class="nut-card">
                <p class="nut-label">Resumen</p>
                <h2 class="nut-card-title">Datos nutricionales</h2>
                <p class="nut-muted">Solo mostramos estimaciones cuando la fuente de datos es fiable. Si no hay fuente, verás "Sin datos verificados".</p>
                <div class="nut-empty-mini">Las estimaciones aparecerán aquí cuando el catálogo tenga fuente.</div>
            </section>
        </aside>
    </div>

    {{-- MODAL CONFIRMAR ELIMINAR --}}
    <div class="nut-modal" id="nut-modal" hidden role="dialog" aria-modal="true" aria-labelledby="nut-modal-titulo">
        <div class="nut-modal-box">
            <h3 id="nut-modal-titulo">¿Eliminar esta comida?</h3>
            <p class="nut-muted">Se borrarán también sus fotografías. Esta acción no se puede deshacer.</p>
            <div class="nut-actions">
                <button type="button" class="nut-btn nut-btn-secondary" data-action="modal-cancelar">Cancelar</button>
                <button type="button" class="nut-btn nut-btn-danger" data-action="modal-confirmar">Eliminar</button>
            </div>
        </div>
    </div>

    <div class="nut-toast" id="nut-toast" role="status" aria-live="polite" hidden></div>
</div>
@endsection

@push('scripts')
    <script src="{{ asset('js/nutricion.js') }}" defer></script>
@endpush