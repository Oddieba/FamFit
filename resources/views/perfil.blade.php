@extends('layouts.quest')

@section('title', 'Perfil | FamFit Quest')

@section('content')


    <header class="fq-page-heading"><div><p class="fq-eyebrow">TU CUENTA</p><h1>Perfil</h1><p class="fq-muted">Administra tus datos y consulta tu progreso.</p></div></header>
    <section class="fq-profile-overview">
        <article class="fq-card fq-profile-card">
            <div class="fq-avatar-art fq-profile-avatar" style="--fq-outfit: {{ $avatar['outfit'] }}"><span class="fq-avatar-character">{{ $avatar['animal'] }}</span>@if($avatar['accessory_symbol'])<span class="fq-avatar-accessory">{{ $avatar['accessory_symbol'] }}</span>@endif</div>
            <div><p class="fq-eyebrow">NIVEL {{ $level }}</p><h2>{{ $user->name }}</h2><p class="fq-muted">{{ $user->username ? '@'.$user->username : $user->email }}</p><a class="fq-text-link" href="{{ route('menu') }}">Personalizar avatar →</a></div>
        </article>
        <article class="fq-card fq-stat-card"><span class="fq-card-icon fq-icon-green" aria-hidden="true">✦</span><strong>{{ $user->xp ?? 0 }}</strong><span class="fq-muted">XP acumulados</span><div class="fq-progress-track" role="progressbar" aria-label="Progreso de nivel" aria-valuenow="{{ $xpProgress }}" aria-valuemin="0" aria-valuemax="100"><span style="width: {{ $xpProgress }}%"></span></div><small class="fq-muted">{{ $xpProgress }} / 100 XP al siguiente nivel</small></article>
        <article class="fq-card fq-stat-card"><span class="fq-card-icon" aria-hidden="true">✓</span><strong>{{ $completedCount }}</strong><span class="fq-muted">Ejercicios completados</span><small class="fq-muted">{{ $routine ? 'Rutina: '.$routine->name : 'Aún no tienes una rutina' }}</small></article>
    </section>
    <section class="fq-card fq-profile-form-card">
        <div class="fq-card-heading"><div><p class="fq-eyebrow">INFORMACIÓN PERSONAL</p><h2>Datos de la cuenta</h2></div><span class="fq-card-icon" aria-hidden="true">👤</span></div>
        <form action="{{ route('perfil.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="fq-form-grid">
                <div><label class="fq-field-label" for="profileName">Nombre</label><input class="fq-input" id="profileName" name="name" value="{{ old('name', $user->name) }}" maxlength="255" autocomplete="name" required></div>
                <div><label class="fq-field-label" for="profileUsername">Nombre de usuario</label><input class="fq-input" id="profileUsername" name="username" value="{{ old('username', $user->username) }}" minlength="3" maxlength="20" pattern="[A-Za-z0-9_]+" autocomplete="username" required></div>
                <div class="fq-form-wide"><label class="fq-field-label" for="profileEmail">Correo electrónico</label><input class="fq-input" id="profileEmail" type="email" name="email" value="{{ old('email', $user->email) }}" maxlength="190" autocomplete="email" required></div>
            </div>
                                <!-- Contenedor flex para separar los botones -->
                              <!-- Estilos específicos para este botón -->
                <style>
                    .btn-repetir-test {
                        transition: all 0.2s ease-in-out;
                    }
                    .btn-repetir-test:hover {
                        background-color: #cd7b1e !important; /* Amarillo más oscuro */
                        transform: translateY(-2px); /* Se mueve 3px hacia arriba */
                        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* Sombra para dar efecto de elevación */
                    }

                    .custom-modal-overlay {
                    position: fixed;
                    top: 0; left: 0; width: 100%; height: 100%;
                    background: rgba(43, 54, 116, 0.4);
                    backdrop-filter: blur(4px);
                    display: flex; justify-content: center; align-items: center;
                    z-index: 9999; opacity: 0; visibility: hidden;
                    transition: all 0.3s ease;
                }
                .custom-modal-overlay.open { opacity: 1; visibility: visible; }
                .custom-modal-box {
                    background: white; border-radius: 20px; width: 90%; max-width: 400px;
                    padding: 28px; text-align: center; box-shadow: 0 15px 35px rgba(0,0,0,0.1);
                    transform: scale(0.8); transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
                }
                .custom-modal-overlay.open .custom-modal-box { transform: scale(1); }
                .modal-icon-container {
                    width: 60px; height: 60px; background: #FFF4E5; color: #FF9800;
                    border-radius: 50%; display: flex; align-items: center; justify-content: center;
                    font-size: 1.6rem; margin: 0 auto 16px auto;
                }
                .btn-modal-action {
                    background: var(--primary-blue, #2864F0); color: white; border: none; width: 100%;
                    padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: background 0.2s;
                }
                .btn-modal-action:hover { background: #1B52D8; }
                .btn-modal-cancel {
                    background: #E5E7EB; color: #374151; border: none; width: 100%;
                    padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; transition: background 0.2s;
                }
                .btn-modal-cancel:hover { background: #D1D5DB; }
                </style>

                <!-- Contenedor flex para separar los botones -->
                <div style="display: flex; justify-content: space-between; align-items: center; width: 100%; margin-top: 1.5rem;">
                    
                    <!-- Botón Guardar (Izquierda) -->
                    <button class="fq-button fq-button-primary" type="submit">
                        Guardar perfil
                    </button>

                                  <!-- Botón Test (Derecha) -->
                <a href="#" onclick="openRetakeModal(event)" 
                   class="btn-repetir-test"
                   style="display: inline-flex; align-items: center; justify-content: center; padding: 10px 20px; background-color: #FACC15; color: #1F2937; font-weight: 600; border-radius: 8px; text-decoration: none;">
                    
                    <svg style="width: 20px; height: 20px; margin-right: 8px;" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                    </svg>
                    
                    Repetir Test
                </a>
                    
                </div>
        </form>
    </section>
    <!-- MODAL DE CONFIRMACIÓN PARA REPETIR TEST -->
    <div class="custom-modal-overlay" id="retakeModal">
        <div class="custom-modal-box">
            
            <!-- Ícono -->
            <div class="modal-icon-container">
                <i class="fa-solid fa-clipboard-question" style="font-size: 1.6rem;"></i>
            </div>
            
            <h3 class="modal-title" style="font-size: 1.2rem; font-weight: 700; color: #2B3674; margin-bottom: 8px;">
                ¿Volver a tomar el test?
            </h3>
            
            <p class="modal-message" style="font-size: 0.9rem; color: #A3AED0; margin-bottom: 24px; line-height: 1.4;">
                ¿Seguro que quieres volver a tomar tus datos del test inicial? Esto actualizará tu perfil actual y tu historial.
            </p>
            
            <div style="display: flex; gap: 10px; width: 100%;">
                <!-- Botón Cancelar (Forzando colores) -->
                <button type="button" 
                        onclick="closeRetakeModal()" 
                        style="flex: 1; background-color: #E5E7EB !important; color: #374151 !important; border: none; padding: 12px; border-radius: 12px; font-weight: 700; cursor: pointer; font-size: 1rem; transition: background 0.2s;">
                    Cancelar
                </button>
                
                <!-- Botón Continuar (Forzando colores) -->
                <a href="{{ route('onboarding', ['retake' => 1]) }}" 
                   style="flex: 1; background-color: #2864F0 !important; color: white !important; text-decoration: none; display: flex; justify-content: center; align-items: center; padding: 12px; border-radius: 12px; font-weight: 700; font-size: 1rem; transition: background 0.2s;">
                    Sí, continuar
                </a>
            </div>
        </div>
    </div>

    <script>
        function openRetakeModal(event) {
            event.preventDefault(); // Evita que el enlace recargue la página
            document.getElementById('retakeModal').classList.add('open');
        }

        function closeRetakeModal() {
            document.getElementById('retakeModal').classList.remove('open');
        }
    </script>
@endsection
