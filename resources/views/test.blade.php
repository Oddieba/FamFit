<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FamFit Quest - Test Inicial de Héroe</title>
    <!-- FontAwesome para Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            --primary-blue: #2864F0;
            --soft-bg: #F5F7FB;
            --accent-green: #A7E66D;
            --accent-teal: #19C6B0;
            --text-dark: #2B3674;
            --text-gray: #A3AED0;
            --white: #FFFFFF;
            --error-red: #FF4D4D;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background-color: var(--soft-bg);
            color: var(--text-dark);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .quest-wizard-card {
            background: var(--white);
            border-radius: 24px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
            width: 100%;
            max-width: 700px;
            padding: 32px;
            position: relative;
            overflow: hidden;
        }

        /* --- BARRA DE PROGRESO --- */
        .progress-header {
            margin-bottom: 28px;
        }

        .progress-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-weight: 700;
            margin-bottom: 10px;
            color: var(--primary-blue);
        }

        .progress-bar-bg {
            background-color: #E9ECEF;
            height: 10px;
            border-radius: 10px;
            overflow: hidden;
        }

        .progress-bar-fill {
            background: linear-gradient(90deg, var(--primary-blue), var(--accent-teal));
            height: 100%;
            width: 0%; 
            border-radius: 10px;
            transition: width 0.4s ease;
        }

        /* --- PASOS DEL CUESTIONARIO --- */
        .wizard-step {
            display: none;
            animation: fadeIn 0.4s ease-in-out;
        }

        .wizard-step.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .step-title {
            font-size: 1.35rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: var(--text-dark);
            text-align: center;
        }

        .step-subtitle {
            font-size: 0.9rem;
            color: var(--text-gray);
            margin-bottom: 24px;
            text-align: center;
        }

        .welcome-hero {
            text-align: center;
            padding: 10px 0;
        }

        .welcome-hero i {
            font-size: 3.5rem;
            color: var(--primary-blue);
            margin-bottom: 16px;
        }

        /* --- GRID Y TARJETAS DE OPCIONES --- */
        .options-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .options-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 20px;
        }

        .option-card {
            border: 2px solid #E9ECEF;
            border-radius: 16px;
            padding: 16px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--white);
        }

        .option-card i {
            font-size: 1.6rem;
            color: var(--primary-blue);
            margin-bottom: 8px;
            display: block;
        }

        .option-card span {
            font-weight: 600;
            font-size: 0.88rem;
        }

        .option-row {
            border: 2px solid #E9ECEF;
            border-radius: 14px;
            padding: 14px 18px;
            cursor: pointer;
            transition: all 0.2s ease;
            background: var(--white);
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }

        .option-row i {
            font-size: 1.2rem;
            color: var(--primary-blue);
            margin-top: 2px;
        }

        .option-row-content strong {
            display: block;
            font-size: 0.95rem;
            margin-bottom: 2px;
            color: var(--text-dark);
        }

        .option-row-content p {
            font-size: 0.82rem;
            color: var(--text-gray);
            margin: 0;
        }

        .option-card.selected, .option-row.selected {
            border-color: var(--primary-blue);
            background-color: #F0F5FF;
            box-shadow: 0 4px 12px rgba(40, 100, 240, 0.12);
        }

        /* --- FORMULARIOS E INPUTS --- */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
            gap: 16px;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 0.9rem;
        }

        .form-control {
            width: 100%;
            padding: 12px 16px;
            border-radius: 12px;
            border: 2px solid #E9ECEF;
            font-size: 0.95rem;
            outline: none;
            transition: border-color 0.2s, background-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
        }

        .form-control.is-invalid {
            border-color: var(--error-red) !important;
            background-color: #FFF5F5;
        }

        /* --- MATRIZ DE DISPONIBILIDAD BLOQUEABLE --- */
        .schedule-matrix {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 10px;
            max-height: 340px;
            overflow-y: auto;
            padding-right: 5px;
        }

        .schedule-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--soft-bg);
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid #E0E5F2;
        }

        .schedule-row.header-row {
            background: #E8EEFF;
            border-color: #D0E1FF;
        }

        .day-toggle-container {
            display: flex;
            align-items: center;
            gap: 10px;
            width: 140px;
        }

        .day-checkbox {
            width: 18px;
            height: 18px;
            accent-color: var(--primary-blue);
            cursor: pointer;
        }

        .day-name {
            font-weight: 700;
            font-size: 0.88rem;
            color: var(--text-dark);
            cursor: pointer;
        }

        .time-slots {
            display: flex;
            gap: 6px;
            opacity: 0.3;
            pointer-events: none;
            transition: opacity 0.3s ease;
        }

        .time-slots.enabled {
            opacity: 1;
            pointer-events: auto;
        }

        .slot-radio {
            display: none;
        }

        .slot-label {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #E0E5F2;
            background: white;
            font-size: 0.78rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .slot-radio:checked + .slot-label {
            background: var(--accent-green);
            color: #1A3E00;
            border-color: var(--accent-green);
        }

        .injury-details-container {
            display: none;
            margin-top: 12px;
            animation: fadeIn 0.3s ease;
        }

        /* --- BOTONES DE NAVEGACIÓN --- */
        .wizard-buttons {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 24px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 12px;
            font-weight: 700;
            border: none;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-prev {
            background: #E9ECEF;
            color: var(--text-dark);
        }

        .btn-next {
            background: var(--primary-blue);
            color: var(--white);
            box-shadow: 0 4px 12px rgba(40, 100, 240, 0.3);
        }

        .btn-next:hover {
            background: #1B52D8;
        }

        .reward-card {
            text-align: center;
            padding: 20px 0;
        }

        .reward-icon {
            font-size: 4rem;
            color: #FFC107;
            margin-bottom: 16px;
            animation: bounce 1s infinite alternate;
        }

        @keyframes bounce {
            from { transform: translateY(0); }
            to { transform: translateY(-10px); }
        }

        /* --- MODAL BONITO CON CSS --- */
        .custom-modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(43, 54, 116, 0.4);
            backdrop-filter: blur(4px);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }

        .custom-modal-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .custom-modal-box {
            background: var(--white);
            border-radius: 20px;
            width: 90%;
            max-width: 380px;
            padding: 28px;
            text-align: center;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            transform: scale(0.8);
            transition: transform 0.3s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }

        .custom-modal-overlay.open .custom-modal-box {
            transform: scale(1);
        }

        .modal-icon-container {
            width: 60px;
            height: 60px;
            background: #FFF4E5;
            color: #FF9800;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.6rem;
            margin: 0 auto 16px auto;
        }

        .modal-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 8px;
        }

        .modal-message {
            font-size: 0.9rem;
            color: var(--text-gray);
            margin-bottom: 24px;
            line-height: 1.4;
        }

        .btn-modal-action {
            background: var(--primary-blue);
            color: var(--white);
            border: none;
            width: 100%;
            padding: 12px;
            border-radius: 12px;
            font-weight: 700;
            cursor: pointer;
            transition: background 0.2s;
        }

        .btn-modal-action:hover {
            background: #1B52D8;
        }
    </style>
</head>
<body>

    <!-- MODAL BONITO PARA ALERTAS -->
    <div class="custom-modal-overlay" id="customModal">
        <div class="custom-modal-box">
            <div class="modal-icon-container">
                <i class="fa-solid fa-exclamation"></i>
            </div>
            <h3 class="modal-title" id="modalTitle">Campo incompleto</h3>
            <p class="modal-message" id="modalMessage">Por favor completa la información necesaria para continuar.</p>
            <button type="button" class="btn-modal-action" onclick="closeModal()">Entendido</button>
        </div>
    </div>

    <div class="quest-wizard-card">
        <!-- BARRA DE PROGRESO -->
        <div class="progress-header" id="progressHeader" style="display: none;">
            <div class="progress-info">
                <span>Configuración de Perfil</span>
                <span id="step-indicator">Paso 1 de 5</span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" id="progress-fill"></div>
            </div>
        </div>

        <form id="onboardingForm" action="{{ route('onboarding.guardar') }}" method="POST">
            <!-- Token de seguridad CSRF de Laravel -->
            @csrf

            <!-- PASO 0: BIENVENIDA -->
            <div class="wizard-step active" id="step-0">
                <div class="welcome-hero">
                    <i class="fa-solid fa-gamepad"></i>
                    <h2 class="step-title">¡Bienvenido a FamFit Quest!</h2>
                    <p class="step-subtitle">Configura tu perfil de aventurero para adaptar las misiones, objetivos y rutinas a tus necesidades personales y condición física actual.</p>
                </div>
            </div>

            <!-- PASO 1: DATOS BIOMÉTRICOS -->
            <div class="wizard-step" id="step-1">
                <h2 class="step-title">Tus Datos Físicos Base</h2>
                <p class="step-subtitle">Necesarios para realizar cálculos precisos de gasto calórico y nutrición.</p>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label" for="edad">Edad (años)</label>
                        <input type="number" id="edad" name="edad" class="form-control" placeholder="Ej: 24" required min="10" max="120" maxlength="3" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3); checkRealisticRange(this, 10, 120);">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="peso">Peso (kg)</label>
                        <input type="text" inputmode="decimal" id="peso" name="peso" class="form-control" placeholder="Ej: 68.5" required 
                               oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1'); checkRealisticRange(this, 30, 300);">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="altura">Altura (cm)</label>
                        <input type="number" id="altura" name="altura" class="form-control" placeholder="Ej: 172" required min="100" max="250" maxlength="3" 
                               oninput="this.value = this.value.replace(/[^0-9]/g, '').slice(0, 3); checkRealisticRange(this, 100, 250);">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Género:</label>
                    <input type="hidden" name="genero" id="input-genero" value="masculino">
                    <div class="options-grid">
                        <div class="option-card selected" onclick="selectSingle(this, 'input-genero', 'masculino')">
                            <i class="fa-solid fa-mars"></i>
                            <span>Masculino</span>
                        </div>
                        <div class="option-card" onclick="selectSingle(this, 'input-genero', 'femenino')">
                            <i class="fa-solid fa-venus"></i>
                            <span>Femenino</span>
                        </div>
                        <div class="option-card" onclick="selectSingle(this, 'input-genero', 'otro')">
                            <i class="fa-solid fa-genderless"></i>
                            <span>Otro</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 2: OBJETIVO PRINCIPAL -->
            <div class="wizard-step" id="step-2">
                <h2 class="step-title">¿Cuál es tu objetivo principal en este momento?</h2>
                <p class="step-subtitle">Esto orientará el tipo de recompensas y misiones principales que recibirás.</p>

                <input type="hidden" name="objetivo" id="input-objetivo" value="perder_grasa">
                <div class="options-list">
                    <div class="option-row selected" onclick="selectSingleRow(this, 'input-objetivo', 'perder_grasa')">
                        <i class="fa-solid fa-fire"></i>
                        <div class="option-row-content">
                            <strong>Perder grasa / Bajar de peso</strong>
                            <p>Enfoque en definición, quema calórica y déficit controlado.</p>
                        </div>
                    </div>
                    <div class="option-row" onclick="selectSingleRow(this, 'input-objetivo', 'ganar_musculo')">
                        <i class="fa-solid fa-dumbbell"></i>
                        <div class="option-row-content">
                            <strong>Ganar masa muscular / Fuerza</strong>
                            <p>Enfoque en hipertrofia, aumento de carga y resistencia muscular.</p>
                        </div>
                    </div>
                    <div class="option-row" onclick="selectSingleRow(this, 'input-objetivo', 'resistencia_cardio')">
                        <i class="fa-solid fa-heart-pulse"></i>
                        <div class="option-row-content">
                            <strong>Mejorar resistencia / Cardio</strong>
                            <p>Enfoque en capacidad pulmonar, agilidad y rendimiento cardiovascular.</p>
                        </div>
                    </div>
                    <div class="option-row" onclick="selectSingleRow(this, 'input-objetivo', 'activo_saludable')">
                        <i class="fa-solid fa-leaf"></i>
                        <div class="option-row-content">
                            <strong>Mantenerme activo y saludable</strong>
                            <p>Enfoque en bienestar general, constancia y hábitos diarios.</p>
                        </div>
                    </div>
                    <div class="option-row" onclick="selectSingleRow(this, 'input-objetivo', 'flexibilidad_movilidad')">
                        <i class="fa-solid fa-child-reaching"></i>
                        <div class="option-row-content">
                            <strong>Flexibilidad y movilidad</strong>
                            <p>Enfoque en estiramientos, postura y salud articular.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 3: EXPERIENCIA ACTUAL -->
            <div class="wizard-step" id="step-3">
                <h2 class="step-title">¿Cómo describirías tu experiencia actual haciendo ejercicio?</h2>
                <p class="step-subtitle">Calibraremos la dificultad inicial de tus desafíos y rutinas.</p>

                <input type="hidden" name="experiencia_ejercicio" id="input-experiencia" value="principiante">
                <div class="options-list">
                    <div class="option-row selected" onclick="selectSingleRow(this, 'input-experiencia', 'principiante')">
                        <i class="fa-solid fa-seedling"></i>
                        <div class="option-row-content">
                            <strong>Principiante</strong>
                            <p>Casi nunca hago ejercicio o llevo mucho tiempo inactivo.</p>
                        </div>
                    </div>
                    <div class="option-row" onclick="selectSingleRow(this, 'input-experiencia', 'intermedio')">
                        <i class="fa-solid fa-bolt"></i>
                        <div class="option-row-content">
                            <strong>Intermedio</strong>
                            <p>Hago ejercicio de forma ocasional o he retomado hace poco.</p>
                        </div>
                    </div>
                    <div class="option-row" onclick="selectSingleRow(this, 'input-experiencia', 'avanzado')">
                        <i class="fa-solid fa-trophy"></i>
                        <div class="option-row-content">
                            <strong>Avanzado</strong>
                            <p>Entreno de forma constante (mínimo 3 veces por semana) desde hace meses.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 4: DISPONIBILIDAD SEMANAL -->
            <div class="wizard-step" id="step-4">
                <h2 class="step-title">¿Qué días y en qué horario puedes entrenar?</h2>
                <p class="step-subtitle">Marca los días que entrenarás y elige su franja horaria correspondiente.</p>

                <div class="schedule-matrix">
                    <!-- Fila Maestra para Marcar / Desmarcar Todo -->
                    <div class="schedule-row header-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_all_days" class="day-checkbox" onchange="toggleAllDays(this)">
                            <label for="check_all_days" class="day-name" style="color: var(--primary-blue);">Marcar todos</label>
                        </div>
                        <span style="font-size: 0.8rem; color: var(--text-gray); font-weight: 600;">Selección rápida semanal</span>
                    </div>

                    <!-- Lunes -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_lunes" class="day-checkbox day-item" onchange="toggleDaySlots('lunes')">
                            <label for="check_lunes" class="day-name">Lunes</label>
                        </div>
                        <div class="time-slots" id="slots_lunes">
                            <input type="radio" id="lun_m" name="disponibilidad[lunes]" value="mañana" class="slot-radio"><label for="lun_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="lun_t" name="disponibilidad[lunes]" value="tarde" class="slot-radio"><label for="lun_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="lun_n" name="disponibilidad[lunes]" value="noche" class="slot-radio"><label for="lun_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Martes -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_martes" class="day-checkbox day-item" onchange="toggleDaySlots('martes')">
                            <label for="check_martes" class="day-name">Martes</label>
                        </div>
                        <div class="time-slots" id="slots_martes">
                            <input type="radio" id="mar_m" name="disponibilidad[martes]" value="mañana" class="slot-radio"><label for="mar_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="mar_t" name="disponibilidad[martes]" value="tarde" class="slot-radio"><label for="mar_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="mar_n" name="disponibilidad[martes]" value="noche" class="slot-radio"><label for="mar_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Miércoles -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_miercoles" class="day-checkbox day-item" onchange="toggleDaySlots('miercoles')">
                            <label for="check_miercoles" class="day-name">Miércoles</label>
                        </div>
                        <div class="time-slots" id="slots_miercoles">
                            <input type="radio" id="mie_m" name="disponibilidad[miercoles]" value="mañana" class="slot-radio"><label for="mie_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="mie_t" name="disponibilidad[miercoles]" value="tarde" class="slot-radio"><label for="mie_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="mie_n" name="disponibilidad[miercoles]" value="noche" class="slot-radio"><label for="mie_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Jueves -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_jueves" class="day-checkbox day-item" onchange="toggleDaySlots('jueves')">
                            <label for="check_jueves" class="day-name">Jueves</label>
                        </div>
                        <div class="time-slots" id="slots_jueves">
                            <input type="radio" id="jue_m" name="disponibilidad[jueves]" value="mañana" class="slot-radio"><label for="jue_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="jue_t" name="disponibilidad[jueves]" value="tarde" class="slot-radio"><label for="jue_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="jue_n" name="disponibilidad[jueves]" value="noche" class="slot-radio"><label for="jue_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Viernes -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_viernes" class="day-checkbox day-item" onchange="toggleDaySlots('viernes')">
                            <label for="check_viernes" class="day-name">Viernes</label>
                        </div>
                        <div class="time-slots" id="slots_viernes">
                            <input type="radio" id="vie_m" name="disponibilidad[viernes]" value="mañana" class="slot-radio"><label for="vie_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="vie_t" name="disponibilidad[viernes]" value="tarde" class="slot-radio"><label for="vie_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="vie_n" name="disponibilidad[viernes]" value="noche" class="slot-radio"><label for="vie_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Sábado -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_sabado" class="day-checkbox day-item" onchange="toggleDaySlots('sabado')">
                            <label for="check_sabado" class="day-name">Sábado</label>
                        </div>
                        <div class="time-slots" id="slots_sabado">
                            <input type="radio" id="sab_m" name="disponibilidad[sabado]" value="mañana" class="slot-radio"><label for="sab_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="sab_t" name="disponibilidad[sabado]" value="tarde" class="slot-radio"><label for="sab_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="sab_n" name="disponibilidad[sabado]" value="noche" class="slot-radio"><label for="sab_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Domingo -->
                    <div class="schedule-row">
                        <div class="day-toggle-container">
                            <input type="checkbox" id="check_domingo" class="day-checkbox day-item" onchange="toggleDaySlots('domingo')">
                            <label for="check_domingo" class="day-name">Domingo</label>
                        </div>
                        <div class="time-slots" id="slots_domingo">
                            <input type="radio" id="dom_m" name="disponibilidad[domingo]" value="mañana" class="slot-radio"><label for="dom_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="dom_t" name="disponibilidad[domingo]" value="tarde" class="slot-radio"><label for="dom_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="dom_n" name="disponibilidad[domingo]" value="noche" class="slot-radio"><label for="dom_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 5: CONDICIÓN MÉDICA / LESIONES -->
            <div class="wizard-step" id="step-5">
                <h2 class="step-title">¿Tienes alguna lesión, molestia o condición médica que debamos tener en cuenta?</h2>
                <p class="step-subtitle">Tu seguridad es nuestra prioridad al sugerir ejercicios y adaptaciones.</p>

                <input type="hidden" name="tiene_lesion" id="input-lesion" value="no">
                <div class="options-grid" style="grid-template-columns: 1fr 1fr; margin-bottom: 15px;">
                    <div class="option-card selected" id="card-lesion-no" onclick="toggleLesion('no')">
                        <i class="fa-solid fa-circle-check"></i>
                        <span>No</span>
                    </div>
                    <div class="option-card" id="card-lesion-si" onclick="toggleLesion('si')">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                        <span>Sí</span>
                    </div>
                </div>

                <div class="injury-details-container" id="injuryDetails">
                    <div class="form-group">
                        <label class="form-label" for="descripcion_lesion">Especifica brevemente la lesión o condición:</label>
                        <input type="text" id="descripcion_lesion" name="descripcion_lesion" class="form-control" placeholder="Ej: Molestia leve en la rodilla derecha, dolor lumbar ocasional...">
                    </div>
                </div>
            </div>

            <!-- PASO 6: RECOMPENSA FINAL -->
            <div class="wizard-step" id="step-6">
                <div class="reward-card">
                    <i class="fa-solid fa-gem reward-icon"></i>
                    <h2 class="step-title">¡Misión de Inicio Cumplida!</h2>
                    <p class="step-subtitle">Tus datos biométricos, objetivos y disponibilidad se han configurado con éxito. Has ganado **+100 XP** iniciales en FamFit Quest.</p>
                </div>
            </div>

            <!-- BOTONES DE NAVEGACIÓN -->
            <div class="wizard-buttons">
                <button type="button" class="btn btn-prev" id="btnPrev" onclick="changeStep(-1)" style="display: none;">Anterior</button>
                <button type="button" class="btn btn-next" id="btnNext" onclick="handleNextStep(event)">Comenzar Test</button>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 0;
        const totalSteps = 6;

        function handleNextStep(event) {
            if (currentStep === totalSteps) {
                return true;
            }
            
            if (!validateCurrentStep(currentStep)) {
                return; 
            }

            changeStep(1);
        }

        function changeStep(direction) {
            if (direction > 0) {
                if (!validateCurrentStep(currentStep)) {
                    return; // Detiene el avance si falta completar algún campo obligatorio
                }
            }

            document.getElementById(`step-${currentStep}`).classList.remove('active');
            currentStep += direction;
            document.getElementById(`step-${currentStep}`).classList.add('active');

            const progressHeader = document.getElementById('progressHeader');
            if (currentStep === 0) {
                progressHeader.style.display = 'none';
            } else {
                progressHeader.style.display = 'block';
                document.getElementById('step-indicator').innerText = `Paso ${currentStep} de ${totalSteps - 1}`;
                document.getElementById('progress-fill').style.width = `${(currentStep / (totalSteps - 1)) * 100}%`;
            }

            const btnPrev = document.getElementById('btnPrev');
            const btnNext = document.getElementById('btnNext');

            btnPrev.style.display = (currentStep === 0) ? 'none' : 'block';

            if (currentStep === 0) {
                btnNext.innerText = 'Comenzar Test';
                btnNext.style.backgroundColor = 'var(--primary-blue)';
            } else if (currentStep === totalSteps) {
                btnNext.innerText = 'Guardar y Ver Panel';
                btnNext.style.backgroundColor = 'var(--accent-teal)';
                btnNext.type = 'submit'; 
            } else {
                btnNext.innerText = 'Siguiente';
                btnNext.style.backgroundColor = 'var(--primary-blue)';
                btnNext.type = 'button';
            }
        }

        function showCustomModal(message) {
            document.getElementById('modalMessage').innerText = message;
            document.getElementById('customModal').classList.add('open');
        }

        function closeModal() {
            document.getElementById('customModal').classList.remove('open');
        }

        function checkRealisticRange(input, min, max) {
            const val = parseFloat(input.value);
            if (input.value !== '' && (isNaN(val) || val < min || val > max)) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        }

        // Lógica optimizada y corregida para Marcar / Desmarcar todos los días
        function toggleAllDays(masterCheckbox) {
            const days = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
            const isChecked = masterCheckbox.checked;

            days.forEach(day => {
                const checkbox = document.getElementById(`check_${day}`);
                const slotsContainer = document.getElementById(`slots_${day}`);
                
                if (checkbox) {
                    checkbox.checked = isChecked;
                }

                if (slotsContainer) {
                    const radios = slotsContainer.querySelectorAll('input[type="radio"]');
                    if (isChecked) {
                        slotsContainer.classList.add('enabled');
                        let anyChecked = Array.from(radios).some(r => r.checked);
                        if (!anyChecked && radios.length > 0) {
                            radios[0].checked = true; // Selecciona "Mañana" por defecto
                        }
                    } else {
                        slotsContainer.classList.remove('enabled');
                        radios.forEach(r => r.checked = false);
                    }
                }
            });
        }

        function toggleDaySlots(day) {
            const checkbox = document.getElementById(`check_${day}`);
            const slotsContainer = document.getElementById(`slots_${day}`);
            
            if (checkbox && slotsContainer) {
                const radios = slotsContainer.querySelectorAll('input[type="radio"]');
                if (checkbox.checked) {
                    slotsContainer.classList.add('enabled');
                    let anyChecked = Array.from(radios).some(r => r.checked);
                    if (!anyChecked && radios.length > 0) {
                        radios[0].checked = true;
                    }
                } else {
                    slotsContainer.classList.remove('enabled');
                    radios.forEach(r => r.checked = false);
                }
            }

            updateMasterCheckboxState();
        }

        function updateMasterCheckboxState() {
            const days = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
            const masterCheckbox = document.getElementById('check_all_days');
            
            if (masterCheckbox) {
                const allChecked = days.every(day => {
                    const cb = document.getElementById(`check_${day}`);
                    return cb && cb.checked;
                });
                masterCheckbox.checked = allChecked;
            }
        }

        function validateCurrentStep(step) {
            if (step === 1) {
                const edad = document.getElementById('edad');
                const peso = document.getElementById('peso');
                const altura = document.getElementById('altura');

                if (!edad.value || !peso.value || !altura.value) {
                    showCustomModal('Por favor, completa todos los campos de tus datos físicos base.');
                    return false;
                }

                const valEdad = parseFloat(edad.value);
                const valPeso = parseFloat(peso.value);
                const valAltura = parseFloat(altura.value);

                if (valEdad < 10 || valEdad > 120) {
                    edad.classList.add('is-invalid');
                    showCustomModal('Por favor, ingresa una edad real (entre 10 y 120 años).');
                    edad.focus();
                    return false;
                }

                if (valPeso < 30 || valPeso > 300) {
                    peso.classList.add('is-invalid');
                    showCustomModal('Por favor, ingresa un peso real (entre 30 kg y 300 kg).');
                    peso.focus();
                    return false;
                }

                if (valAltura < 100 || valAltura > 250) {
                    altura.classList.add('is-invalid');
                    showCustomModal('Por favor, ingresa una altura real (entre 100 cm y 250 cm).');
                    altura.focus();
                    return false;
                }
            } 
            else if (step === 4) {
                const days = ['lunes', 'martes', 'miercoles', 'jueves', 'viernes', 'sabado', 'domingo'];
                let selectedDaysCount = 0;

                for (let day of days) {
                    const checkbox = document.getElementById(`check_${day}`);
                    if (checkbox && checkbox.checked) {
                        selectedDaysCount++;
                        const timeSelected = document.querySelector(`input[name="disponibilidad[${day}]"]:checked`);
                        if (!timeSelected) {
                            showCustomModal(`Selecciona el turno (mañana, tarde o noche) para el día ${day.charAt(0).toUpperCase() + day.slice(1)}.`);
                            return false;
                        }
                    }
                }

                if (selectedDaysCount === 0) {
                    showCustomModal('Debes seleccionar al menos un día de la semana para entrenar.');
                    return false;
                }
            } 
            else if (step === 5) {
                const lesion = document.getElementById('input-lesion').value;
                const descLesion = document.getElementById('descripcion_lesion').value.trim();
                if (lesion === 'si' && descLesion === '') {
                    showCustomModal('Por favor, describe brevemente tu lesión o condición médica.');
                    document.getElementById('descripcion_lesion').focus();
                    return false;
                }
            }

            return true;
        }

        function selectSingle(element, hiddenInputId, value) {
            const parent = element.parentElement;
            const cards = parent.querySelectorAll('.option-card');
            cards.forEach(card => card.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById(hiddenInputId).value = value;
        }

        function selectSingleRow(element, hiddenInputId, value) {
            const parent = element.parentElement;
            const rows = parent.querySelectorAll('.option-row');
            rows.forEach(row => row.classList.remove('selected'));
            element.classList.add('selected');
            document.getElementById(hiddenInputId).value = value;
        }

        function toggleLesion(option) {
            const cardNo = document.getElementById('card-lesion-no');
            const cardSi = document.getElementById('card-lesion-si');
            const inputLesion = document.getElementById('input-lesion');
            const container = document.getElementById('injuryDetails');

            if (option === 'si') {
                cardSi.classList.add('selected');
                cardNo.classList.remove('selected');
                inputLesion.value = 'si';
                container.style.display = 'block';
            } else {
                cardNo.classList.add('selected');
                cardSi.classList.remove('selected');
                inputLesion.value = 'no';
                container.style.display = 'none';
                document.getElementById('descripcion_lesion').value = '';
            }
        }
    </script>
</body>
</html>