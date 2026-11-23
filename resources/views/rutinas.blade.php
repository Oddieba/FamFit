@extends('layouts.quest')

@section('title', 'Rutinas | FamFit Quest')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: { colors: { brand: { primary: '#2864F0', surface: '#FFFFFF', 'surface-muted': '#EEF2F8', 'text-primary': '#172033', 'text-secondary': '#64748B', border: '#DCE4EF', success: '#A7E66D', habit: '#19C6B0', warning: '#F2B85B' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } }
        }
    </script>

    <main class="w-full h-full font-sans text-brand-text-primary p-6 md:p-10 overflow-y-auto">
        
        <!-- Botón Volver y Título Dinámico -->
        <div class="max-w-6xl mx-auto mb-8">
            <a href="{{ route('entrenamientos') }}" class="inline-flex items-center text-sm font-bold text-brand-text-secondary hover:text-brand-primary mb-4 transition-colors">
                <i class="fa-solid fa-arrow-left mr-2"></i> Volver a Entornos
            </a>
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold capitalize">Rutinas: {{ $entorno }}</h1>
                    <p class="text-brand-text-secondary text-sm mt-1">Explora, filtra o crea tu propia rutina personalizada.</p>
                </div>
                <!-- Botón Crear Rutina -->
                <button class="px-5 py-2.5 bg-brand-primary text-white font-bold rounded-xl shadow-md hover:bg-brand-primary/90 transition-all flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i> Crear mi rutina
                </button>
            </div>
        </div>

        <div class="max-w-6xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- COLUMNA IZQUIERDA: Recomendaciones del Test -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-gradient-to-br from-brand-primary to-blue-600 rounded-2xl p-6 text-white shadow-lg relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-10">
                        <i class="fa-solid fa-wand-magic-sparkles text-6xl"></i>
                    </div>
                    <h3 class="font-extrabold text-lg mb-2">Recomendado para ti</h3>
                    <p class="text-xs text-blue-100 mb-4">Basado en los resultados de tu Test Inicial.</p>
                    
                    <!-- Aquí usarás los datos de $perfil en el futuro -->
                    <div class="bg-white/10 rounded-xl p-4 backdrop-blur-sm border border-white/20">
                        <p class="text-sm font-bold mb-1"><i class="fa-solid fa-bullseye mr-2 text-brand-success"></i> Objetivo: {{ ucfirst(str_replace('_', ' ', $perfil->goals ?? 'General')) }}</p>
                        <p class="text-sm font-bold mb-1"><i class="fa-solid fa-chart-line mr-2 text-brand-success"></i> Nivel: {{ ucfirst($perfil->experience ?? 'Principiante') }}</p>
                        <p class="text-sm font-bold"><i class="fa-solid fa-clock mr-2 text-brand-success"></i> Tiempo: 30-45 min</p>
                    </div>
                    
                    <button class="w-full mt-4 py-2 bg-white text-brand-primary font-bold text-sm rounded-lg hover:bg-gray-50 transition-colors">
                        Ver rutinas sugeridas
                    </button>
                </div>
            </div>

            <!-- COLUMNA DERECHA: Tabla de Rutinas -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Filtros -->
                <div class="flex space-x-2 border-b border-brand-border pb-4">
                    <button class="px-4 py-2 bg-brand-surface-muted text-brand-text-primary font-bold text-sm rounded-lg border border-brand-border hover:bg-gray-100">Todas</button>
                    <button class="px-4 py-2 text-brand-text-secondary font-bold text-sm rounded-lg hover:bg-brand-surface-muted">Famosas</button>
                    <button class="px-4 py-2 text-brand-text-secondary font-bold text-sm rounded-lg hover:bg-brand-surface-muted">Comunidad</button>
                    <button class="px-4 py-2 text-brand-text-secondary font-bold text-sm rounded-lg hover:bg-brand-surface-muted">Mis rutinas</button>
                </div>

                <!-- Tabla -->
                <div class="bg-brand-surface rounded-2xl border border-brand-border overflow-hidden shadow-sm">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-brand-surface-muted text-brand-text-secondary text-xs uppercase tracking-wider">
                                <th class="p-4 font-bold">Nombre de la Rutina</th>
                                <th class="p-4 font-bold">Dificultad</th>
                                <th class="p-4 font-bold">Duración</th>
                                <th class="p-4 font-bold">Tipo</th>
                                <th class="p-4 font-bold text-center">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-brand-border">
                            <!-- Rutina de Ejemplo 1 -->
                            <tr class="hover:bg-brand-surface-muted/50 transition-colors">
                                <td class="p-4">
                                    <span class="font-bold text-sm block">Rutina Full Body (Famosa)</span>
                                    <span class="text-xs text-brand-text-secondary">Ideal para empezar</span>
                                </td>
                                <td class="p-4"><span class="px-2 py-1 bg-green-100 text-green-700 text-xs font-bold rounded-md">Principiante</span></td>
                                <td class="p-4 text-sm font-medium">45 min</td>
                                <td class="p-4 text-sm font-medium">Fuerza</td>
                                <td class="p-4 text-center">
                                    <button class="text-brand-primary hover:text-blue-700 font-bold text-sm">Ver / Iniciar</button>
                                </td>
                            </tr>

                            <!-- Rutina de Ejemplo 2 -->
                            <tr class="hover:bg-brand-surface-muted/50 transition-colors">
                                <td class="p-4">
                                    <span class="font-bold text-sm block">Hipertrofia 5x5</span>
                                    <span class="text-xs text-brand-text-secondary">Clásica de Gimnasio</span>
                                </td>
                                <td class="p-4"><span class="px-2 py-1 bg-orange-100 text-orange-700 text-xs font-bold rounded-md">Avanzado</span></td>
                                <td class="p-4 text-sm font-medium">60 min</td>
                                <td class="p-4 text-sm font-medium">Fuerza</td>
                                <td class="p-4 text-center">
                                    <button class="text-brand-primary hover:text-blue-700 font-bold text-sm">Ver / Iniciar</button>
                                </td>
                            </tr>

                            <!-- Rutina de Ejemplo 3 -->
                            <tr class="hover:bg-brand-surface-muted/50 transition-colors">
                                <td class="p-4">
                                    <span class="font-bold text-sm block">Mi rutina personalizada</span>
                                    <span class="text-xs text-brand-text-secondary">Creada por ti</span>
                                </td>
                                <td class="p-4"><span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-md">A tu nivel</span></td>
                                <td class="p-4 text-sm font-medium">30 min</td>
                                <td class="p-4 text-sm font-medium">Mixto</td>
                                <td class="p-4 text-center">
                                    <button class="text-brand-primary hover:text-blue-700 font-bold text-sm">Ver / Iniciar</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>
@endsection