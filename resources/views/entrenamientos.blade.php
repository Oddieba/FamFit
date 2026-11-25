<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamFit Quest - Acondicionamiento Físico</title>
    <script src="{{ asset('js/theme.js') }}"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Google Fonts Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

@section('title', 'Acondicionamiento Físico | FamFit Quest')

@section('content')
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            primary: 'var(--fq-primary)',
                            bg: 'var(--fq-bg)',
                            surface: 'var(--fq-surface)',
                            'surface-muted': 'var(--fq-surface-muted)',
                            'text-primary': 'var(--fq-text)',
                            'text-secondary': 'var(--fq-muted)',
                            border: 'var(--fq-border)',
                            success: 'var(--fq-progress)',
                            habit: 'var(--fq-habit)',
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
    <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--fq-bg);
            color: var(--fq-text);
        }
        .card-transition {
    transition: transform 1.4s cubic-bezier(0.16, 1, 0.1, 1),
                box-shadow 1.4s cubic-bezier(0.16, 1, 0.1, 1),
                border-color 1.4s cubic-bezier(0.16, 1, 0.1, 1) !important;
    will-change: transform, box-shadow;
    position: relative;
    z-index: 1;
}
        .card-transition:hover { transform: translateY(-15px) !important; border-color: rgba(40, 100, 240, 0.5) !important; box-shadow: 0 24px 48px -8px rgba(40, 100, 240, 0.2), 0 24px 48px -6px rgba(40, 100, 240, 0.25) !important; z-index: 10; }
        .img-zoom { transition: transform 1.5s ease-in-out !important; }
        .card-transition:hover .img-zoom { transform: scale(1.1) !important; }
        body { overflow: hidden !important; }
    </style>

    <main class="w-full h-full flex flex-col justify-center font-sans text-brand-text-primary py-4 overflow-visible">
        <div class="text-center max-w-3xl mx-auto mb-8">
            <div class="inline-flex items-center space-x-2 bg-brand-surface-muted border border-brand-border px-3 py-1 rounded-full mb-3">
                <span class="w-2 h-2 rounded-full bg-brand-habit"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-text-secondary">Acondicionamiento Físico</span>
            </div>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-brand-text-primary tracking-tight leading-tight">
                Selecciona el entorno para tus ejercicios
            </h1>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-7xl mx-auto w-full px-4">
            
            <!-- EN CASA -->
            <div class="card-transition bg-brand-surface rounded-3xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm group">
                <div>
                    <div class="relative h-56 sm:h-64 w-full overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=800&q=80" class="img-zoom w-full h-full object-cover">
                        <div class="absolute inset-0 bg-linear-to-t from-brand-text-primary/80 via-transparent to-transparent opacity-90"></div>
                    </div>
                    <div class="p-8">
                        <h2 class="text-2xl font-extrabold mb-3 flex items-center justify-between group-hover:text-brand-primary transition-colors">
                            <span>En Casa</span>
                            <i class="fa-solid fa-house-chimney-user text-brand-text-secondary"></i>
                        </h2>
                        <p class="text-sm text-brand-text-secondary mb-4">Ejercita tu cuerpo sin salir de casa. Rutinas basadas en objetos a tu disposición.</p>
                    </div>
                </div>
                <!-- AHORA ES UN ENLACE A LA NUEVA PÁGINA -->
                <div class="p-8 pt-0">
                    <a href="{{ route('entrenamientos.rutinas', 'casa') }}" class="w-full py-3.5 px-4 bg-brand-primary hover:bg-brand-primary/90 !text-white font-bold text-base rounded-xl shadow-md flex items-center justify-center space-x-2">
                        <span>Ver Rutinas</span>
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- GIMNASIO -->
            <div class="card-transition bg-brand-surface rounded-3xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm group">
                <div>
                    <div class="relative h-56 sm:h-64 w-full overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80" class="img-zoom w-full h-full object-cover">
                        <div class="absolute inset-0 bg-linear-to-t from-brand-text-primary/80 via-transparent to-transparent opacity-90"></div>
                    </div>
                    <div class="p-8">
                        <h2 class="text-2xl font-extrabold mb-3 flex items-center justify-between group-hover:text-brand-primary transition-colors">
                            <span>Gimnasio</span>
                            <i class="fa-solid fa-dumbbell text-brand-text-secondary"></i>
                        </h2>
                        <p class="text-sm text-brand-text-secondary mb-4">Máquinas y mancuernas. Obtén una rutina famosa o personalizada.</p>
                    </div>
                </div>
                <div class="p-8 pt-0">
                    <a href="{{ route('entrenamientos.rutinas', 'gym') }}" class="w-full py-3.5 px-4 bg-brand-primary hover:bg-brand-primary/90 !text-white font-bold text-base rounded-xl shadow-md flex items-center justify-center space-x-2">
                        <span>Ver Rutinas</span>
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                </div>
            </div>

            <!-- AL AIRE LIBRE -->
            <div class="card-transition bg-brand-surface rounded-3xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm group">
                <div>
                    <div class="relative h-56 sm:h-64 w-full overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?auto=format&fit=crop&w=800&q=80" class="img-zoom w-full h-full object-cover">
                        <div class="absolute inset-0 bg-linear-to-t from-brand-text-primary/80 via-transparent to-transparent opacity-90"></div>
                    </div>
                    <div class="p-8">
                        <h2 class="text-2xl font-extrabold mb-3 flex items-center justify-between group-hover:text-brand-primary transition-colors">
                            <span>Al Aire Libre</span>
                            <i class="fa-solid fa-tree text-brand-text-secondary"></i>
                        </h2>
                        <p class="text-sm text-brand-text-secondary mb-4">Trotes, caminatas o sprints mediante geolocalización.</p>
                    </div>
                </div>
                <div class="p-8 pt-0">
                    <a href="{{ route('entrenamientos.rutinas', 'aire') }}" class="w-full py-3.5 px-4 bg-brand-primary hover:bg-brand-primary/90 !text-white font-bold text-base rounded-xl shadow-md flex items-center justify-center space-x-2">
                        <span>Ver Rutinas</span>
                        <i class="fa-solid fa-chevron-right text-sm"></i>
                    </a>
                </div>
            </div>

        </div>
    </main>
@endsection