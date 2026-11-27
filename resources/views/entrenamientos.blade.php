<!DOCTYPE html>
<html lang="es" class="h-full">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrenamientos | FamFit Quest</title>
    <script src="{{ asset('js/theme.js') }}"></script>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        brand: {
                            primary: 'var(--fq-primary)',
                            surface: 'var(--fq-surface)',
                            'surface-muted': 'var(--fq-surface-muted)',
                            'text-primary': 'var(--fq-text)',
                            'text-secondary': 'var(--fq-muted)',
                            border: 'var(--fq-border)',
                            habit: 'var(--fq-habit)',
                        },
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif']
                    },
                },
            },
        };
    </script>
    <style>
        body {
            min-height: 100vh;
            margin: 0;
            background: var(--fq-bg);
            color: var(--fq-text);
            font-family: Inter, sans-serif;
        }

        .training-card {
            transition: transform .25s ease, box-shadow .25s ease, border-color .25s ease;
        }

        .training-card:hover {
            transform: translateY(-5px);
            border-color: var(--fq-primary);
            box-shadow: 0 18px 38px rgba(40, 100, 240, .15);
        }

        .training-image {
            transition: transform .5s ease;
        }

        .training-card:hover .training-image {
            transform: scale(1.05);
        }
    </style>
</head>

<body>
    <main class="min-h-screen flex flex-col justify-center py-10">
        <header class="text-center max-w-3xl mx-auto mb-8 px-4">
            <span
                class="inline-flex items-center gap-2 bg-brand-surface-muted border border-brand-border px-3 py-1 rounded-full mb-3">
                <span class="w-2 h-2 rounded-full bg-brand-habit"></span>
                <span class="text-xs font-bold uppercase tracking-wider text-brand-text-secondary">Acondicionamiento
                    físico</span>
            </span>
            <h1 class="text-3xl sm:text-4xl font-extrabold text-brand-text-primary tracking-tight leading-tight">
                Selecciona el entorno para tus ejercicios
            </h1>
        </header>

        <section class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-7xl mx-auto w-full px-4"
            aria-label="Entornos de entrenamiento">
            <article
                class="training-card bg-brand-surface rounded-3xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm">
                <div>
                    <div class="relative h-56 sm:h-64 overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=800&q=80"
                            alt="Persona haciendo ejercicio en casa" class="training-image w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <h2
                            class="text-2xl font-extrabold mb-3 flex items-center justify-between text-brand-text-primary">
                            <span>En casa</span><i class="fa-solid fa-house-chimney-user text-brand-text-secondary"
                                aria-hidden="true"></i>
                        </h2>
                        <p class="text-sm text-brand-text-secondary">Ejercita tu cuerpo sin salir de casa con rutinas
                            basadas en los objetos a tu disposición.</p>
                    </div>
                </div>
                <div class="p-8 pt-0">
                    <a href="{{ route('entrenamientos.rutinas', 'casa') }}"
                        class="w-full py-3.5 px-4 bg-brand-primary hover:opacity-90 text-white font-bold text-base rounded-xl shadow-md flex items-center justify-center gap-2">
                        <span>Ver rutinas</span><i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </a>
                </div>
            </article>

            <article
                class="training-card bg-brand-surface rounded-3xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm">
                <div>
                    <div class="relative h-56 sm:h-64 overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=800&q=80"
                            alt="Equipamiento de gimnasio" class="training-image w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <h2
                            class="text-2xl font-extrabold mb-3 flex items-center justify-between text-brand-text-primary">
                            <span>Gimnasio</span><i class="fa-solid fa-dumbbell text-brand-text-secondary"
                                aria-hidden="true"></i>
                        </h2>
                        <p class="text-sm text-brand-text-secondary mb-4">Máquinas, mancuernas y equipo para rutinas
                            famosas o personalizadas.</p>
                        <div class="border-t border-brand-border pt-3">
                            <span
                                class="text-[11px] font-bold uppercase tracking-wider text-brand-primary block mb-2">Lo
                                que incluye</span>
                            <ul class="text-xs text-brand-text-secondary space-y-2">
                                <li><i class="fa-solid fa-circle-check text-brand-habit mr-2"
                                        aria-hidden="true"></i>Guía de técnica postural.</li>
                                <li><i class="fa-solid fa-circle-check text-brand-habit mr-2"
                                        aria-hidden="true"></i>Bitácora de cargas y repeticiones.</li>
                                <li><i class="fa-solid fa-circle-check text-brand-habit mr-2"
                                        aria-hidden="true"></i>Temporizador de descanso.</li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="p-8 pt-0">
                    <a href="{{ route('entrenamientos.rutinas', 'gym') }}"
                        class="w-full py-3.5 px-4 bg-brand-primary hover:opacity-90 text-white font-bold text-base rounded-xl shadow-md flex items-center justify-center gap-2">
                        <span>Ver rutinas</span><i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </a>
                </div>
            </article>

            <article
                class="training-card bg-brand-surface rounded-3xl border border-brand-border overflow-hidden flex flex-col justify-between shadow-sm">
                <div>
                    <div class="relative h-56 sm:h-64 overflow-hidden bg-brand-surface-muted">
                        <img src="https://images.unsplash.com/photo-1476480862126-209bfaa8edc8?auto=format&fit=crop&w=800&q=80"
                            alt="Persona corriendo al aire libre" class="training-image w-full h-full object-cover">
                    </div>
                    <div class="p-8">
                        <h2
                            class="text-2xl font-extrabold mb-3 flex items-center justify-between text-brand-text-primary">
                            <span>Al aire libre</span><i class="fa-solid fa-tree text-brand-text-secondary"
                                aria-hidden="true"></i>
                        </h2>
                        <p class="text-sm text-brand-text-secondary">Trotes, caminatas o sprints para disfrutar y
                            entrenar al aire libre.</p>
                    </div>
                </div>
                <div class="p-8 pt-0">
                    <a href="{{ route('entrenamientos.rutinas', 'aire') }}"
                        class="w-full py-3.5 px-4 bg-brand-primary hover:opacity-90 text-white font-bold text-base rounded-xl shadow-md flex items-center justify-center gap-2">
                        <span>Ver rutinas</span><i class="fa-solid fa-chevron-right text-sm" aria-hidden="true"></i>
                    </a>
                </div>
            </article>
        </section>
    </main>
</body>

</html>
