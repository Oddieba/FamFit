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
            transition: border-color 0.2s;
        }

        .form-control:focus {
            border-color: var(--primary-blue);
        }

        /* --- MATRIZ DE DISPONIBILIDAD (SELECCIÓN ÚNICA CON RADIOS) --- */
        .schedule-matrix {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 10px;
        }

        .schedule-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--soft-bg);
            padding: 10px 14px;
            border-radius: 12px;
        }

        .day-name {
            font-weight: 700;
            width: 75px;
            font-size: 0.88rem;
        }

        .time-slots {
            display: flex;
            gap: 6px;
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
    </style>
</head>
<body>

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
                        <input type="number" id="edad" name="edad" class="form-control" placeholder="Ej: 24" required min="10" max="100">
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="peso">Peso (kg)</label>
                        <input type="number" step="0.1" id="peso" name="peso" class="form-control" placeholder="Ej: 68.5" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="altura">Altura (cm)</label>
                        <input type="number" id="altura" name="altura" class="form-control" placeholder="Ej: 172" required>
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
                    <div class="option-row" onclick="selectSingleRow(this, 'input-experiencia', 'principiante')">
                        <i class="fa-solid fa-circle-question"></i>
                        <div class="option-row-content">
                            <strong>No estoy seguro</strong>
                            <p>El sistema asignará el nivel Principiante por defecto para empezar seguros.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PASO 4: DISPONIBILIDAD SEMANAL (ÚNICA SELECCIÓN POR DÍA) -->
            <div class="wizard-step" id="step-4">
                <h2 class="step-title">¿Qué disponibilidad tienes para entrenar?</h2>
                <p class="step-subtitle">Selecciona la franja horaria principal para cada día de la semana.</p>

                <div class="schedule-matrix">
                    <!-- Lunes -->
                    <div class="schedule-row">
                        <span class="day-name">Lunes</span>
                        <div class="time-slots">
                            <input type="radio" id="lun_m" name="disponibilidad[lunes]" value="mañana" class="slot-radio" checked><label for="lun_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="lun_t" name="disponibilidad[lunes]" value="tarde" class="slot-radio"><label for="lun_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="lun_n" name="disponibilidad[lunes]" value="noche" class="slot-radio"><label for="lun_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Martes -->
                    <div class="schedule-row">
                        <span class="day-name">Martes</span>
                        <div class="time-slots">
                            <input type="radio" id="mar_m" name="disponibilidad[martes]" value="mañana" class="slot-radio"><label for="mar_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="mar_t" name="disponibilidad[martes]" value="tarde" class="slot-radio" checked><label for="mar_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="mar_n" name="disponibilidad[martes]" value="noche" class="slot-radio"><label for="mar_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Miércoles -->
                    <div class="schedule-row">
                        <span class="day-name">Miércoles</span>
                        <div class="time-slots">
                            <input type="radio" id="mie_m" name="disponibilidad[miercoles]" value="mañana" class="slot-radio" checked><label for="mie_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="mie_t" name="disponibilidad[miercoles]" value="tarde" class="slot-radio"><label for="mie_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="mie_n" name="disponibilidad[miercoles]" value="noche" class="slot-radio"><label for="mie_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Jueves -->
                    <div class="schedule-row">
                        <span class="day-name">Jueves</span>
                        <div class="time-slots">
                            <input type="radio" id="jue_m" name="disponibilidad[jueves]" value="mañana" class="slot-radio"><label for="jue_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="jue_t" name="disponibilidad[jueves]" value="tarde" class="slot-radio" checked><label for="jue_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="jue_n" name="disponibilidad[jueves]" value="noche" class="slot-radio"><label for="jue_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Viernes -->
                    <div class="schedule-row">
                        <span class="day-name">Viernes</span>
                        <div class="time-slots">
                            <input type="radio" id="vie_m" name="disponibilidad[viernes]" value="mañana" class="slot-radio" checked><label for="vie_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="vie_t" name="disponibilidad[viernes]" value="tarde" class="slot-radio"><label for="vie_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="vie_n" name="disponibilidad[viernes]" value="noche" class="slot-radio"><label for="vie_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Sábado -->
                    <div class="schedule-row">
                        <span class="day-name">Sábado</span>
                        <div class="time-slots">
                            <input type="radio" id="sab_m" name="disponibilidad[sabado]" value="mañana" class="slot-radio"><label for="sab_m" class="slot-label"><i class="fa-solid fa-sun"></i> Mañana</label>
                            <input type="radio" id="sab_t" name="disponibilidad[sabado]" value="tarde" class="slot-radio"><label for="sab_t" class="slot-label"><i class="fa-solid fa-cloud-sun"></i> Tarde</label>
                            <input type="radio" id="sab_n" name="disponibilidad[sabado]" value="noche" class="slot-radio"><label for="sab_n" class="slot-label"><i class="fa-solid fa-moon"></i> Noche</label>
                        </div>
                    </div>
                    <!-- Domingo -->
                    <div class="schedule-row">
                        <span class="day-name">Domingo</span>
                        <div class="time-slots">
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
                <button type="button" class="btn btn-next" id="btnNext" onclick="changeStep(1)">Comenzar Test</button>
            </div>
        </form>
    </div>

    <script>
        let currentStep = 0;
        const totalSteps = 6;

        function changeStep(direction) {
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