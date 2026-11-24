@extends('layouts.quest')

@section('title', 'Crear Rutina | FamFit Quest')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { primary: '#2864F0', surface: '#FFFFFF', 'surface-muted': '#EEF2F8', 'text-primary': '#172033', 'text-secondary': '#64748B', border: '#DCE4EF', success: '#A7E66D', habit: '#19C6B0', warning: '#F2B85B', danger: '#E45865' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>

    <script>
        const allExercises = @json($todosLosEjercicios);
        const recommendedIds = @json($recomendados);
        const userLevel = "{{ $perfil->experience ?? 'beginner' }}";
    </script>

    <!-- Bloqueo estricto de scroll en la ventana -->
    <style>
        html, body {
            overflow: hidden !important;
            height: 100vh !important;
            margin: 0 !important;
            padding: 0 !important;
            background-color: #F5F7FB;
        }
        
        /* Scroll delgado para los paneles internos */
        .custom-scroll::-webkit-scrollbar { width: 6px; }
        .custom-scroll::-webkit-scrollbar-track { background: transparent; }
        .custom-scroll::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 6px; }
        .custom-scroll::-webkit-scrollbar-thumb:hover { background: #94A3B8; }

        /* Animación para las tarjetas que se añaden */
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideIn 0.3s ease-out forwards; }
    </style>

    <!-- Contenedor Principal que centra la Gran Tarjeta -->
    <div class="w-full h-screen flex items-center justify-center p-4 md:p-6 font-sans text-brand-text-primary">
        
        <!-- LA GRAN TARJETA -->
        <div class="w-full max-w-[1400px] h-full max-h-[850px] bg-white rounded-3xl shadow-xl border border-brand-border flex flex-col overflow-hidden">
            
            <!-- Header de la Gran Tarjeta -->
            <div class="flex-none px-6 py-4 border-b border-brand-border flex items-center justify-between bg-white z-10">
                <div class="flex items-center space-x-4">
                    <a href="javascript:history.back()" class="w-10 h-10 rounded-full bg-brand-surface-muted flex items-center justify-center hover:bg-gray-200 transition-colors">
                        <i class="fa-solid fa-arrow-left text-brand-text-secondary"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-extrabold leading-tight">Creador de Rutinas</h1>
                        <p class="text-xs text-brand-text-secondary font-medium mt-0.5">Diseña tu sesión perfecta</p>
                    </div>
                </div>
                
                <button type="button" onclick="saveRoutine()" id="btnSave" class="px-6 py-2.5 bg-gray-300 text-gray-500 text-sm font-bold rounded-xl cursor-not-allowed transition-all flex items-center shadow-sm">
                    <i class="fa-solid fa-save mr-2"></i> Guardar Rutina
                </button>
            </div>

            <!-- Contenido de la Gran Tarjeta (Dos Columnas) -->
            <div class="flex-1 flex flex-col lg:flex-row min-h-0 overflow-hidden">
                
                <!-- COLUMNA IZQUIERDA: Catálogo de Ejercicios (Más ancha - 65%) -->
                <div class="w-full lg:w-[65%] flex flex-col min-h-0 bg-brand-surface-muted/30 border-r border-brand-border">
                    
                    <!-- Barra de Filtros y Búsqueda -->
                    <div class="p-4 flex-none border-b border-brand-border/50">
                        <div class="relative mb-3">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-2.5 text-sm text-brand-text-secondary"></i>
                            <input type="text" id="searchExercise" onkeyup="filterExercises()" placeholder="Buscar ejercicio por nombre..." class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-brand-border focus:border-brand-primary outline-none text-sm transition-all shadow-sm">
                        </div>
                        
                        <div class="flex overflow-x-auto space-x-2 pb-1 custom-scroll" id="muscleFilters">
                            <button onclick="setFilter('all')" class="filter-pill active px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-primary bg-brand-primary text-white transition-all shadow-sm">Todos</button>
                            <button onclick="setFilter('Pecho')" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-gray-50 transition-all">Pecho</button>
                            <button onclick="setFilter('Piernas')" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-gray-50 transition-all">Piernas</button>
                            <button onclick="setFilter('Espalda')" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-gray-50 transition-all">Espalda</button>
                            <button onclick="setFilter('Core')" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-gray-50 transition-all">Core</button>
                            <button onclick="setFilter('Cardio')" class="filter-pill px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-gray-50 transition-all">Cardio</button>
                        </div>
                    </div>
                    
                    <!-- Grid de Ejercicios (3 columnas) -->
                    <div class="flex-1 overflow-y-auto p-4 custom-scroll grid grid-cols-2 md:grid-cols-3 gap-3 auto-rows-max" id="exerciseCatalog">
                        <!-- JS llenará esto -->
                    </div>
                </div>

                <!-- COLUMNA DERECHA: Constructor de Rutina (35%) -->
                <div class="w-full lg:w-[35%] flex flex-col min-h-0 bg-white">
                    
                    <!-- Configuración y Dificultad -->
                    <div class="p-5 border-b border-brand-border flex-none bg-white z-10 shadow-sm">
                        <div class="mb-4">
                            <label class="text-[10px] font-extrabold text-brand-text-secondary uppercase tracking-widest mb-1 block">Nombre de tu rutina</label>
                            <input type="text" id="routineName" placeholder="Ej: Mi Rutina Full Body..." class="w-full px-4 py-3 rounded-xl border border-brand-border focus:border-brand-primary outline-none text-sm font-bold bg-brand-surface-muted transition-all">
                        </div>
                        
                        <div>
                            <div class="flex justify-between items-end mb-1.5">
                                <label class="text-[10px] font-extrabold text-brand-text-secondary uppercase tracking-widest">Dificultad</label>
                                <button onclick="openInfoModal()" class="text-[11px] text-brand-primary hover:underline font-bold flex items-center">
                                    <i class="fa-solid fa-circle-info mr-1"></i> ¿Cómo se calcula?
                                </button>
                            </div>
                            <div class="flex items-center space-x-3">
                                <div class="flex-1 h-2.5 bg-gray-200 rounded-full overflow-hidden">
                                    <div id="difficultyBar" class="h-full bg-gray-300 transition-all duration-500 ease-out" style="width: 0%"></div>
                                </div>
                                <span id="difficultyText" class="text-xs font-black text-gray-400 w-20 text-right">Vacía</span>
                            </div>
                            <div id="warningMsg" class="hidden mt-3 bg-red-50 text-red-600 text-[11px] font-bold p-2.5 rounded-lg text-center border border-red-100 animate-slide-in">
                                <i class="fa-solid fa-triangle-exclamation mr-1"></i> Supera tu nivel actual en el test.
                            </div>
                        </div>
                    </div>

                    <!-- Lista de Ejercicios Seleccionados -->
                    <div class="flex-1 overflow-y-auto p-4 custom-scroll relative bg-brand-surface-muted/20" id="routineBlocks">
                        <!-- Estado vacío -->
                        <div id="emptyState" class="absolute inset-0 flex flex-col items-center justify-center text-brand-text-secondary pointer-events-none opacity-60">
                            <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mb-3 shadow-sm border border-brand-border">
                                <i class="fa-solid fa-puzzle-piece text-3xl text-brand-primary"></i>
                            </div>
                            <p class="text-sm font-bold text-brand-text-primary">Aún no hay ejercicios</p>
                            <p class="text-xs mt-1 text-center px-8">Selecciona del catálogo de la izquierda para armar tu rompecabezas</p>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <!-- MODAL EXPLICATIVO -->
    <div id="infoModal" class="fixed inset-0 bg-brand-text-primary/60 backdrop-blur-sm z-50 hidden items-center justify-center p-4">
        <div class="bg-brand-surface rounded-2xl border border-brand-border shadow-2xl max-w-sm w-full overflow-hidden transform transition-all">
            <div class="bg-brand-surface-muted px-5 py-4 border-b border-brand-border flex items-center justify-between">
                <h3 class="text-sm font-extrabold text-brand-text-primary"><i class="fa-solid fa-calculator text-brand-primary mr-2"></i> Fórmula de Dificultad</h3>
                <button onclick="closeInfoModal()" class="text-brand-text-secondary hover:text-brand-danger w-8 h-8 flex items-center justify-center rounded-full hover:bg-white transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="p-5 text-xs text-brand-text-secondary space-y-4">
                <p>Evaluamos el <strong>Volumen Total</strong> de tu rutina multiplicando series y repeticiones, ponderado por la dificultad del ejercicio.</p>
                <div class="bg-brand-surface-muted p-3 rounded-xl border border-brand-border font-mono text-[11px] text-center font-bold text-brand-text-primary">
                    (Series × Reps) × Factor
                </div>
                <ul class="list-disc pl-4 space-y-1.5 text-[11px]">
                    <li>Fácil: <strong class="text-brand-text-primary">Factor x1</strong></li>
                    <li>Medio: <strong class="text-brand-text-primary">Factor x1.5</strong></li>
                    <li>Difícil: <strong class="text-brand-text-primary">Factor x2.5</strong></li>
                </ul>
                <div class="flex flex-col space-y-2 text-[11px] font-bold mt-3 bg-white p-3 rounded-lg border border-brand-border">
                    <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-green-500 mr-2"></span> Principiante (&lt; 100 pts)</span>
                    <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-orange-500 mr-2"></span> Intermedio (100-249 pts)</span>
                    <span class="flex items-center"><span class="w-2.5 h-2.5 rounded-full bg-red-500 mr-2"></span> Avanzado (250+ pts)</span>
                </div>
            </div>
        </div>
    </div>

    <script>
        const MAX_EXERCISES = 12;
        const MAX_SETS = 8;
        const MAX_REPS = 30;

        let routineExercises = [];
        let currentMuscleFilter = 'all';

        const muscleColors = {
            'Pecho': 'bg-red-50 text-red-700 border-red-200',
            'Piernas': 'bg-purple-50 text-purple-700 border-purple-200',
            'Espalda': 'bg-blue-50 text-blue-700 border-blue-200',
            'Core': 'bg-orange-50 text-orange-700 border-orange-200',
            'Hombros': 'bg-teal-50 text-teal-700 border-teal-200',
            'Bíceps': 'bg-pink-50 text-pink-700 border-pink-200',
            'Tríceps': 'bg-indigo-50 text-indigo-700 border-indigo-200',
            'Cardio': 'bg-green-50 text-green-700 border-green-200',
            'Default': 'bg-gray-50 text-gray-700 border-gray-200'
        };

        function setFilter(muscle) {
            currentMuscleFilter = muscle;
            document.querySelectorAll('.filter-pill').forEach(btn => {
                if(btn.innerText === (muscle === 'all' ? 'Todos' : muscle)) {
                    btn.className = "filter-pill active px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-primary bg-brand-primary text-white transition-all shadow-sm";
                } else {
                    btn.className = "filter-pill px-3 py-1.5 rounded-lg text-xs font-bold whitespace-nowrap border border-brand-border bg-white text-brand-text-secondary hover:bg-gray-50 transition-all";
                }
            });
            filterExercises();
        }

        function renderCatalog() {
            const catalog = document.getElementById('exerciseCatalog');
            catalog.innerHTML = '';
            
            allExercises.forEach(ex => {
                const isAdded = routineExercises.some(e => e.id === ex.id);
                const muscleClass = muscleColors[ex.muscle_group] || muscleColors['Default'];
                
                const card = document.createElement('div');
                
                if (isAdded) {
                    card.className = `p-3 rounded-xl border-2 border-green-500 bg-green-50 opacity-60 cursor-not-allowed flex flex-col justify-between transition-all h-[110px]`;
                } else {
                    card.className = `p-3 rounded-xl border border-brand-border bg-white hover:border-brand-primary hover:shadow-md hover:-translate-y-0.5 cursor-pointer transition-all flex flex-col justify-between group h-[110px]`;
                    card.onclick = () => addExercise(ex);
                }
                
                card.innerHTML = `
                    <div>
                        <div class="flex justify-between items-start mb-1.5">
                            <span class="text-[9px] font-extrabold px-1.5 py-0.5 rounded border ${muscleClass} uppercase tracking-wider">${ex.muscle_group || 'General'}</span>
                            ${isAdded ? '<i class="fa-solid fa-check-circle text-green-500 text-sm"></i>' : `<i class="fa-solid fa-plus text-brand-text-secondary opacity-0 group-hover:opacity-100 transition-opacity text-sm"></i>`}
                        </div>
                        <p class="font-bold text-[12px] leading-tight ${isAdded ? 'text-green-800' : 'text-brand-text-primary group-hover:text-brand-primary'} line-clamp-2">${ex.name}</p>
                    </div>
                    <div class="flex justify-between items-center text-[10px] mt-2 pt-2 border-t border-gray-100">
                        <span class="text-brand-text-secondary font-bold uppercase">${ex.difficulty}</span>
                        <span class="text-brand-text-secondary truncate ml-1" title="${ex.equipment}"><i class="fa-solid fa-dumbbell mr-1"></i>${ex.equipment || 'Peso Corporal'}</span>
                    </div>
                `;
                catalog.appendChild(card);
            });
            filterExercises();
        }

        function addExercise(ex) {
            if (routineExercises.find(e => e.id === ex.id)) return;
            if (routineExercises.length >= MAX_EXERCISES) return alert(`Límite: máximo ${MAX_EXERCISES} ejercicios.`);

            routineExercises.push({ id: ex.id, name: ex.name, difficulty: ex.difficulty, sets: 3, reps: 10, rest: 60 });
            renderRoutineBlocks();
            renderCatalog();
        }

        function removeExercise(index) {
            routineExercises.splice(index, 1);
            renderRoutineBlocks();
            renderCatalog();
        }

        function updateExercise(index, field, value) {
            let numValue = parseInt(value) || 0;
            if (field === 'sets' && numValue > MAX_SETS) numValue = MAX_SETS;
            if (field === 'reps' && numValue > MAX_REPS) numValue = MAX_REPS;
            if (numValue < 1) numValue = 1;
            routineExercises[index][field] = numValue;
            calculateDifficulty(); 
        }

        function renderRoutineBlocks() {
            const container = document.getElementById('routineBlocks');
            const emptyState = document.getElementById('emptyState');
            
            Array.from(container.children).forEach(child => {
                if (child.id !== 'emptyState') child.remove();
            });

            if (routineExercises.length === 0) {
                emptyState.classList.remove('hidden');
                calculateDifficulty();
                return;
            }

            emptyState.classList.add('hidden');

            routineExercises.forEach((ex, index) => {
                const block = document.createElement('div');
                block.className = "bg-white rounded-xl border border-brand-border p-3 mb-3 shadow-sm flex flex-col gap-3 animate-slide-in relative z-10 hover:border-brand-primary/50 transition-colors";
                
                block.innerHTML = `
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-2">
                            <span class="w-6 h-6 rounded-md bg-brand-primary text-white text-[11px] font-black flex items-center justify-center shrink-0">${index + 1}</span>
                            <p class="font-bold text-sm text-brand-text-primary leading-tight">${ex.name}</p>
                        </div>
                        <button onclick="removeExercise(${index})" class="w-7 h-7 rounded-md bg-red-50 text-brand-danger flex items-center justify-center hover:bg-red-100 transition-colors shrink-0">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>
                    </div>
                    
                    <div class="flex items-center justify-between bg-brand-surface-muted/50 p-2 rounded-lg border border-brand-border/50">
                        <div class="flex flex-col items-center w-1/3">
                            <label class="text-[9px] font-bold text-brand-text-secondary uppercase mb-1">Series</label>
                            <input type="number" min="1" max="${MAX_SETS}" value="${ex.sets}" onchange="updateExercise(${index}, 'sets', this.value)" class="w-full text-center bg-white border border-brand-border rounded-md py-1.5 text-sm font-bold focus:ring-1 focus:ring-brand-primary outline-none transition-all">
                        </div>
                        <span class="text-brand-text-secondary font-black text-xs mt-3 px-1">×</span>
                        <div class="flex flex-col items-center w-1/3">
                            <label class="text-[9px] font-bold text-brand-text-secondary uppercase mb-1">Reps</label>
                            <input type="number" min="1" max="${MAX_REPS}" value="${ex.reps}" onchange="updateExercise(${index}, 'reps', this.value)" class="w-full text-center bg-white border border-brand-border rounded-md py-1.5 text-sm font-bold focus:ring-1 focus:ring-brand-primary outline-none transition-all">
                        </div>
                        <div class="flex flex-col items-center w-1/3 ml-2">
                            <label class="text-[9px] font-bold text-brand-text-secondary uppercase mb-1">Desc.(s)</label>
                            <input type="number" min="0" value="${ex.rest}" onchange="updateExercise(${index}, 'rest', this.value)" class="w-full text-center bg-white border border-brand-border rounded-md py-1.5 text-sm font-bold focus:ring-1 focus:ring-brand-primary outline-none transition-all">
                        </div>
                    </div>
                `;
                container.appendChild(block);
            });

            calculateDifficulty();
        }

        function calculateDifficulty() {
            const bar = document.getElementById('difficultyBar');
            const text = document.getElementById('difficultyText');
            const warning = document.getElementById('warningMsg');
            const btnSave = document.getElementById('btnSave');

            if (routineExercises.length === 0) {
                text.innerText = "Vacía";
                text.className = "text-xs font-black text-gray-400 w-20 text-right";
                bar.style.width = "0%";
                bar.className = "h-full bg-gray-300 transition-all duration-500 ease-out";
                warning.classList.add('hidden');
                btnSave.classList.add('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
                btnSave.classList.remove('bg-brand-primary', 'text-white', 'hover:bg-brand-primary/90', 'shadow-md');
                return;
            }

            let totalScore = 0;
            routineExercises.forEach(ex => {
                let basePoints = (ex.sets * ex.reps);
                let multiplier = 1;
                if (ex.difficulty === 'intermediate') multiplier = 1.5;
                if (ex.difficulty === 'advanced') multiplier = 2.5;
                totalScore += (basePoints * multiplier);
            });

            let calculatedLevel = 'beginner';
            let barColor = 'bg-green-500';
            let barWidth = Math.min((totalScore / 250) * 100, 100) + '%';
            let textClass = "text-green-600";
            let levelName = "Principiante";

            if (totalScore >= 100 && totalScore < 250) {
                calculatedLevel = 'intermediate';
                barColor = 'bg-orange-500';
                textClass = "text-orange-600";
                levelName = "Intermedio";
            } else if (totalScore >= 250) {
                calculatedLevel = 'advanced';
                barColor = 'bg-red-500';
                textClass = "text-red-600";
                levelName = "Avanzado";
            }

            text.innerText = levelName;
            text.className = `text-xs font-black ${textClass} w-20 text-right`;
            bar.style.width = barWidth;
            bar.className = `h-full ${barColor} transition-all duration-500 ease-out`;

            const levelPower = { 'beginner': 1, 'intermediate': 2, 'advanced': 3 };
            if (levelPower[calculatedLevel] > levelPower[userLevel]) {
                warning.classList.remove('hidden');
            } else {
                warning.classList.add('hidden');
            }

            btnSave.classList.remove('bg-gray-300', 'text-gray-500', 'cursor-not-allowed');
            btnSave.classList.add('bg-brand-primary', 'text-white', 'hover:bg-brand-primary/90', 'shadow-md');
        }

        function filterExercises() {
            const query = document.getElementById('searchExercise').value.toLowerCase();
            const cards = document.getElementById('exerciseCatalog').children;
            
            Array.from(cards).forEach((card, index) => {
                const ex = allExercises[index];
                const matchesSearch = ex.name.toLowerCase().includes(query);
                const matchesMuscle = currentMuscleFilter === 'all' || ex.muscle_group === currentMuscleFilter;

                if (matchesSearch && matchesMuscle) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        function openInfoModal() {
            document.getElementById('infoModal').classList.remove('hidden');
            document.getElementById('infoModal').classList.add('flex');
        }
        function closeInfoModal() {
            document.getElementById('infoModal').classList.add('hidden');
            document.getElementById('infoModal').classList.remove('flex');
        }

        function saveRoutine() {
            const name = document.getElementById('routineName').value;
            if (!name) { alert("Ponle un nombre a tu rutina."); return; }
            if (routineExercises.length === 0) return;

            const badgeText = document.getElementById('difficultyText').innerText;
            const diffMap = { 'Principiante': 'beginner', 'Intermedio': 'intermediate', 'Avanzado': 'advanced' };
            const difficulty = diffMap[badgeText] || 'beginner';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = "{{ route('entrenamientos.crear.guardar') }}";
            
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden'; csrfInput.name = '_token'; csrfInput.value = "{{ csrf_token() }}";
            form.appendChild(csrfInput);

            const fields = { name, environment: 'home', difficulty, description: 'Rutina personalizada' };
            for (const [key, value] of Object.entries(fields)) {
                const input = document.createElement('input');
                input.type = 'hidden'; input.name = key; input.value = value;
                form.appendChild(input);
            }

            routineExercises.forEach((ex, index) => {
                const fields = { [`exercises[${index}][id]`]: ex.id, [`exercises[${index}][sets]`]: ex.sets, [`exercises[${index}][reps]`]: ex.reps, [`exercises[${index}][rest]`]: ex.rest };
                for (const [key, value] of Object.entries(fields)) {
                    const input = document.createElement('input');
                    input.type = 'hidden'; input.name = key; input.value = value;
                    form.appendChild(input);
                }
            });

            document.body.appendChild(form);
            form.submit();
        }

        document.addEventListener("DOMContentLoaded", () => {
            renderCatalog();
            calculateDifficulty();
        });
    </script>
@endsection