<!DOCTYPE html>
<html lang="es" class="h-full bg-[#F5F7FB]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamFit Quest - Acondicionamiento Físico</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Configuración Estricta de Paleta Oficial FamFit Quest -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            primary: '#2864F0',
                            bg: '#F5F7FB',
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
                        sans: ['Inter', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #F5F7FB;
            color: #172033;
        }
        .card-transition {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .card-transition:hover {
            transform: translateY(-6px);
            box-shadow: 0 16px 30px -10px rgba(40, 100, 240, 0.15);
        }
        .img-zoom {
            transition: transform 0.6s ease;
        }
        .card-transition:hover .img-zoom {
            transform: scale(1.06);
        }
        /* Custom Scrollbar for Modal */
        .custom-scroll::-webkit-scrollbar {
            width: 6px;
        }
        .custom-scroll::-webkit-scrollbar-track {
            background: #EEF2F8;
            border-radius: 8px;
        }
        .custom-scroll::-webkit-scrollbar-thumb {
            background: #64748B;
            border-radius: 8px;
        }
    </style>
</head>
<body class="min-h-full flex flex-col justify-between selection:bg-brand-habit selection:text-white">

    <!-- Header / Navbar Principal -->
    <header class="bg-brand-surface border-b border-brand-border sticky top-0 z-40 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo & Back Link -->
            <div class="flex items-center space-x-4">
                <button onclick="showToast('Regresando al menú principal de inicio...', 'info')" class="p-2 rounded-xl text-brand-text-secondary hover:text-brand-primary hover:bg-brand-surface-muted transition-colors">
                    <i class="fa-solid fa-arrow-left text-lg"></i>
                </button>
                <div class="flex items-center space-x-3 cursor-pointer" onclick="showToast('FamFit Quest - Panel Principal', 'info')">
                    <div class="w-10 h-10 rounded-xl bg-brand-primary flex items-center justify-center text-white font-extrabold text-xl shadow-md">
                        F
                    </div>
                    <div>
                        <span class="font-extrabold text-lg text-brand-text-primary tracking-tight">FamFit</span>
                        <span class="text-[10px] font-bold px-2 py-0.5 rounded-full bg-brand-surface-muted text-brand-primary border border-brand-border ml-1 uppercase">Quest</span>
                    </div>
                </div>
            </div>

            <!-- Gamification & User Summary Header -->
            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Racha -->
                <div class="flex items-center space-x-1.5 bg-brand-surface-muted px-3 py-1.5 rounded-full border border-brand-border text-xs font-bold text-brand-text-primary">
                    <i class="fa-solid fa-fire text-brand-warning"></i>
                    <span>5 Días</span>
                </div>

                <!-- Nivel & XP -->
                <div class="hidden sm:flex items-center space-x-2 bg-brand-surface-muted px-3 py-1.5 rounded-full border border-brand-border text-xs font-bold text-brand-text-primary">
                    <i class="fa-solid fa-gem text-brand-habit"></i>
                    <span>Nivel 4 • <span class="text-brand-primary">850 XP</span></span>
                </div>

                <!-- Avatar Profile Link -->
                <button onclick="showToast('Abriendo perfil de usuario y avatar', 'info')" class="w-9 h-9 rounded-full bg-brand-surface-muted border border-brand-border flex items-center justify-center text-brand-primary hover:border-brand-primary transition-all">
                    <i class="fa-solid fa-user text-sm"></i>
                </button>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 w-full flex flex-col justify-center">

        <!-- Header de Sección -->
        <div class="text-center max-w-3xl mx-auto mb-8 sm:mb-10">
            <div class="inline-flex items-center space-x-2 bg-brand-surface-muted border border-brand-border px-3 py-1 rounded-full mb-3">
                <span class="w-2 h-2 rounded-full bg-brand-habit"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-text-secondary">Módulo de Acondicionamiento Físico</span>
            </div>
            <h1 class="text-2xl sm:text-4xl font-extrabold text-brand-text-primary tracking-tight leading-tight">
                Selecciona el entorno donde quieres llevar a cabo tus ejercicios
            </h1>
            <p class="mt-3 text-sm sm:text-base text-brand-text-secondary leading-relaxed">
                Elige tu espacio de entrenamiento. Adaptamos las rutinas, tiempos y objetivos según tu equipo y disponibilidad actual.
            </p>

            <!-- Filtro de Dificultad Rápida -->
            <div class="mt-6 flex flex-wrap justify-center gap-2">
                <span class="text-xs font-semibold text-brand-text-secondary self-center mr-2">Filtro rápido:</span>
                <button onclick="setDifficulty('Todos', this)" class="diff-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-primary text-white transition-all shadow-sm">
                    Todos los niveles
                </button>
                <button onclick="setDifficulty('Principiante', this)" class="diff-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-surface-muted text-brand-text-secondary hover:text-brand-text-primary border border-brand-border transition-all">
                    Principiante
                </button>
                <button onclick="setDifficulty('Intermedio', this)" class="diff-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-surface-muted text-brand-text-secondary hover:text-brand-text-primary border border-brand-border transition-all">
                    Intermedio
                </button>
                <button onclick="setDifficulty('Avanzado', this)" class="diff-btn px-3 py-1.5 rounded-lg text-xs font-bold bg-brand-surface-muted text-brand-text-secondary hover:text-brand-text-primary border border-brand-border transition-all">
                    Avanzado
                </button>
            </div>
        </div>

        <!-- Cards Grid (3 Columnas principales) -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 max-w-6xl mx-auto w-full">

            <!-- TARJETA 1: EN CASA -->
            <div class="card-transition bg-brand-surface rounded-2xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm hover:border-brand-primary/50 group">
                <div>
                    <!-- Imagen en Recuadro Atractivo -->
                    <div class="relative h-52 sm:h-56 w-full overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=800&q=80"
                             alt="Entrenamiento En Casa"
                             class="img-zoom w-full h-full object-cover"
                             onerror="this.src='https://placehold.co/600x400/EEF2F8/172033?text=Entrenamiento+en+Casa'">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-text-primary/70 via-transparent to-transparent opacity-80"></div>

                        <!-- Badges en Imagen -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                            <span class="bg-brand-surface/95 backdrop-blur-md text-brand-text-primary font-bold text-xs px-2.5 py-1 rounded-lg border border-brand-border shadow-sm flex items-center">
                                <i class="fa-solid fa-house text-brand-primary mr-1.5"></i> En Casa
                            </span>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-brand-success text-brand-text-primary font-bold text-xs px-2.5 py-1 rounded-lg shadow-sm flex items-center">
                                <i class="fa-solid fa-bolt mr-1"></i> +100 XP
                            </span>
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <p class="text-xs font-medium text-gray-200">Ideal para espacios pequeños</p>
                        </div>
                    </div>

                    <!-- Contenido y Características Desglosadas -->
                    <div class="p-6">
                        <h2 class="text-xl font-extrabold text-brand-text-primary mb-2 group-hover:text-brand-primary transition-colors flex items-center justify-between">
                            <span>En Casa</span>
                            <i class="fa-solid fa-mat-fit text-brand-text-secondary text-sm font-normal"></i>
                        </h2>

                        <p class="text-xs text-brand-text-secondary mb-4 leading-relaxed font-medium">
                            ¡Ejercita tu cuerpo sin necesidad de salir de casa y ponte en forma! Podrás obtener rutinas con base a objetos a tu disposición.
                        </p>

                        <div class="border-t border-brand-border pt-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-primary block mb-2">Lo que incluye:</span>
                            <ul class="text-xs text-brand-text-secondary space-y-2.5">
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Calistenia avanzada:</strong> Ejercicios con peso corporal adaptables.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Entrenamiento silencioso:</strong> Rutinas sin saltos ni ruido para apartamentos.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Corrección postural:</strong> Pausas activas para trabajo y estudio prolongado.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Duración flexible:</strong> Sesiones exprés de 15 min hasta completas de 45 min.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botón Acción Principal -->
                <div class="p-6 pt-0">
                    <button onclick="openRoutineModal('En Casa', 'casa')" class="w-full py-3 px-4 bg-brand-primary hover:bg-brand-primary/90 text-white font-bold text-sm rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                        <span>Explorar Rutinas en Casa</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- TARJETA 2: GIMNASIO -->
            <div class="card-transition bg-brand-surface rounded-2xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm hover:border-brand-primary/50 group">
                <div>
                    <!-- Imagen en Recuadro Atractivo -->
                    <div class="relative h-52 sm:h-56 w-full overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80"
                             alt="Entrenamiento en Gimnasio"
                             class="img-zoom w-full h-full object-cover"
                             onerror="this.src='https://placehold.co/600x400/EEF2F8/172033?text=Entrenamiento+en+Gimnasio'">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-text-primary/70 via-transparent to-transparent opacity-80"></div>

                        <!-- Badges en Imagen -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                            <span class="bg-brand-surface/95 backdrop-blur-md text-brand-text-primary font-bold text-xs px-2.5 py-1 rounded-lg border border-brand-border shadow-sm flex items-center">
                                <i class="fa-solid fa-dumbbell text-brand-primary mr-1.5"></i> Gimnasio
                            </span>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-brand-success text-brand-text-primary font-bold text-xs px-2.5 py-1 rounded-lg shadow-sm flex items-center">
                                <i class="fa-solid fa-bolt mr-1"></i> +120 XP
                            </span>
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <p class="text-xs font-medium text-gray-200">Mancuernas, barras y máquinas</p>
                        </div>
                    </div>

                    <!-- Contenido y Características Desglosadas -->
                    <div class="p-6">
                        <h2 class="text-xl font-extrabold text-brand-text-primary mb-2 group-hover:text-brand-primary transition-colors flex items-center justify-between">
                            <span>Gimnasio</span>
                            <i class="fa-solid fa-dumbbell text-brand-text-secondary text-sm font-normal"></i>
                        </h2>

                        <p class="text-xs text-brand-text-secondary mb-4 leading-relaxed font-medium">
                            Máquinas, mancuernas y elementos a tu disposición. Utiliza rutinas famosas o obtén una personalizada para ti.
                        </p>

                        <div class="border-t border-brand-border pt-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-primary block mb-2">Lo que incluye:</span>
                            <ul class="text-xs text-brand-text-secondary space-y-2.5">
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Guía de técnica postural:</strong> Animaciones explicativas paso a paso.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Bitácora de cargas:</strong> Registro preciso de series, peso y repeticiones.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Sobrecarga progresiva:</strong> Recomendaciones automáticas de incremento.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Temporizador de descanso:</strong> Alertador de intervalos entre series.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botón Acción Principal -->
                <div class="p-6 pt-0">
                    <button onclick="openRoutineModal('Gimnasio', 'gym')" class="w-full py-3 px-4 bg-brand-primary hover:bg-brand-primary/90 text-white font-bold text-sm rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                        <span>Explorar Rutinas de Gym</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

            <!-- TARJETA 3: AL AIRE LIBRE -->
            <div class="card-transition bg-brand-surface rounded-2xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm hover:border-brand-primary/50 group">
                <div>
                    <!-- Imagen en Recuadro Atractivo -->
                    <div class="relative h-52 sm:h-56 w-full overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?auto=format&fit=crop&w=800&q=80"
                             alt="Entrenamiento Al Aire Libre"
                             class="img-zoom w-full h-full object-cover"
                             onerror="this.src='https://placehold.co/600x400/EEF2F8/172033?text=Al+Aire+Libre'">
                        <div class="absolute inset-0 bg-gradient-to-t from-brand-text-primary/70 via-transparent to-transparent opacity-80"></div>

                        <!-- Badges en Imagen -->
                        <div class="absolute top-3 left-3 flex flex-wrap gap-2">
                            <span class="bg-brand-surface/95 backdrop-blur-md text-brand-text-primary font-bold text-xs px-2.5 py-1 rounded-lg border border-brand-border shadow-sm flex items-center">
                                <i class="fa-solid fa-person-running text-brand-habit mr-1.5"></i> Al Aire Libre
                            </span>
                        </div>
                        <div class="absolute top-3 right-3">
                            <span class="bg-brand-success text-brand-text-primary font-bold text-xs px-2.5 py-1 rounded-lg shadow-sm flex items-center">
                                <i class="fa-solid fa-bolt mr-1"></i> +110 XP
                            </span>
                        </div>

                        <div class="absolute bottom-3 left-3 right-3 text-white">
                            <p class="text-xs font-medium text-gray-200">Rutas GPS, parques y trote</p>
                        </div>
                    </div>

                    <!-- Contenido y Características Desglosadas -->
                    <div class="p-6">
                        <h2 class="text-xl font-extrabold text-brand-text-primary mb-2 group-hover:text-brand-primary transition-colors flex items-center justify-between">
                            <span>Al Aire Libre</span>
                            <i class="fa-solid fa-tree text-brand-text-secondary text-sm font-normal"></i>
                        </h2>

                        <p class="text-xs text-brand-text-secondary mb-4 leading-relaxed font-medium">
                            Realiza trotes, caminatas o sprints mediante geolocalización y cuenta tus calorías quemadas con base en el tiempo.
                        </p>

                        <div class="border-t border-brand-border pt-3">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-brand-primary block mb-2">Lo que incluye:</span>
                            <ul class="text-xs text-brand-text-secondary space-y-2.5">
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Circuitos urbanos:</strong> Calistenia en barras fijas y parque público.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Mapa GPS en vivo:</strong> Mapeo de ritmo promedio, elevación y distancia.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Misiones secundarias:</strong> Bonos extra por metas de pasos e hidratación.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fa-solid fa-circle-check text-brand-habit text-sm mt-0.5 mr-2 flex-shrink-0"></i>
                                    <span><strong>Comunidad local:</strong> Desafíos de racha compartidos con amigos.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Botón Acción Principal -->
                <div class="p-6 pt-0">
                    <button onclick="openRoutineModal('Al Aire Libre', 'aire')" class="w-full py-3 px-4 bg-brand-primary hover:bg-brand-primary/90 text-white font-bold text-sm rounded-xl transition-all shadow-md hover:shadow-lg flex items-center justify-center space-x-2">
                        <span>Explorar Rutinas al Aire Libre</span>
                        <i class="fa-solid fa-chevron-right text-xs"></i>
                    </button>
                </div>
            </div>

        </div>

    </main>

    <!-- Footer Institucional -->
    <footer class="bg-brand-surface border-t border-brand-border py-6 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <div class="flex items-center justify-center space-x-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-brand-habit"></span>
                <span class="text-xs font-bold text-brand-text-primary uppercase tracking-wider">FamFit Quest • Análisis y Desarrollo de Software</span>
            </div>
            <p class="text-xs text-brand-text-secondary">
                Las sugerencias de entrenamiento son orientativas. Mantén una hidratación adecuada y entrena según tus capacidades.
            </p>
        </div>
    </footer>

    <!-- MODAL DE PREVISUALIZACIÓN Y SELECCIÓN DE RUTINAS -->
    <div id="routineModal" class="fixed inset-0 bg-brand-text-primary/60 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
        <div class="bg-brand-surface rounded-2xl border border-brand-border shadow-2xl max-w-lg w-full overflow-hidden transform transition-all">
            <!-- Header Modal -->
            <div class="bg-brand-surface-muted px-6 py-4 border-b border-brand-border flex items-center justify-between">
                <div class="flex items-center space-x-2">
                    <span id="modalCategoryBadge" class="p-2 rounded-xl bg-brand-primary/10 text-brand-primary text-sm font-bold">
                        <i class="fa-solid fa-dumbbell"></i>
                    </span>
                    <div>
                        <h3 id="modalTitle" class="text-lg font-extrabold text-brand-text-primary">Rutinas Seleccionadas</h3>
                        <p class="text-xs text-brand-text-secondary">Elige la rutina recomendada para tu sesión</p>
                    </div>
                </div>
                <button onclick="closeRoutineModal()" class="w-8 h-8 rounded-full bg-brand-surface border border-brand-border flex items-center justify-center text-brand-text-secondary hover:text-brand-danger transition-colors">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>

            <!-- Cuerpo Modal (Lista de Rutinas) -->
            <div class="p-6 space-y-4 max-h-[60vh] overflow-y-auto custom-scroll" id="modalRoutineList">
                <!-- Se llena dinámicamente con JS -->
            </div>

            <!-- Footer Modal -->
            <div class="bg-brand-surface-muted px-6 py-4 border-t border-brand-border flex items-center justify-between">
                <span class="text-xs text-brand-text-secondary">Recompensa estimada: <strong class="text-brand-text-primary">+100-150 XP</strong></span>
                <button onclick="closeRoutineModal()" class="px-4 py-2 bg-brand-surface hover:bg-brand-border text-brand-text-primary font-bold text-xs rounded-xl border border-brand-border transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>

    <!-- NOTIFICACIÓN TIPO TOAST INTERACTIVA -->
    <div id="toastNotification" class="fixed bottom-6 right-6 translate-y-20 opacity-0 transition-all duration-300 bg-brand-surface border border-brand-border text-brand-text-primary p-4 rounded-2xl shadow-2xl flex items-center space-x-3 z-50 max-w-sm pointer-events-none">
        <div id="toastIconContainer" class="w-10 h-10 rounded-xl bg-brand-habit/20 text-brand-habit flex items-center justify-center flex-shrink-0">
            <i id="toastIcon" class="fa-solid fa-circle-check text-lg"></i>
        </div>
        <div>
            <p id="toastTitle" class="text-sm font-extrabold text-brand-text-primary">Notificación</p>
            <p id="toastMessage" class="text-xs text-brand-text-secondary mt-0.5">Mensaje del sistema...</p>
        </div>
    </div>

    <!-- Scripts de Interactividad -->
    <script>
        // Banco de datos mock para rutinas según entorno
        const mockRoutines = {
            casa: [
                { title: "Calistenia Total Body", duration: "25 min", difficulty: "Principiante", xp: "+100 XP", desc: "Flexiones con apoyo, sentadillas con peso corporal y planchas sostenidas." },
                { title: "HIIT Espacio Reducido (Sin Saltos)", duration: "20 min", difficulty: "Intermedio", xp: "+120 XP", desc: "Sprints estáticos, zancadas alternativas y escaladores controlados." },
                { title: "Postura & Espalda Sana", duration: "15 min", difficulty: "Principiante", xp: "+80 XP", desc: "Estiramientos activos para liberar tensión lumbar y cervical." }
            ],
            gym: [
                { title: "Hipertrofia Torso (Pecho / Espalda)", duration: "50 min", difficulty: "Intermedio", xp: "+130 XP", desc: "Press de banca, remo con barra y polea alta con descanso controlado." },
                { title: "Fuerza Pierna & Core", duration: "45 min", difficulty: "Avanzado", xp: "+150 XP", desc: "Sentadilla libre, prensa inclinado y peso muerto rumano." },
                { title: "Acondicionamiento Funcional Gym", duration: "35 min", difficulty: "Principiante", xp: "+110 XP", desc: "Circuito con mancuernas ligeras y poleas continuas." }
            ],
            aire: [
                { title: "Intervalos Trote / Caminata GPS", duration: "30 min", difficulty: "Principiante", xp: "+100 XP", desc: "2 min trote ligero x 1 min caminata en parque o avenida." },
                { title: "Circuito Calistenia en Parque", duration: "40 min", difficulty: "Avanzado", xp: "+140 XP", desc: "Dominadas en barras fijas, fondos en paralelas y sprints cortantes." },
                { title: "Ruta Cardio Quemagrasas", duration: "45 min", difficulty: "Intermedio", xp: "+125 XP", desc: "Caminata rápida sostenida con geolocalización continua." }
            ]
        };

        // Abrir Modal con contenido dinámico
        function openRoutineModal(envTitle, envKey) {
            const modal = document.getElementById('routineModal');
            const modalTitle = document.getElementById('modalTitle');
            const modalRoutineList = document.getElementById('modalRoutineList');
            const modalCategoryBadge = document.getElementById('modalCategoryBadge');

            modalTitle.innerText = `Rutinas en ${envTitle}`;

            // Icono del modal
            if(envKey === 'casa') modalCategoryBadge.innerHTML = '<i class="fa-solid fa-house text-brand-primary"></i>';
            if(envKey === 'gym') modalCategoryBadge.innerHTML = '<i class="fa-solid fa-dumbbell text-brand-primary"></i>';
            if(envKey === 'aire') modalCategoryBadge.innerHTML = '<i class="fa-solid fa-person-running text-brand-habit"></i>';

            const routines = mockRoutines[envKey] || [];

            modalRoutineList.innerHTML = routines.map(r => `
                <div class="p-4 rounded-xl border border-brand-border bg-brand-surface-muted hover:bg-brand-surface transition-all flex flex-col justify-between space-y-3 group">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-[10px] font-extrabold uppercase tracking-wider px-2 py-0.5 rounded bg-brand-surface text-brand-primary border border-brand-border">${r.difficulty}</span>
                            <h4 class="text-sm font-bold text-brand-text-primary mt-1.5">${r.title}</h4>
                        </div>
                        <span class="text-xs font-bold text-brand-text-primary bg-brand-success px-2 py-1 rounded-lg">${r.xp}</span>
                    </div>
                    <p class="text-xs text-brand-text-secondary leading-relaxed">${r.desc}</p>
                    <div class="flex items-center justify-between pt-2 border-t border-brand-border/60">
                        <span class="text-xs font-medium text-brand-text-secondary"><i class="fa-regular fa-clock mr-1"></i> ${r.duration}</span>
                        <button onclick="startRoutine('${r.title}', '${r.xp}')" class="px-3 py-1.5 bg-brand-primary text-white font-bold text-xs rounded-lg hover:bg-brand-primary/90 transition-all flex items-center space-x-1 shadow-sm">
                            <span>Iniciar</span>
                            <i class="fa-solid fa-play text-[10px]"></i>
                        </button>
                    </div>
                </div>
            `).join('');

            modal.classList.remove('hidden');
        }

        // Cerrar Modal
        function closeRoutineModal() {
            document.getElementById('routineModal').classList.add('hidden');
        }

        // Iniciar Rutina
        function startRoutine(title, xp) {
            closeRoutineModal();
            showToast(`¡Rutina iniciada: ${title}! Recompensa al finalizar ${xp}`, 'success');
        }

        // Filtro rápido de dificultad
        function setDifficulty(diff, btn) {
            document.querySelectorAll('.diff-btn').forEach(b => {
                b.classList.remove('bg-brand-primary', 'text-white');
                b.classList.add('bg-brand-surface-muted', 'text-brand-text-secondary');
            });
            btn.classList.remove('bg-brand-surface-muted', 'text-brand-text-secondary');
            btn.classList.add('bg-brand-primary', 'text-white');

            showToast(`Filtrando ejercicios por nivel: ${diff}`, 'info');
        }

        // Mostrar Toast interactivo
        function showToast(msg, type = 'info') {
            const toast = document.getElementById('toastNotification');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');
            const iconContainer = document.getElementById('toastIconContainer');

            toastTitle.innerText = type === 'success' ? '¡Éxito!' : 'FamFit Quest';
            toastMessage.innerText = msg;

            if(type === 'success') {
                iconContainer.className = "w-10 h-10 rounded-xl bg-brand-success/30 text-brand-text-primary flex items-center justify-center flex-shrink-0";
                toastIcon.className = "fa-solid fa-trophy text-lg";
            } else {
                iconContainer.className = "w-10 h-10 rounded-xl bg-brand-habit/20 text-brand-habit flex items-center justify-center flex-shrink-0";
                toastIcon.className = "fa-solid fa-circle-info text-lg";
            }

            toast.classList.remove('translate-y-20', 'opacity-0');
            toast.classList.add('translate-y-0', 'opacity-100');

            setTimeout(() => {
                toast.classList.remove('translate-y-0', 'opacity-100');
                toast.classList.add('translate-y-20', 'opacity-0');
            }, 3000);
        }
    </script>
</body>
</html>