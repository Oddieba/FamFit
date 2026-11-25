@extends('layouts.quest')

@section('title', 'Crear Rutina | FamFit Quest')

{{-- =========================================================
     ESTILOS + TAILWIND (van al <head> del layout con @stack('styles'))
     ========================================================= --}}
@push('styles')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            primary: '#2864F0',
                            surface: '#FFFFFF',
                            'surface-muted': '#EEF2F8',
                            'text-primary': '#172033',
                            'text-secondary': '#64748B',
                            border: '#DCE4EF',
                            success: '#A7E66D',
                            habit: '#19C6B0',
                            warning: '#F2B85B',
                            danger: '#E45865'
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    }
                }
            }
        };
    </script>

    <style>
        /* ---------- 1. La página NO hace scroll: todo vive dentro de la tarjeta ---------- */
        html, body {
            height: 100%;
            overflow: hidden;
        }

        .fq-main {
            box-sizing: border-box;
            height: 100vh;               /* fallback */
            height: 100dvh;              /* alto real en móviles (barra del navegador) */
            max-height: 100dvh;
            min-height: 0;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }

        .fq-page {
            flex: 1 1 auto;
            min-height: 0;
            display: flex;
            flex-direction: column;
        }

        .fq-page > .fq-alert {
            flex: none;                  /* los avisos de sesión no deforman la tarjeta */
        }

        /* ---------- 2. Catálogo: columnas automáticas según el ancho disponible ---------- */
        .fq-catalog-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 0.875rem;
            align-content: start;
        }

        /* ---------- 3. Móvil / tablet: un panel a la vez (Catálogo o Mi rutina) ---------- */
        @media (max-width: 1023.98px) {
            #routineBody[data-mobile-panel="catalog"] #panelRoutine { display: none; }
            #routineBody[data-mobile-panel="routine"] #panelCatalog { display: none; }
        }

        /* ---------- Scrollbars internos ---------- */
        .custom-scroll::-webkit-scrollbar { width: 8px; height: 8px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 9999px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        @keyframes slideIn {
            from { opacity: 0; transform: translateY(8px); }
            to   { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideIn 0.2s ease-out forwards; }

        .force-white-text, .force-white-text * { color: #ffffff !important; }

        /* Inputs numéricos: ocultamos los spinners nativos (solo aparecen al hover y quedan pegados al borde)
           y usamos nuestras propias flechas, siempre visibles */
        .fq-num { -moz-appearance: textfield; appearance: textfield; }
        .fq-num::-webkit-outer-spin-button,
        .fq-num::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    </style>
@endpush

@section('content')
    <!-- Tarjeta principal: ocupa todo el alto disponible y NUNCA crece más que la pantalla -->
    <div class="relative w-full flex-1 min-h-0 bg-white rounded-3xl shadow-sm border border-brand-border flex flex-col overflow-hidden">

        <!-- Aviso flotante (no depende de qué panel esté visible en móvil) -->
        <div class="absolute inset-x-0 top-24 z-30 flex justify-center px-4 pointer-events-none">
            <div id="feedbackBanner" class="hidden text-sm font-bold px-4 py-2.5 rounded-2xl text-center shadow-lg"></div>
        </div>

        <!-- Header -->
        <header class="flex-none px-4 sm:px-6 lg:px-8 py-4 border-b border-brand-border flex items-center justify-between gap-4 bg-white z-10">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                <a href="javascript:history.back()"
                   class="w-11 h-11 rounded-full bg-brand-surface-muted flex items-center justify-center hover:bg-slate-200 transition-colors shrink-0"
                   title="Volver">
                    <i class="fa-solid fa-arrow-left text-brand-text-secondary"></i>
                </a>
                <div class="min-w-0">
                    <div class="flex items-center gap-2.5 flex-wrap">
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight leading-none truncate text-brand-text-primary">Creador de Rutinas</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold bg-blue-50 text-brand-primary border border-blue-100 shrink-0">
                            FamFit Quest
                        </span>
                    </div>
                    <p class="text-sm text-brand-text-secondary font-medium mt-1.5 truncate">
                        Arma tu sesión, ajusta series y repeticiones y controla el nivel de exigencia.
                    </p>
                </div>
            </div>

            <!-- Resumen de la rutina -->
            <div class="hidden md:flex items-center gap-5 bg-brand-surface-muted/70 px-6 py-3 rounded-2xl border border-brand-border/70 shrink-0">
                <div>
                    <span class="text-brand-text-secondary block text-[11px] font-bold uppercase tracking-wide">Ejercicios</span>
                    <strong id="statExercises" class="text-lg font-black text-brand-text-primary">0 / 12</strong>
                </div>
                <div class="h-8 w-px bg-brand-border"></div>
                <div>
                    <span class="text-brand-text-secondary block text-[11px] font-bold uppercase tracking-wide">Series totales</span>
                    <strong id="statSets" class="text-lg font-black text-brand-text-primary">0</strong>
                </div>
                <div class="h-8 w-px bg-brand-border"></div>
                <div>
                    <span class="text-brand-text-secondary block text-[11px] font-bold uppercase tracking-wide">Tiempo est.</span>
                    <strong id="statTime" class="text-lg font-black text-brand-primary">0 min</strong>
                </div>
            </div>
        </header>

        <!-- Pestañas solo para pantallas pequeñas -->
        <div class="lg:hidden flex-none px-3 py-2 border-b border-brand-border bg-white">
            <div class="grid grid-cols-2 gap-1 bg-brand-surface-muted rounded-full p-1">
                <button type="button" id="tabCatalog" onclick="setMobilePanel('catalog')"
                        class="py-2.5 rounded-full text-sm font-bold transition-all">
                    Catálogo
                </button>
                <button type="button" id="tabRoutine" onclick="setMobilePanel('routine')"
                        class="py-2.5 rounded-full text-sm font-bold transition-all">
                    Mi rutina <span id="tabRoutineCount" class="ml-1 px-2 py-0.5 rounded-full text-xs bg-brand-primary text-white">0</span>
                </button>
            </div>
        </div>

        <!-- Cuerpo: 2 columnas en escritorio, 1 panel a la vez en móvil -->
        <div id="routineBody" data-mobile-panel="catalog" class="flex-1 min-h-0 flex flex-col lg:flex-row overflow-hidden">

            <!-- COLUMNA IZQUIERDA: Catálogo -->
            <section id="panelCatalog" class="flex-1 min-h-0 min-w-0 flex flex-col bg-brand-surface-muted/35 lg:border-r border-brand-border overflow-hidden">

                <!-- Buscador y filtros -->
                <div class="p-4 sm:p-5 flex-none border-b border-brand-border/60 bg-white/85">
                    <div class="flex flex-col sm:flex-row gap-3 mb-3.5">
                        <div class="relative flex-1">
                            <i class="fa-solid fa-magnifying-glass absolute left-4 top-1/2 -translate-y-1/2 text-sm text-brand-text-secondary"></i>
                            <input type="text"
                                   id="searchExercise"
                                   placeholder="Buscar por nombre, equipo o zona objetivo..."
                                   autocomplete="off"
                                   class="w-full pl-11 pr-4 py-3 rounded-full border border-brand-border focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/15 outline-none text-base bg-white transition-all">
                        </div>
                        <button type="button"
                                id="btnOnlyRecommended"
                                onclick="toggleRecommendedFilter()"
                                class="px-5 py-3 rounded-full text-sm font-bold border border-brand-border bg-white text-brand-text-secondary hover:border-brand-primary hover:text-brand-primary transition-all flex items-center justify-center gap-2 shrink-0">
                            <i class="fa-solid fa-star text-amber-500"></i>
                            <span>Recomendados</span>
                        </button>
                    </div>

                    <!-- Pills generadas por JS según los grupos musculares que existan en tus datos -->
                    <div class="flex items-center gap-2 overflow-x-auto pb-1.5 custom-scroll" id="muscleFilters"></div>
                </div>

                <!-- Grid con scroll independiente -->
                <div class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-5 custom-scroll">
                    <div id="exerciseCatalog" class="fq-catalog-grid">
                        <!-- Renderizado por JS -->
                    </div>
                </div>
            </section>

            <!-- COLUMNA DERECHA: Constructor de rutina -->
            <aside id="panelRoutine" class="flex-1 lg:flex-none lg:w-[420px] xl:w-[470px] 2xl:w-[540px] min-h-0 flex flex-col bg-white overflow-hidden">

                <!-- Configuración y medidor de dificultad -->
                <div class="p-4 sm:p-5 border-b border-brand-border flex-none bg-white z-10">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-4">
                        <div class="sm:col-span-2">
                            <label for="routineName" class="text-[11px] font-extrabold text-brand-text-secondary uppercase tracking-widest mb-1.5 block">
                                Nombre de tu rutina
                            </label>
                            <input type="text"
                                   id="routineName"
                                   maxlength="60"
                                   placeholder="Ej: Full Body Express..."
                                   class="w-full px-4 py-2.5 rounded-full border border-brand-border focus:border-brand-primary focus:ring-2 focus:ring-brand-primary/15 outline-none text-base font-semibold bg-brand-surface-muted/60 transition-all">
                        </div>

                        <div>
                            <label for="routineEnv" class="text-[11px] font-extrabold text-brand-text-secondary uppercase tracking-widest mb-1.5 block">
                                Entorno
                            </label>
                            <select id="routineEnv"
                                    class="w-full px-3.5 py-2.5 rounded-full border border-brand-border focus:border-brand-primary outline-none text-sm font-bold bg-brand-surface-muted/60 text-brand-text-primary transition-all">
                                <option value="home">En casa</option>
                                <option value="gym">Gimnasio</option>
                                <option value="outdoor">Aire libre</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-2">
                            <label class="text-[11px] font-extrabold text-brand-text-secondary uppercase tracking-widest">
                                Dificultad estimada
                            </label>
                            <button type="button"
                                    onclick="openInfoModal()"
                                    class="text-xs text-brand-primary hover:underline font-bold flex items-center gap-1.5">
                                <i class="fa-solid fa-circle-info"></i>
                                <span>¿Cómo se calcula?</span>
                            </button>
                        </div>

                        <div class="flex items-center gap-3">
                            <div class="relative flex-1 h-3 bg-slate-200 rounded-full overflow-hidden">
                                <div id="difficultyBar" class="h-full bg-slate-300 rounded-full transition-all duration-300 ease-out" style="width: 0%"></div>
                                <!-- Marcas donde cambia de nivel (35 y 65 pts) -->
                                <span class="absolute inset-y-0 w-0.5 bg-white/90" style="left: 35%"></span>
                                <span class="absolute inset-y-0 w-0.5 bg-white/90" style="left: 65%"></span>
                            </div>
                            <span id="difficultyText" class="text-sm font-black text-slate-400 min-w-[96px] text-right">Vacía</span>
                        </div>
                        <p id="difficultyDetail" class="mt-1.5 text-xs text-brand-text-secondary font-medium min-h-[16px]"></p>

                        <div id="warningMsg" class="hidden mt-2.5 bg-red-50 text-red-600 text-xs font-bold px-3.5 py-2.5 rounded-xl text-center border border-red-100 animate-slide-in">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>
                            Esta rutina supera tu nivel actual en el test.
                        </div>
                    </div>
                </div>

                <!-- Lista de ejercicios añadidos (scroll propio: aquí se acumulan los ejercicios) -->
                <div id="routineBlocks" class="flex-1 min-h-0 overflow-y-auto p-4 sm:p-5 custom-scroll relative bg-brand-surface-muted/25">
                    <div id="emptyState" class="h-full flex flex-col items-center justify-center text-brand-text-secondary select-none py-6">
                        <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center mb-4 shadow-sm border border-brand-border">
                            <i class="fa-solid fa-dumbbell text-3xl text-brand-primary"></i>
                        </div>
                        <p class="text-base font-bold text-brand-text-primary">Aún no hay ejercicios</p>
                        <p class="text-sm mt-1.5 text-center max-w-xs">
                            Haz clic en cualquier tarjeta del catálogo para agregarla y personalizar series, reps y descanso.
                        </p>
                    </div>
                </div>

                <!-- Footer fijo con el botón Guardar -->
                <div class="flex-none p-4 sm:p-5 border-t border-brand-border bg-white flex items-center justify-between gap-3">
                    <div class="text-sm text-brand-text-secondary font-medium">
                        <span id="footerSummaryCount" class="font-bold text-brand-text-primary">0 ejercicios</span>
                        <span id="footerSummaryTime" class="md:hidden"></span>
                    </div>
                    <button type="button"
                            onclick="saveRoutine()"
                            id="btnSave"
                            disabled
                            class="px-7 py-3 bg-slate-200 text-slate-400 text-base font-bold rounded-full cursor-not-allowed transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i>
                        <span>Guardar Rutina</span>
                    </button>
                </div>

            </aside>
        </div>
    </div>

    <!-- MODAL EXPLICATIVO -->
    <div id="infoModal" onclick="if (event.target === this) closeInfoModal()"
         class="fixed inset-0 bg-brand-text-primary/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-brand-surface rounded-3xl border border-brand-border shadow-2xl max-w-md w-full overflow-hidden animate-slide-in">
            <div class="bg-brand-surface-muted px-6 py-4 border-b border-brand-border flex items-center justify-between">
                <h3 class="text-base font-extrabold text-brand-text-primary flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-brand-primary"></i>
                    <span>Fórmula de dificultad</span>
                </h3>
                <button type="button" onclick="closeInfoModal()" class="text-brand-text-secondary hover:text-brand-danger w-9 h-9 flex items-center justify-center rounded-full hover:bg-white transition-colors" aria-label="Cerrar">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-6 text-sm text-brand-text-secondary space-y-4">
                <p>La dificultad es un puntaje de 0 a 100 que suma tres factores:</p>
                <ul class="space-y-3">
                    <li class="flex gap-3">
                        <strong class="text-brand-text-primary w-28 shrink-0">Ejercicios (40)</strong>
                        <span>Nivel promedio de los ejercicios elegidos (principiante, intermedio o avanzado), según sus series.</span>
                    </li>
                    <li class="flex gap-3">
                        <strong class="text-brand-text-primary w-28 shrink-0">Volumen (35)</strong>
                        <span>Repeticiones totales de la sesión (series × reps de todos los ejercicios). El máximo se alcanza con 400.</span>
                    </li>
                    <li class="flex gap-3">
                        <strong class="text-brand-text-primary w-28 shrink-0">Descansos (25)</strong>
                        <span>Descanso promedio entre series. Con 90 s o más no suma; con 15 s o menos suma todo.</span>
                    </li>
                </ul>
                <div class="flex flex-col gap-2 text-sm font-bold bg-slate-50 p-4 rounded-2xl border border-brand-border">
                    <span class="flex items-center gap-2.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Principiante (menos de 35 pts)</span>
                    <span class="flex items-center gap-2.5"><span class="w-3 h-3 rounded-full bg-amber-500"></span> Intermedio (35 – 64 pts)</span>
                    <span class="flex items-center gap-2.5"><span class="w-3 h-3 rounded-full bg-red-500"></span> Avanzado (65 pts o más)</span>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        /* ============================================================
           DATOS DESDE LARAVEL
           ============================================================ */
        const allExercises   = @json($todosLosEjercicios ?? []);
        const recommendedIds = (@json($recomendados ?? [])).map(Number);
        const userLevel      = @json($perfil->experience ?? 'beginner');

        /* ============================================================
           CONSTANTES Y ESTADO
           ============================================================ */
        const MAX_EXERCISES = 12;
        const MAX_SETS = 8;
        const MAX_REPS = 30;
        const MAX_REST = 300;

        const LIMITS = {
            sets: [1, MAX_SETS],
            reps: [1, MAX_REPS],
            rest: [0, MAX_REST]
        };

        // Cuánto suben/bajan las flechitas (el descanso avanza de 10 en 10)
        const STEPS = {
            sets: 1,
            reps: 1,
            rest: 10
        };

        // Cálculo de dificultad (puntaje 0-100)
        const SCORE_WEIGHTS = { exercises: 40, volume: 35, rest: 25 };
        const LEVEL_THRESHOLDS = { intermediate: 35, advanced: 65 };
        const VOLUME_MAX_REPS = 400;   // repeticiones totales que dan el puntaje máximo de volumen
        const REST_EASY = 90;          // descanso promedio (s) que no suma nada
        const REST_HARD = 15;          // descanso promedio (s) que suma el máximo

        const exerciseById = new Map(allExercises.map(ex => [Number(ex.id), ex]));

        let routineExercises = [];
        let currentMuscleFilter = 'all';
        let onlyRecommended = false;
        let currentLevel = 'beginner';   // nivel calculado de la rutina actual
        let isSaving = false;

        const muscleColors = {
            'Pecho': 'bg-red-50 text-red-700 border-red-200',
            'Piernas': 'bg-purple-50 text-purple-700 border-purple-200',
            'Espalda': 'bg-blue-50 text-blue-700 border-blue-200',
            'Core': 'bg-orange-50 text-orange-700 border-orange-200',
            'Hombros': 'bg-teal-50 text-teal-700 border-teal-200',
            'Bíceps': 'bg-pink-50 text-pink-700 border-pink-200',
            'Tríceps': 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'Cardio': 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'Default': 'bg-slate-50 text-slate-700 border-slate-200'
        };

        const difficultyLabels = {
            beginner: 'Principiante',
            intermediate: 'Intermedio',
            advanced: 'Avanzado'
        };

        /* ============================================================
           UTILIDADES
           ============================================================ */
        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, c => ({
                '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
            }[c]));
        }

        function showFeedback(message, type = 'warning') {
            const banner = document.getElementById('feedbackBanner');
            const styles = {
                warning: 'bg-amber-50 text-amber-800 border border-amber-200',
                info: 'bg-blue-50 text-brand-primary border border-blue-200'
            };
            banner.className = `text-sm font-bold px-4 py-2.5 rounded-2xl text-center shadow-lg animate-slide-in ${styles[type] || styles.warning}`;
            banner.textContent = message;

            clearTimeout(showFeedback._timer);
            showFeedback._timer = setTimeout(() => banner.classList.add('hidden'), 3000);
        }

        /* ============================================================
           PANELES EN MÓVIL
           ============================================================ */
        function setMobilePanel(panel) {
            document.getElementById('routineBody').dataset.mobilePanel = panel;

            const active = 'py-2.5 rounded-full text-sm font-bold transition-all bg-white text-brand-primary shadow-sm';
            const idle = 'py-2.5 rounded-full text-sm font-bold transition-all text-brand-text-secondary';
            document.getElementById('tabCatalog').className = panel === 'catalog' ? active : idle;
            document.getElementById('tabRoutine').className = panel === 'routine' ? active : idle;
        }

        /* ============================================================
           FILTROS
           ============================================================ */
        function pillClass(isActive) {
            return isActive
                ? 'filter-pill force-white-text px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap border border-brand-primary bg-brand-primary transition-all'
                : 'filter-pill px-4 py-2 rounded-full text-sm font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-slate-50 transition-all';
        }

        function renderMuscleFilters() {
            const container = document.getElementById('muscleFilters');
            const present = [...new Set(allExercises.map(ex => ex.muscle_group).filter(Boolean))];

            // Orden: primero los grupos conocidos (en el orden de muscleColors), luego cualquier otro
            const known = Object.keys(muscleColors).filter(k => k !== 'Default');
            const groups = [
                ...known.filter(k => present.includes(k)),
                ...present.filter(k => !known.includes(k))
            ];

            container.innerHTML = ['all', ...groups].map(group => `
                <button type="button" data-muscle="${escapeHtml(group)}" class="${pillClass(group === currentMuscleFilter)}">
                    ${group === 'all' ? 'Todos' : escapeHtml(group)}
                </button>
            `).join('');
        }

        function setFilter(muscle) {
            currentMuscleFilter = muscle;
            document.querySelectorAll('.filter-pill').forEach(btn => {
                btn.className = pillClass(btn.dataset.muscle === muscle);
            });
            renderCatalog();
        }

        function toggleRecommendedFilter() {
            onlyRecommended = !onlyRecommended;
            const btn = document.getElementById('btnOnlyRecommended');
            btn.className = onlyRecommended
                ? 'px-5 py-3 rounded-full text-sm font-bold border border-amber-400 bg-amber-50 text-amber-900 transition-all flex items-center justify-center gap-2 shrink-0'
                : 'px-5 py-3 rounded-full text-sm font-bold border border-brand-border bg-white text-brand-text-secondary hover:border-brand-primary hover:text-brand-primary transition-all flex items-center justify-center gap-2 shrink-0';
            renderCatalog();
        }

        /* ============================================================
           CATÁLOGO
           ============================================================ */
        function exerciseCardHTML(ex) {
            const id = Number(ex.id);
            const isAdded = routineExercises.some(e => Number(e.id) === id);
            const isRec = recommendedIds.includes(id);
            const muscleClass = muscleColors[ex.muscle_group] || muscleColors['Default'];
            const diff = difficultyLabels[ex.difficulty] || difficultyLabels.beginner;
            const equipment = escapeHtml(ex.equipment || 'Peso corporal');

            // Ambos estados usan border-2: así el borde no cambia de grosor y la tarjeta no crece al seleccionarla
            const cardClass = isAdded
                ? 'text-left p-4 rounded-2xl border-2 border-emerald-500 bg-emerald-50/80 hover:bg-red-50 hover:border-red-300 transition-colors flex flex-col justify-between group min-h-[132px]'
                : 'text-left p-4 rounded-2xl border-2 border-brand-border bg-white hover:border-brand-primary hover:shadow-md transition-colors flex flex-col justify-between group min-h-[132px]';

            return `
                <button type="button" data-id="${id}" class="${cardClass}">
                    <div>
                        <div class="flex items-center justify-between gap-2 mb-2.5 h-7">
                            <div class="flex items-center gap-1.5 min-w-0">
                                <span class="text-[10px] font-extrabold px-2.5 py-1 rounded-full border ${muscleClass} uppercase tracking-wider truncate">
                                    ${escapeHtml(ex.muscle_group || 'General')}
                                </span>
                                ${isRec ? `<span class="text-[11px] font-extrabold px-2 py-0.5 rounded-full bg-amber-100 text-amber-800 border border-amber-200 shrink-0" title="Recomendado para ti">★</span>` : ''}
                            </div>
                            ${isAdded
                                ? `<span class="shrink-0 w-6 h-6 flex items-center justify-center text-lg">
                                       <i class="fa-solid fa-circle-check text-emerald-600 group-hover:hidden"></i>
                                       <i class="fa-solid fa-circle-minus text-red-500 hidden group-hover:inline"></i>
                                   </span>`
                                : `<span class="shrink-0 w-6 h-6 flex items-center justify-center text-sm text-brand-text-secondary group-hover:text-brand-primary transition-colors"><i class="fa-solid fa-plus"></i></span>`
                            }
                        </div>
                        <p class="font-bold text-[15px] leading-snug ${isAdded ? 'text-emerald-950 group-hover:text-red-700' : 'text-brand-text-primary group-hover:text-brand-primary'} line-clamp-2">
                            ${escapeHtml(ex.name)}
                        </p>
                    </div>
                    <div class="flex justify-between items-center text-xs mt-3 pt-3 border-t border-slate-100 text-brand-text-secondary">
                        <span class="font-bold uppercase">${diff}</span>
                        <span class="truncate ml-2" title="${equipment}">
                            <i class="fa-solid fa-dumbbell mr-1"></i>${equipment}
                        </span>
                    </div>
                </button>
            `;
        }

        function renderCatalog() {
            const catalog = document.getElementById('exerciseCatalog');
            const query = (document.getElementById('searchExercise')?.value || '').trim().toLowerCase();

            const filtered = allExercises.filter(ex => {
                const matchesMuscle = currentMuscleFilter === 'all' || ex.muscle_group === currentMuscleFilter;
                const matchesRecommended = !onlyRecommended || recommendedIds.includes(Number(ex.id));
                const matchesSearch = !query
                    || (ex.name || '').toLowerCase().includes(query)
                    || (ex.equipment || '').toLowerCase().includes(query)
                    || (ex.muscle_group || '').toLowerCase().includes(query)
                    || (ex.target_zone || '').toLowerCase().includes(query)
                    || (ex.objective || '').toLowerCase().includes(query);
                return matchesMuscle && matchesRecommended && matchesSearch;
            });

            if (filtered.length === 0) {
                catalog.innerHTML = `
                    <div class="col-span-full py-16 text-center text-brand-text-secondary">
                        <i class="fa-solid fa-magnifying-glass text-3xl mb-3 opacity-50"></i>
                        <p class="text-base font-bold text-brand-text-primary">No encontramos ejercicios con ese filtro</p>
                        <p class="text-sm mt-1.5">Prueba cambiando el grupo muscular o limpiando la búsqueda.</p>
                    </div>
                `;
                return;
            }

            catalog.innerHTML = filtered.map(exerciseCardHTML).join('');
        }

        /* ============================================================
           RUTINA
           ============================================================ */
        function toggleExercise(ex) {
            const index = routineExercises.findIndex(e => Number(e.id) === Number(ex.id));
            let added = false;

            if (index !== -1) {
                routineExercises.splice(index, 1);
            } else {
                if (routineExercises.length >= MAX_EXERCISES) {
                    showFeedback(`Has alcanzado el límite de ${MAX_EXERCISES} ejercicios por rutina.`);
                    return;
                }
                routineExercises.push({
                    id: ex.id,
                    name: ex.name,
                    difficulty: ex.difficulty || 'beginner',
                    sets: 3,
                    reps: 10,
                    rest: 60
                });
                added = true;
            }

            renderRoutineBlocks(added);   // si se agregó uno, baja hasta el final de la lista
            renderCatalog();
        }

        function removeExercise(index) {
            routineExercises.splice(index, 1);
            renderRoutineBlocks();
            renderCatalog();
        }

        function moveExercise(index, direction) {
            const targetIndex = index + direction;
            if (targetIndex < 0 || targetIndex >= routineExercises.length) return;
            const [item] = routineExercises.splice(index, 1);
            routineExercises.splice(targetIndex, 0, item);
            renderRoutineBlocks();
        }

        // Actualiza solo los números: no re-renderiza la lista, así no se pierde el foco al tabular
        function setExerciseValue(index, field, rawValue) {
            const [min, max] = LIMITS[field];
            let value = parseInt(rawValue, 10);
            if (Number.isNaN(value)) value = routineExercises[index][field];
            value = Math.min(max, Math.max(min, value));

            routineExercises[index][field] = value;

            const input = document.getElementById(`ex-${index}-${field}`);
            if (input) input.value = value;

            updateSummaryAndDifficulty();
        }

        function updateExercise(index, field, input) {
            setExerciseValue(index, field, input.value);
        }

        // Flechitas ▲▼: suman/restan el paso del campo (descanso = 10 s).
        // Si el valor no es múltiplo del paso (ej. 65 s), primero se alinea (70 s o 60 s).
        function stepExercise(index, field, direction) {
            const step = STEPS[field];
            const current = routineExercises[index][field];

            let next;
            if (current % step === 0) {
                next = current + direction * step;
            } else {
                next = direction > 0
                    ? Math.ceil(current / step) * step
                    : Math.floor(current / step) * step;
            }

            setExerciseValue(index, field, next);
        }

        function stepperHTML(index, field, label, value) {
            const [min, max] = LIMITS[field];
            const id = `ex-${index}-${field}`;
            const arrowClass = 'w-6 h-[18px] rounded-md text-slate-400 hover:text-brand-primary hover:bg-brand-surface-muted flex items-center justify-center text-[10px] transition-colors';

            return `
                <div class="flex flex-col items-center">
                    <label for="${id}" class="text-[11px] font-extrabold text-brand-text-secondary uppercase mb-1.5">${label}</label>
                    <div class="relative w-full">
                        <input id="${id}" type="number" inputmode="numeric" min="${min}" max="${max}" step="${STEPS[field]}" value="${value}"
                               onfocus="this.select()"
                               onchange="updateExercise(${index}, '${field}', this)"
                               class="fq-num w-full text-center bg-white border border-brand-border rounded-full py-2 pl-3 pr-10 text-base font-extrabold focus:border-brand-primary outline-none">
                        <div class="absolute right-2.5 top-1/2 -translate-y-1/2 flex flex-col">
                            <button type="button" tabindex="-1" onclick="stepExercise(${index}, '${field}', 1)" class="${arrowClass}" aria-label="Aumentar ${label}">
                                <i class="fa-solid fa-chevron-up"></i>
                            </button>
                            <button type="button" tabindex="-1" onclick="stepExercise(${index}, '${field}', -1)" class="${arrowClass}" aria-label="Disminuir ${label}">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }

        function renderRoutineBlocks(scrollToEnd = false) {
            const container = document.getElementById('routineBlocks');
            const emptyState = document.getElementById('emptyState');

            Array.from(container.children).forEach(child => {
                if (child.id !== 'emptyState') child.remove();
            });

            if (routineExercises.length === 0) {
                emptyState.classList.remove('hidden');
                updateSummaryAndDifficulty();
                return;
            }

            emptyState.classList.add('hidden');

            routineExercises.forEach((ex, index) => {
                const block = document.createElement('div');
                block.className = 'bg-white rounded-2xl border border-brand-border p-4 mb-3 shadow-sm flex flex-col gap-3 hover:border-brand-primary/50 transition-colors';

                const name = escapeHtml(ex.name);

                block.innerHTML = `
                    <div class="flex items-center justify-between gap-2">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <span class="w-7 h-7 rounded-full bg-brand-primary text-white text-xs font-black flex items-center justify-center shrink-0">
                                ${index + 1}
                            </span>
                            <p class="font-bold text-sm sm:text-base text-brand-text-primary truncate" title="${name}">${name}</p>
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button type="button" onclick="moveExercise(${index}, -1)" ${index === 0 ? 'disabled' : ''} class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 disabled:opacity-30 hover:bg-slate-200 flex items-center justify-center text-xs" title="Subir">
                                <i class="fa-solid fa-chevron-up"></i>
                            </button>
                            <button type="button" onclick="moveExercise(${index}, 1)" ${index === routineExercises.length - 1 ? 'disabled' : ''} class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 disabled:opacity-30 hover:bg-slate-200 flex items-center justify-center text-xs" title="Bajar">
                                <i class="fa-solid fa-chevron-down"></i>
                            </button>
                            <button type="button" onclick="removeExercise(${index})" class="w-8 h-8 rounded-full bg-red-50 text-brand-danger flex items-center justify-center hover:bg-red-100 transition-colors ml-0.5" title="Quitar">
                                <i class="fa-solid fa-trash text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="grid grid-cols-3 gap-3 bg-brand-surface-muted/60 p-3 rounded-xl border border-brand-border/60">
                        ${stepperHTML(index, 'sets', 'Series', ex.sets)}
                        ${stepperHTML(index, 'reps', 'Reps', ex.reps)}
                        ${stepperHTML(index, 'rest', 'Desc. (s)', ex.rest)}
                    </div>
                `;
                container.appendChild(block);
            });

            if (scrollToEnd) {
                container.scrollTo({ top: container.scrollHeight, behavior: 'smooth' });
            }

            updateSummaryAndDifficulty();
        }

        /* ============================================================
           RESUMEN Y DIFICULTAD
           ============================================================ */
        // Convierte el texto de dificultad del ejercicio (BD) a 1, 2 o 3
        function difficultyRank(value) {
            const v = String(value || '').toLowerCase();
            if (/(adv|hard|dif[ií]cil|avanz)/.test(v)) return 3;
            if (/(inter|medi)/.test(v)) return 2;
            return 1;   // beginner / easy / principiante / fácil
        }

        /*
         * Puntaje 0-100 = ejercicios (40) + volumen (35) + descansos (25)
         *  - Ejercicios: nivel promedio de los ejercicios, ponderado por series.
         *  - Volumen:    repeticiones totales (series × reps) respecto a VOLUME_MAX_REPS.
         *  - Descansos:  descanso promedio entre series; más corto = más exigente.
         */
        function calculateDifficulty() {
            const totalSets = routineExercises.reduce((acc, ex) => acc + ex.sets, 0);
            const totalReps = routineExercises.reduce((acc, ex) => acc + ex.sets * ex.reps, 0);
            const avgRank = routineExercises.reduce((acc, ex) => acc + ex.sets * difficultyRank(ex.difficulty), 0) / totalSets;
            const avgRest = routineExercises.reduce((acc, ex) => acc + ex.sets * ex.rest, 0) / totalSets;

            const exercisePts = ((avgRank - 1) / 2) * SCORE_WEIGHTS.exercises;
            const volumePts = Math.min(totalReps / VOLUME_MAX_REPS, 1) * SCORE_WEIGHTS.volume;
            const restRatio = Math.min(1, Math.max(0, (REST_EASY - avgRest) / (REST_EASY - REST_HARD)));
            const restPts = restRatio * SCORE_WEIGHTS.rest;

            const score = Math.round(exercisePts + volumePts + restPts);
            const parts = {
                exercises: Math.round(exercisePts),
                volume: Math.round(volumePts),
                rest: Math.round(restPts)
            };

            if (score >= LEVEL_THRESHOLDS.advanced) {
                return { score, parts, level: 'advanced', label: 'Avanzado', bar: 'bg-red-500', text: 'text-red-600' };
            }
            if (score >= LEVEL_THRESHOLDS.intermediate) {
                return { score, parts, level: 'intermediate', label: 'Intermedio', bar: 'bg-amber-500', text: 'text-amber-600' };
            }
            return { score, parts, level: 'beginner', label: 'Principiante', bar: 'bg-emerald-500', text: 'text-emerald-600' };
        }

        function updateSummaryAndDifficulty() {
            const bar = document.getElementById('difficultyBar');
            const text = document.getElementById('difficultyText');
            const warning = document.getElementById('warningMsg');
            const detail = document.getElementById('difficultyDetail');
            const btnSave = document.getElementById('btnSave');

            const count = routineExercises.length;
            const totalSets = routineExercises.reduce((acc, ex) => acc + ex.sets, 0);
            const totalSeconds = routineExercises.reduce((acc, ex) => acc + (ex.sets * (ex.reps * 3 + ex.rest)), 0);
            const estMinutes = Math.max(0, Math.round(totalSeconds / 60));

            document.getElementById('footerSummaryCount').textContent = `${count} ${count === 1 ? 'ejercicio' : 'ejercicios'}`;
            document.getElementById('footerSummaryTime').textContent = count ? ` · ${estMinutes} min` : '';
            document.getElementById('tabRoutineCount').textContent = count;
            document.getElementById('statExercises').textContent = `${count} / ${MAX_EXERCISES}`;
            document.getElementById('statSets').textContent = totalSets;
            document.getElementById('statTime').textContent = `${estMinutes} min`;

            if (count === 0) {
                currentLevel = 'beginner';
                text.textContent = 'Vacía';
                text.className = 'text-sm font-black text-slate-400 min-w-[96px] text-right';
                bar.style.width = '0%';
                bar.className = 'h-full bg-slate-300 rounded-full transition-all duration-300 ease-out';
                detail.textContent = '';
                warning.classList.add('hidden');
                btnSave.disabled = true;
                btnSave.className = 'px-7 py-3 bg-slate-200 text-slate-400 text-base font-bold rounded-full cursor-not-allowed transition-all flex items-center gap-2';
                return;
            }

            const result = calculateDifficulty();
            currentLevel = result.level;

            text.textContent = result.label;
            text.className = `text-sm font-black ${result.text} min-w-[96px] text-right`;
            bar.style.width = Math.max(4, Math.min(result.score, 100)) + '%';
            detail.textContent = `${result.score}/100 pts: ejercicios ${result.parts.exercises}, volumen ${result.parts.volume}, descansos ${result.parts.rest}`;
            bar.className = `h-full ${result.bar} rounded-full transition-all duration-300 ease-out`;

            const levelPower = { beginner: 1, intermediate: 2, advanced: 3 };
            warning.classList.toggle('hidden', (levelPower[result.level] || 1) <= (levelPower[userLevel] || 1));

            btnSave.disabled = false;
            btnSave.className = 'px-7 py-3 bg-brand-primary force-white-text text-base font-bold rounded-full shadow-sm hover:opacity-95 transition-all flex items-center gap-2 cursor-pointer';
        }

        /* ============================================================
           MODAL
           ============================================================ */
        function openInfoModal() {
            const modal = document.getElementById('infoModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeInfoModal() {
            const modal = document.getElementById('infoModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        /* ============================================================
           GUARDAR
           ============================================================ */
        function saveRoutine() {
            if (isSaving) return;

            const name = document.getElementById('routineName').value.trim();
            if (!name) {
                setMobilePanel('routine');
                showFeedback('Escribe un nombre para tu rutina antes de guardar.');
                document.getElementById('routineName').focus();
                return;
            }
            if (routineExercises.length === 0) return;

            isSaving = true;

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('entrenamientos.crear.guardar') }}";

            const fields = {
                _token: "{{ csrf_token() }}",
                name,
                environment: document.getElementById('routineEnv').value || 'home',
                difficulty: currentLevel,
                description: 'Rutina personalizada'
            };

            routineExercises.forEach((ex, index) => {
                fields[`exercises[${index}][id]`] = ex.id;
                fields[`exercises[${index}][position]`] = index + 1;
                fields[`exercises[${index}][sets]`] = ex.sets;
                fields[`exercises[${index}][reps]`] = ex.reps;
                fields[`exercises[${index}][rest]`] = ex.rest;
            });

            Object.entries(fields).forEach(([key, value]) => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = key;
                input.value = value;
                form.appendChild(input);
            });

            document.body.appendChild(form);
            form.submit();
        }

        /* ============================================================
           INICIO
           ============================================================ */
        document.addEventListener('DOMContentLoaded', () => {
            renderMuscleFilters();
            renderCatalog();
            renderRoutineBlocks();
            setMobilePanel('catalog');

            // Delegación de eventos: un solo listener para todo el catálogo / filtros
            document.getElementById('exerciseCatalog').addEventListener('click', (e) => {
                const card = e.target.closest('[data-id]');
                if (!card) return;
                const ex = exerciseById.get(Number(card.dataset.id));
                if (ex) toggleExercise(ex);
            });

            document.getElementById('muscleFilters').addEventListener('click', (e) => {
                const pill = e.target.closest('[data-muscle]');
                if (pill) setFilter(pill.dataset.muscle);
            });

            document.getElementById('searchExercise').addEventListener('input', renderCatalog);

            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape') closeInfoModal();
            });
        });
    </script>
@endpush