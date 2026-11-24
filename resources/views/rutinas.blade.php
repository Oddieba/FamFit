@extends('layouts.quest')

@section('title', 'Rutinas | FamFit Quest')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { primary: '#2864F0', surface: '#FFFFFF', 'surface-muted': '#EEF2F8', 'text-primary': '#172033', 'text-secondary': '#64748B', border: '#DCE4EF', success: '#A7E66D', habit: '#19C6B0', warning: '#F2B85B', danger: '#E45865' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>

    <main class="w-full h-full font-sans text-brand-text-primary p-4 md:p-8 overflow-y-auto">
        
        <!-- Header -->
        <div class="max-w-6xl mx-auto mb-8">
            <a href="{{ route('entrenamientos') }}" class="inline-flex items-center text-sm font-bold text-brand-text-secondary hover:text-brand-primary mb-4 transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver a Entornos
            </a>
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-extrabold capitalize">Rutinas: {{ $entorno }}</h1>
                    <p class="text-brand-text-secondary text-sm mt-1">Explora las rutinas disponibles o crea la tuya.</p>
                </div>
                <a href="{{ route('entrenamientos.crear') }}" class="px-5 py-2.5 bg-brand-primary text-white font-bold rounded-xl shadow-md hover:bg-brand-primary/90 transition-all flex items-center justify-center">
                    <i class="fa-solid fa-plus mr-2"></i> Crear mi rutina
                </a>
            </div>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-4 gap-8">
            
            <!-- COLUMNA IZQUIERDA: Recomendación -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gradient-to-br from-brand-primary to-blue-700 rounded-2xl p-6 text-white shadow-xl relative overflow-hidden sticky top-8">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fa-solid fa-wand-magic-sparkles text-6xl"></i>
                    </div>
                    <h3 class="font-extrabold text-xl mb-2">Tu perfil</h3>
                    <p class="text-xs text-blue-100 mb-4">Basado en tu Test Inicial.</p>
                    
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20 space-y-3">
                        <p class="text-sm font-bold flex items-center"><i class="fa-solid fa-bullseye w-6 text-brand-success"></i> {{ ucfirst(str_replace('_', ' ', $perfil->goals ?? 'General')) }}</p>
                        <p class="text-sm font-bold flex items-center"><i class="fa-solid fa-chart-line w-6 text-brand-success"></i> {{ ucfirst($perfil->experience ?? 'Principiante') }}</p>
                    </div>
                    
                    <div class="mt-6 border-t border-white/20 pt-4">
                        <p class="text-sm text-blue-100 leading-relaxed">
                            Tenemos <strong class="text-white">{{ $rutinasRecomendadas->count() }} rutinas</strong> que FamFit ha acomodado especialmente para tu objetivo.
                        </p>
                    </div>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Tabla -->
            <div class="lg:col-span-3 space-y-6">
                
                <!-- Filtros (Botones) - Todos inician con el mismo diseño base -->
                <div class="flex space-x-2 border-b border-brand-border pb-4 overflow-x-auto" id="filterButtons">
                    <!-- Botón Todas (Inicia Activo) -->
                    <button data-filter="all" class="filter-btn px-4 py-2 bg-brand-primary text-white shadow-sm font-bold text-sm rounded-lg whitespace-nowrap transition-all duration-200">
                        Todas ({{ $todasLasRutinas->count() }})
                    </button>
                    <!-- Botones Inactivos -->
                    <button data-filter="recommended" class="filter-btn px-4 py-2 text-brand-text-secondary hover:bg-brand-surface-muted hover:text-brand-text-primary font-bold text-sm rounded-lg whitespace-nowrap transition-all duration-200">
                        Recomendadas
                    </button>
                    <button data-filter="community" class="filter-btn px-4 py-2 text-brand-text-secondary hover:bg-brand-surface-muted hover:text-brand-text-primary font-bold text-sm rounded-lg whitespace-nowrap transition-all duration-200">
                        Comunidad (Famosas)
                    </button>
                    <button data-filter="mine" class="filter-btn px-4 py-2 text-brand-text-secondary hover:bg-brand-surface-muted hover:text-brand-text-primary font-bold text-sm rounded-lg whitespace-nowrap transition-all duration-200">
                        Mis rutinas
                    </button>
                </div>

                <!-- Tabla -->
                <div class="bg-brand-surface rounded-2xl border border-brand-border overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-brand-surface-muted text-brand-text-secondary text-xs uppercase tracking-wider">
                                <th class="p-4 font-bold">Rutina</th>
                                <th class="p-4 font-bold">Nivel</th>
                                <th class="p-4 font-bold">Duración</th>
                                <th class="p-4 font-bold text-center">Acción</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border" id="routineTableBody">
                            @forelse($todasLasRutinas as $rutina)
                                @php
                                    $isRecommended = $rutinasRecomendadas->contains('id', $rutina->id);
                                    $type = $rutina->source === 'user' ? 'mine' : 'community';
                                @endphp
                                <tr class="routine-row hover:bg-brand-surface-muted/50 transition-colors group" 
                                    data-type="{{ $type }}" 
                                    data-recommended="{{ $isRecommended ? 'true' : 'false' }}">
                                    <td class="p-4">
                                        <span class="font-bold text-base block text-brand-text-primary group-hover:text-brand-primary transition-colors">{{ $rutina->name }}</span>
                                        <span class="text-xs text-brand-text-secondary block mt-1 line-clamp-1">{{ $rutina->description }}</span>
                                    </td>
                                    <td class="p-4">
                                        @php
                                            $badgeColor = match($rutina->difficulty) {
                                                'beginner' => 'bg-green-100 text-green-700',
                                                'intermediate' => 'bg-orange-100 text-orange-700',
                                                'advanced' => 'bg-red-100 text-red-700',
                                                default => 'bg-gray-100 text-gray-700'
                                            };
                                            $badgeText = match($rutina->difficulty) {
                                                'beginner' => 'Principiante',
                                                'intermediate' => 'Intermedio',
                                                'advanced' => 'Avanzado',
                                                default => 'General'
                                            };
                                        @endphp
                                        <span class="px-2.5 py-1 {{ $badgeColor }} text-xs font-bold rounded-md whitespace-nowrap">{{ $badgeText }}</span>
                                    </td>
                                    <td class="p-4 text-sm font-medium">
                                        <i class="fa-regular fa-clock text-brand-text-secondary mr-1"></i> {{ $rutina->estimated_minutes }} min
                                    </td>
                                    <td class="p-4 text-center">
                                        <button onclick='openRoutineModal(@json($rutina))' class="px-4 py-2 bg-brand-surface border border-brand-border text-brand-primary font-bold text-sm rounded-lg hover:bg-brand-primary hover:text-white transition-all shadow-sm">
                                            Ver Detalles
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-brand-text-secondary">
                                        <div class="flex flex-col items-center justify-center">
                                            <i class="fa-solid fa-box-open text-5xl mb-4 opacity-30"></i>
                                            <p class="text-lg font-bold text-brand-text-primary">No hay rutinas aún</p>
                                            <p class="text-sm mt-1">Vuelve a ejecutar los seeders o crea tu propia rutina.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                            
                            <tr id="emptyFilterMessage" class="hidden">
                                <td colspan="4" class="p-8 text-center text-brand-text-secondary">
                                    <p class="text-sm">No se encontraron rutinas en esta categoría.</p>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- MODAL -->
    <div id="routineDetailModal" class="fixed inset-0 bg-brand-text-primary/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-brand-surface rounded-2xl border border-brand-border shadow-2xl max-w-2xl w-full overflow-hidden transform transition-all flex flex-col max-h-[90vh]">
            <div class="bg-brand-surface-muted px-6 py-4 border-b border-brand-border flex items-center justify-between shrink-0">
                <div>
                    <h3 id="modalRoutineName" class="text-xl font-extrabold text-brand-text-primary">Nombre de la Rutina</h3>
                    <p id="modalRoutineDesc" class="text-sm text-brand-text-secondary mt-1">Descripción de la rutina.</p>
                </div>
                <button onclick="closeRoutineModal()" class="w-10 h-10 rounded-full bg-brand-surface border border-brand-border flex items-center justify-center text-brand-text-secondary hover:text-brand-danger hover:border-brand-danger transition-all shrink-0">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            <div class="p-6 overflow-y-auto custom-scroll bg-brand-surface">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="text-xs uppercase tracking-wider text-brand-text-secondary border-b border-brand-border">
                            <th class="pb-3 font-bold">Ejercicio</th>
                            <th class="pb-3 font-bold text-center">Series</th>
                            <th class="pb-3 font-bold text-center">Reps / Tiempo</th>
                            <th class="pb-3 font-bold text-center">Descanso</th>
                        </tr>
                    </thead>
                    <tbody id="modalExerciseList" class="divide-y divide-brand-border/50"></tbody>
                </table>
            </div>
            <div class="bg-brand-surface-muted px-6 py-4 border-t border-brand-border flex items-center justify-between shrink-0">
                <span class="text-sm font-bold text-brand-text-secondary">Entorno: <span id="modalRoutineEnv" class="uppercase text-brand-primary"></span></span>
                <button class="px-6 py-2.5 bg-brand-primary text-white font-bold text-sm rounded-xl shadow-md hover:bg-brand-primary/90 transition-all">
                    Comenzar Entrenamiento <i class="fa-solid fa-play ml-2"></i>
                </button>
            </div>
        </div>
    </div>

    <style>
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: #EEF2F8; border-radius: 8px; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #64748B; border-radius: 8px; }
        .line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
    </style>

    <script>
        // --- LÓGICA DE FILTRADO REFORZADA ---
        const filterButtons = document.querySelectorAll('.filter-btn');
        const routineRows = document.querySelectorAll('.routine-row');
        const emptyMessage = document.getElementById('emptyFilterMessage');

        filterButtons.forEach(button => {
            button.addEventListener('click', () => {
                
                // 1. Resetear TODOS los botones a su estado inactivo (Gris claro, texto oscuro)
                filterButtons.forEach(btn => {
                    btn.classList.remove('bg-brand-primary', 'text-white', 'shadow-sm');
                    btn.classList.add('text-brand-text-secondary', 'hover:bg-brand-surface-muted', 'hover:text-brand-text-primary');
                });

                // 2. Poner el botón clickeado en estado ACTIVO (Azul sólido, texto blanco)
                button.classList.remove('text-brand-text-secondary', 'hover:bg-brand-surface-muted', 'hover:text-brand-text-primary');
                button.classList.add('bg-brand-primary', 'text-white', 'shadow-sm');

                // 3. Lógica para mostrar/ocultar las filas de la tabla
                const filterType = button.dataset.filter;
                let visibleCount = 0;

                routineRows.forEach(row => {
                    let showRow = false;

                    if (filterType === 'all') showRow = true;
                    else if (filterType === 'recommended') showRow = row.dataset.recommended === 'true';
                    else if (filterType === 'community') showRow = row.dataset.type === 'community';
                    else if (filterType === 'mine') showRow = row.dataset.type === 'mine';

                    if (showRow) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                });

                // 4. Mostrar mensaje si el filtro está vacío
                if (visibleCount === 0 && routineRows.length > 0) {
                    emptyMessage.classList.remove('hidden');
                } else {
                    emptyMessage.classList.add('hidden');
                }
            });
        });

        // --- LÓGICA DEL MODAL ---
        function openRoutineModal(routine) {
            document.getElementById('modalRoutineName').innerText = routine.name;
            document.getElementById('modalRoutineDesc').innerText = routine.description;
            
            const envMap = { 'home': 'Casa', 'gym': 'Gimnasio', 'outdoor': 'Aire Libre' };
            document.getElementById('modalRoutineEnv').innerText = envMap[routine.environment] || routine.environment;

            const tbody = document.getElementById('modalExerciseList');
            tbody.innerHTML = ''; 

            if (routine.exercises && routine.exercises.length > 0) {
                routine.exercises.forEach((ex, index) => {
                    let repsOrTime = ex.pivot.reps ? `${ex.pivot.reps} reps` : '';
                    if (ex.pivot.duration_seconds) {
                        repsOrTime = `${ex.pivot.duration_seconds} seg`;
                    }
                    let rest = ex.pivot.rest_seconds ? `${ex.pivot.rest_seconds} seg` : '-';

                    const row = `
                        <tr class="hover:bg-brand-surface-muted/30 transition-colors">
                            <td class="py-3">
                                <span class="font-bold text-sm text-brand-text-primary">${index + 1}. ${ex.name}</span>
                                <span class="block text-xs text-brand-text-secondary mt-0.5">${ex.muscle_group || 'General'}</span>
                            </td>
                            <td class="py-3 text-center text-sm font-bold text-brand-primary">${ex.pivot.sets || '-'}</td>
                            <td class="py-3 text-center text-sm font-medium">${repsOrTime || '-'}</td>
                            <td class="py-3 text-center text-sm text-brand-text-secondary"><i class="fa-regular fa-clock mr-1"></i> ${rest}</td>
                        </tr>
                    `;
                    tbody.innerHTML += row;
                });
            } else {
                tbody.innerHTML = `<tr><td colspan="4" class="py-6 text-center text-brand-text-secondary">Esta rutina no tiene ejercicios asignados.</td></tr>`;
            }

            const modal = document.getElementById('routineDetailModal');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function closeRoutineModal() {
            const modal = document.getElementById('routineDetailModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    </script>
@endsection