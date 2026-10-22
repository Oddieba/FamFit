<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Crear cuenta · Famfit</title>

  <!-- Tipografía principal: Inter (si no carga, se usa Arial o sans-serif) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <style>
    /* ==========================================================
       1. TOKENS DEL SISTEMA DE DISEÑO (sección 8 del documento)
       ========================================================== */
    :root {
      /* Colores oficiales */
      --primary: #2864F0;
      --background: #F5F7FB;
      --surface: #FFFFFF;
      --surface-muted: #EEF2F8;
      --text-primary: #172033;
      --text-secondary: #64748B;
      --border: #DCE4EF;
      --success: #A7E66D;
      --habit: #19C6B0;
      --warning: #F2B85B;
      --danger: #E45865;

      /* Escala de espaciado: 4, 8, 12, 16, 24, 32 y 48 px */
      --sp-1: 4px;
      --sp-2: 8px;
      --sp-3: 12px;
      --sp-4: 16px;
      --sp-6: 24px;
      --sp-8: 32px;
      --sp-12: 48px;

      /* Bordes redondeados */
      --radio-campo: 12px;
      --radio-tarjeta: 16px;
    }

    /* ==========================================================
       2. ESTILOS BASE
       ========================================================== */
    * { box-sizing: border-box; }

    body {
      margin: 0;
      font-family: "Inter", Arial, sans-serif;
      font-size: 16px;
      line-height: 1.5;
      color: var(--text-primary);
      background: var(--background);
    }

    /* Para que el atributo "hidden" siempre oculte el elemento */
    [hidden] { display: none !important; }

    /* Texto solo para lectores de pantalla */
    .solo-lectores {
      position: absolute;
      width: 1px;
      height: 1px;
      overflow: hidden;
      clip: rect(0, 0, 0, 0);
      white-space: nowrap;
    }

    /* Iconos: una sola familia (línea, trazo 2) */
    .icono {
      width: 20px;
      height: 20px;
      fill: none;
      stroke: currentColor;
      stroke-width: 2;
      stroke-linecap: round;
      stroke-linejoin: round;
      flex-shrink: 0;
    }

    a { color: var(--primary); text-decoration: underline; }
    a:hover { text-decoration: none; }

    /* Foco visible para navegar con teclado */
    a:focus-visible,
    button:focus-visible,
    input:focus-visible {
      outline: 3px solid var(--primary);
      outline-offset: 2px;
    }

    /* ==========================================================
       3. DISEÑO GENERAL (mobile-first)
       ========================================================== */
    .registro {
      min-height: 100vh;
      display: grid;
      grid-template-columns: 1fr;
    }

    .registro__lado-form {
      display: flex;
      justify-content: center;
      align-items: flex-start;
      padding: var(--sp-6) var(--sp-4);
    }

    /* Panel derecho: oculto en celular, visible desde 900px */
    .registro__panel {
      display: none;
      flex-direction: column;
      justify-content: center;
      gap: var(--sp-8);
      padding: var(--sp-12);
      color: #FFFFFF;
      background: linear-gradient(160deg, var(--primary) 0%, var(--text-primary) 100%);
      /* Para usar tu imagen de fondo, cambia la línea de arriba por:
         background: linear-gradient(160deg, rgba(40,100,240,0.85), rgba(23,32,51,0.9)),
                     url("img/pesas.jpg") center / cover no-repeat; */
    }

    @media (min-width: 900px) {
      .registro { grid-template-columns: 1fr 1fr; }
      .registro__lado-form { align-items: center; padding: var(--sp-12) var(--sp-8); }
      .registro__panel { display: flex; }
    }

    /* ==========================================================
       4. TARJETA DEL FORMULARIO
       ========================================================== */
    .tarjeta {
      width: 100%;
      max-width: 480px;
      padding: var(--sp-6);
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radio-tarjeta);
      box-shadow: 0 4px 16px rgba(23, 32, 51, 0.06);
    }

    @media (min-width: 600px) {
      .tarjeta { padding: var(--sp-8); }
    }

    .logo {
      display: flex;
      align-items: center;
      gap: var(--sp-2);
      margin-bottom: var(--sp-6);
      font-size: 20px;
      font-weight: 700;
    }

    .logo__icono {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 36px;
      height: 36px;
      color: #FFFFFF;
      background: var(--primary);
      border-radius: var(--radio-campo);
    }

    .tarjeta__titulo {
      margin: 0 0 var(--sp-2);
      font-size: 28px;
      line-height: 1.2;
      font-weight: 700;
    }

    .tarjeta__subtitulo {
      margin: 0 0 var(--sp-6);
      font-size: 14px;
      color: var(--text-secondary);
    }

    /* ==========================================================
       5. CAMPOS DEL FORMULARIO
       ========================================================== */
    .campo { margin-bottom: var(--sp-4); }

    .campo__etiqueta {
      display: block;
      margin-bottom: var(--sp-1);
      font-size: 14px;
      font-weight: 500;
    }

    .campo__control { position: relative; }

    .campo__input {
      width: 100%;
      min-height: 44px;
      padding: var(--sp-2) var(--sp-3);
      font-family: inherit;
      font-size: 16px;
      color: var(--text-primary);
      background: var(--surface);
      border: 1px solid var(--border);
      border-radius: var(--radio-campo);
    }

    .campo__input::placeholder { color: var(--text-secondary); }

    .campo__input:focus {
      border-color: var(--primary);
      outline: none;
      box-shadow: 0 0 0 3px rgba(40, 100, 240, 0.25);
    }

    /* Campo con error: borde rojo + icono + mensaje (no solo color) */
    .campo__input.es-invalido { border-color: var(--danger); }

    /* Espacio a la derecha para el botón de ojo */
    .campo__input--con-boton { padding-right: 48px; }

    .campo__ayuda {
      margin: var(--sp-1) 0 0;
      font-size: 13px;
      color: var(--text-secondary);
    }

    .campo__error {
      display: flex;
      align-items: flex-start;
      gap: var(--sp-2);
      margin: var(--sp-1) 0 0;
      font-size: 13px;
      font-weight: 500;
      color: var(--text-primary);
    }

    .campo__error .icono {
      width: 16px;
      height: 16px;
      margin-top: 2px;
      color: var(--danger);
    }

    /* Botón para mostrar/ocultar contraseña */
    .ver-clave {
      position: absolute;
      top: 0;
      right: 0;
      display: flex;
      align-items: center;
      justify-content: center;
      width: 44px;
      height: 44px;
      color: var(--text-secondary);
      background: transparent;
      border: 0;
      border-radius: var(--radio-campo);
      cursor: pointer;
    }

    .ver-clave .icono-ocultar { display: none; }
    .ver-clave.mostrando .icono-ver { display: none; }
    .ver-clave.mostrando .icono-ocultar { display: block; }

    /* ==========================================================
       6. LISTA DE REQUISITOS DE LA CONTRASEÑA
       ========================================================== */
    .reglas {
      margin: var(--sp-2) 0 0;
      padding: var(--sp-3);
      list-style: none;
      font-size: 13px;
      color: var(--text-secondary);
      background: var(--surface-muted);
      border-radius: var(--radio-campo);
    }

    .reglas li {
      display: flex;
      align-items: center;
      gap: var(--sp-2);
      padding: var(--sp-1) 0;
    }

    /* Círculo vacío = pendiente */
    .regla__marca {
      position: relative;
      width: 16px;
      height: 16px;
      border: 2px solid var(--text-secondary);
      border-radius: 50%;
      flex-shrink: 0;
    }

    /* Círculo verde lima con check = cumplida */
    .reglas li.cumple { color: var(--text-primary); }

    .reglas li.cumple .regla__marca {
      background: var(--success);
      border-color: var(--success);
    }

    .reglas li.cumple .regla__marca::after {
      content: "";
      position: absolute;
      left: 3px;
      top: 0;
      width: 4px;
      height: 8px;
      border: solid var(--text-primary);
      border-width: 0 2px 2px 0;
      transform: rotate(45deg);
    }

    /* ==========================================================
       7. INTERRUPTOR (visibilidad del perfil) Y CASILLA
       ========================================================== */
    .opcion {
      display: flex;
      align-items: flex-start;
      gap: var(--sp-3);
      min-height: 44px;
      padding: var(--sp-3);
      margin-bottom: var(--sp-4);
      background: var(--surface-muted);
      border-radius: var(--radio-campo);
    }

    .opcion__texto { flex: 1; font-size: 14px; }
    .opcion__titulo { display: block; font-weight: 500; }
    .opcion__detalle { display: block; font-size: 13px; color: var(--text-secondary); }

    .interruptor {
      appearance: none;
      -webkit-appearance: none;
      position: relative;
      width: 44px;
      height: 24px;
      margin: var(--sp-1) 0 0;
      background: var(--text-secondary);
      border: 0;
      border-radius: 999px;
      cursor: pointer;
      flex-shrink: 0;
    }

    .interruptor::after {
      content: "";
      position: absolute;
      top: 3px;
      left: 3px;
      width: 18px;
      height: 18px;
      background: #FFFFFF;
      border-radius: 50%;
      transition: transform 0.2s;
    }

    .interruptor:checked { background: var(--primary); }
    .interruptor:checked::after { transform: translateX(20px); }

    .casilla {
      display: flex;
      align-items: flex-start;
      gap: var(--sp-3);
      min-height: 44px;
      font-size: 14px;
    }

    .casilla input {
      width: 20px;
      height: 20px;
      margin: var(--sp-1) 0 0;
      accent-color: var(--primary);
      flex-shrink: 0;
    }

    /* ==========================================================
       8. BOTONES Y MENSAJES
       ========================================================== */
    .boton {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: var(--sp-2);
      width: 100%;
      min-height: 44px;
      padding: var(--sp-2) var(--sp-4);
      font-family: inherit;
      font-size: 16px;
      font-weight: 500;
      text-decoration: none;
      border: 1px solid transparent;
      border-radius: var(--radio-campo);
      cursor: pointer;
    }

    .boton--primario { color: #FFFFFF; background: var(--primary); }
    .boton--primario:hover { filter: brightness(0.92); }
    .boton--primario:disabled { opacity: 0.7; cursor: not-allowed; }

    .boton--secundario {
      color: var(--text-primary);
      background: var(--surface);
      border-color: var(--border);
    }

    /* Estado de carga */
    .spinner {
      width: 18px;
      height: 18px;
      border: 2px solid rgba(255, 255, 255, 0.4);
      border-top-color: #FFFFFF;
      border-radius: 50%;
      animation: girar 0.8s linear infinite;
    }

    @keyframes girar { to { transform: rotate(360deg); } }

    @media (prefers-reduced-motion: reduce) {
      .spinner { animation-duration: 2s; }
      .interruptor::after { transition: none; }
    }

    .tarjeta__pie {
      margin: var(--sp-6) 0 0;
      font-size: 14px;
      text-align: center;
      color: var(--text-secondary);
    }

    /* ==========================================================
       9. ESTADO DE ÉXITO
       ========================================================== */
    .exito { text-align: center; }

    .exito__icono {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 64px;
      height: 64px;
      margin: 0 auto var(--sp-4);
      color: var(--text-primary);
      background: var(--success);
      border-radius: 50%;
    }

    .exito__icono .icono { width: 32px; height: 32px; }
    .exito .tarjeta__titulo:focus { outline: none; }

    /* ==========================================================
       10. PANEL DERECHO
       ========================================================== */
    .panel__frase {
      margin: 0;
      font-size: 32px;
      line-height: 1.2;
      font-weight: 700;
      text-transform: uppercase;
    }

    .panel__lista {
      margin: 0;
      padding: 0;
      list-style: none;
      display: flex;
      flex-direction: column;
      gap: var(--sp-4);
    }

    .panel__lista li {
      display: flex;
      align-items: center;
      gap: var(--sp-3);
      font-size: 16px;
    }

    .panel__icono {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 40px;
      height: 40px;
      color: var(--text-primary);
      background: var(--success);
      border-radius: var(--radio-campo);
      flex-shrink: 0;
    }
  </style>
</head>

<body>
  <main class="registro">

    <!-- ================= LADO IZQUIERDO: FORMULARIO ================= -->
    <section class="registro__lado-form">

      <!-- Tarjeta del formulario -->
      <div class="tarjeta" id="tarjeta-form">

        <div class="logo">
          <span class="logo__icono">
            <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
          </span>
          Famfit
        </div>

        <h1 class="tarjeta__titulo">Crea tu cuenta</h1>
        <p class="tarjeta__subtitulo">Solo te pediremos lo necesario para empezar. Después podrás contarnos tus metas.</p>

        <form id="form-registro" novalidate>

          <!-- Nombre -->
          <div class="campo">
            <label class="campo__etiqueta" for="nombre">Nombre completo</label>
            <div class="campo__control">
              <input class="campo__input" type="text" id="nombre" name="nombre"
                     autocomplete="name" placeholder="Ej: Ana Martínez"
                     aria-describedby="nombre-error">
            </div>
            <p class="campo__error" id="nombre-error" hidden>
              <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span></span>
            </p>
          </div>

          <!-- Nombre de usuario -->
          <div class="campo">
            <label class="campo__etiqueta" for="usuario">Nombre de usuario</label>
            <div class="campo__control">
              <input class="campo__input" type="text" id="usuario" name="usuario"
                     autocomplete="username" placeholder="Ej: ana_fit"
                     aria-describedby="usuario-ayuda usuario-error">
            </div>
            <p class="campo__ayuda" id="usuario-ayuda">Así te verán en la comunidad. De 3 a 20 caracteres.</p>
            <p class="campo__error" id="usuario-error" hidden>
              <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span></span>
            </p>
          </div>

          <!-- Correo -->
          <div class="campo">
            <label class="campo__etiqueta" for="correo">Correo electrónico</label>
            <div class="campo__control">
              <input class="campo__input" type="email" id="correo" name="correo"
                     autocomplete="email" placeholder="nombre@correo.com"
                     aria-describedby="correo-error">
            </div>
            <p class="campo__error" id="correo-error" hidden>
              <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span></span>
            </p>
          </div>

          <!-- Contraseña -->
          <div class="campo">
            <label class="campo__etiqueta" for="clave">Contraseña</label>
            <div class="campo__control">
              <input class="campo__input campo__input--con-boton" type="password" id="clave" name="clave"
                     autocomplete="new-password" placeholder="Crea una contraseña"
                     aria-describedby="reglas-clave clave-error">
              <button class="ver-clave" type="button" data-objetivo="clave"
                      aria-label="Mostrar contraseña" aria-pressed="false">
                <svg class="icono icono-ver" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono icono-ocultar" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            <ul class="reglas" id="reglas-clave" aria-label="Requisitos de la contraseña">
              <li data-regla="largo"><span class="regla__marca" aria-hidden="true"></span><span class="estado-lector solo-lectores">Pendiente: </span>Mínimo 8 caracteres</li>
              <li data-regla="letra"><span class="regla__marca" aria-hidden="true"></span><span class="estado-lector solo-lectores">Pendiente: </span>Al menos una letra</li>
              <li data-regla="numero"><span class="regla__marca" aria-hidden="true"></span><span class="estado-lector solo-lectores">Pendiente: </span>Al menos un número</li>
            </ul>
            <p class="campo__error" id="clave-error" hidden>
              <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span></span>
            </p>
          </div>

          <!-- Confirmar contraseña -->
          <div class="campo">
            <label class="campo__etiqueta" for="clave2">Confirmar contraseña</label>
            <div class="campo__control">
              <input class="campo__input campo__input--con-boton" type="password" id="clave2" name="clave2"
                     autocomplete="new-password" placeholder="Repite tu contraseña"
                     aria-describedby="clave2-error">
              <button class="ver-clave" type="button" data-objetivo="clave2"
                      aria-label="Mostrar contraseña" aria-pressed="false">
                <svg class="icono icono-ver" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg class="icono icono-ocultar" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
            <p class="campo__error" id="clave2-error" hidden>
              <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span></span>
            </p>
          </div>

          <!-- Privacidad: el perfil es privado por defecto -->
          <div class="opcion">
            <label class="opcion__texto" for="visible">
              <span class="opcion__titulo">Mostrar mi perfil en la comunidad</span>
              <span class="opcion__detalle">Es opcional. Tu perfil empieza como <strong id="estado-visible">privado</strong> y puedes cambiarlo cuando quieras.</span>
            </label>
            <input class="interruptor" type="checkbox" role="switch" id="visible" name="visible">
          </div>

          <!-- Términos y privacidad -->
          <div class="campo">
            <label class="casilla" for="terminos">
              <input type="checkbox" id="terminos" name="terminos" aria-describedby="terminos-error">
              <span>Acepto los <a href="terminos.html">Términos de uso</a> y la <a href="privacidad.html">Política de privacidad</a>.</span>
            </label>
            <p class="campo__error" id="terminos-error" hidden>
              <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span></span>
            </p>
          </div>

          <!-- Botón principal -->
          <button class="boton boton--primario" type="submit" id="boton-crear">
            <span class="spinner" id="spinner" hidden></span>
            <span id="texto-boton">Crear cuenta</span>
          </button>
        </form>

        <p class="tarjeta__pie">¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></p>
      </div>

      <!-- Tarjeta de éxito (se muestra al terminar el registro) -->
      <div class="tarjeta exito" id="tarjeta-exito" hidden>
        <div class="exito__icono">
          <svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>
        </div>
        <h2 class="tarjeta__titulo" id="titulo-exito" tabindex="-1">¡Te damos la bienvenida!</h2>
        <p class="tarjeta__subtitulo">Tu cuenta está lista. El siguiente paso es un test corto para personalizar tus rutinas. Podrás cambiar tus respuestas cuando quieras.</p>
        <a class="boton boton--primario" href="test-inicial.html">Continuar al test inicial</a>
      </div>
    </section>

    <!-- ================= LADO DERECHO: PANEL ================= -->
    <aside class="registro__panel">
      <p class="panel__frase">No es solo un objetivo, es un estilo de vida.</p>

      <ul class="panel__lista">
        <li>
          <span class="panel__icono"><svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg></span>
          Rutinas a tu medida
        </li>
        <li>
          <span class="panel__icono"><svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg></span>
          Misiones, rachas y recompensas
        </li>
        <li>
          <span class="panel__icono"><svg class="icono" viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></span>
          Una comunidad que te acompaña
        </li>
      </ul>
    </aside>
  </main>

  <script>
    // ==========================================================
    // 1. ELEMENTOS DE LA PÁGINA
    // ==========================================================
    var formulario = document.getElementById("form-registro");
    var botonCrear = document.getElementById("boton-crear");
    var textoBoton = document.getElementById("texto-boton");
    var spinner = document.getElementById("spinner");

    var campoNombre = document.getElementById("nombre");
    var campoUsuario = document.getElementById("usuario");
    var campoCorreo = document.getElementById("correo");
    var campoClave = document.getElementById("clave");
    var campoClave2 = document.getElementById("clave2");
    var campoTerminos = document.getElementById("terminos");
    var campoVisible = document.getElementById("visible");

    // ==========================================================
    // 2. VALIDACIONES
    // Cada función devuelve un mensaje de error, o "" si todo está bien.
    // ==========================================================
    function validarNombre() {
      if (campoNombre.value.trim().length < 2) {
        return "Escribe tu nombre para continuar.";
      }
      return "";
    }

    function validarUsuario() {
      var texto = campoUsuario.value.trim();
      var patron = /^[a-zA-Z0-9_]{3,20}$/;
      if (texto === "") {
        return "Elige un nombre de usuario para la comunidad.";
      }
      if (!patron.test(texto)) {
        return "Usa de 3 a 20 caracteres: letras, números o guion bajo (_), sin espacios.";
      }
      return "";
    }

    function validarCorreo() {
      var texto = campoCorreo.value.trim();
      var patron = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (texto === "") {
        return "Escribe tu correo electrónico.";
      }
      if (!patron.test(texto)) {
        return "Parece que el correo está incompleto. Ejemplo: nombre@correo.com";
      }
      return "";
    }

    // Revisa las 3 reglas de la contraseña y devuelve cuáles se cumplen
    function revisarReglas(texto) {
      return {
        largo: texto.length >= 8,
        letra: /[a-zA-Z]/.test(texto),
        numero: /[0-9]/.test(texto)
      };
    }

    function validarClave() {
      var reglas = revisarReglas(campoClave.value);
      if (campoClave.value === "") {
        return "Crea una contraseña para proteger tu cuenta.";
      }
      if (!reglas.largo || !reglas.letra || !reglas.numero) {
        return "Tu contraseña aún no cumple todos los requisitos de la lista.";
      }
      return "";
    }

    function validarClave2() {
      if (campoClave2.value === "") {
        return "Repite tu contraseña para confirmarla.";
      }
      if (campoClave2.value !== campoClave.value) {
        return "Las contraseñas no coinciden. Revisa que sean iguales.";
      }
      return "";
    }

    function validarTerminos() {
      if (!campoTerminos.checked) {
        return "Para crear tu cuenta necesitas aceptar los términos y la política de privacidad.";
      }
      return "";
    }

    // ==========================================================
    // 3. MOSTRAR Y QUITAR ERRORES
    // ==========================================================
    function mostrarError(campo, mensaje) {
      var caja = document.getElementById(campo.id + "-error");
      var texto = caja.querySelector("span");

      if (mensaje === "") {
        caja.hidden = true;
        texto.textContent = "";
        campo.removeAttribute("aria-invalid");
        campo.classList.remove("es-invalido");
      } else {
        caja.hidden = false;
        texto.textContent = mensaje;
        campo.setAttribute("aria-invalid", "true");
        campo.classList.add("es-invalido");
      }
    }

    // Valida un campo y muestra su error. Devuelve true si está bien.
    function revisarCampo(campo, funcionValidar) {
      var mensaje = funcionValidar();
      mostrarError(campo, mensaje);
      return mensaje === "";
    }

    // Valida al salir del campo y corrige en vivo si ya había error
    function activarValidacion(campo, funcionValidar, evento) {
      campo.addEventListener("blur", function () {
        revisarCampo(campo, funcionValidar);
      });
      campo.addEventListener(evento, function () {
        if (campo.classList.contains("es-invalido")) {
          revisarCampo(campo, funcionValidar);
        }
      });
    }

    activarValidacion(campoNombre, validarNombre, "input");
    activarValidacion(campoUsuario, validarUsuario, "input");
    activarValidacion(campoCorreo, validarCorreo, "input");
    activarValidacion(campoClave, validarClave, "input");
    activarValidacion(campoClave2, validarClave2, "input");
    activarValidacion(campoTerminos, validarTerminos, "change");

    // ==========================================================
    // 4. LISTA DE REQUISITOS DE LA CONTRASEÑA
    // ==========================================================
    function actualizarReglas() {
      var reglas = revisarReglas(campoClave.value);
      var items = document.querySelectorAll("#reglas-clave li");

      for (var i = 0; i < items.length; i++) {
        var nombreRegla = items[i].getAttribute("data-regla");
        var lector = items[i].querySelector(".estado-lector");

        if (reglas[nombreRegla]) {
          items[i].classList.add("cumple");
          lector.textContent = "Cumplida: ";
        } else {
          items[i].classList.remove("cumple");
          lector.textContent = "Pendiente: ";
        }
      }
    }

    campoClave.addEventListener("input", function () {
      actualizarReglas();
      // Si ya escribió la confirmación, la volvemos a revisar
      if (campoClave2.value !== "") {
        revisarCampo(campoClave2, validarClave2);
      }
    });

    // ==========================================================
    // 5. MOSTRAR / OCULTAR CONTRASEÑA
    // ==========================================================
    function alternarClave(evento) {
      var boton = evento.currentTarget;
      var input = document.getElementById(boton.getAttribute("data-objetivo"));
      var mostrar = input.type === "password";

      if (mostrar) {
        input.type = "text";
        boton.setAttribute("aria-label", "Ocultar contraseña");
        boton.setAttribute("aria-pressed", "true");
        boton.classList.add("mostrando");
      } else {
        input.type = "password";
        boton.setAttribute("aria-label", "Mostrar contraseña");
        boton.setAttribute("aria-pressed", "false");
        boton.classList.remove("mostrando");
      }
    }

    var botonesVer = document.querySelectorAll(".ver-clave");
    for (var i = 0; i < botonesVer.length; i++) {
      botonesVer[i].addEventListener("click", alternarClave);
    }

    // ==========================================================
    // 6. INTERRUPTOR DE VISIBILIDAD DEL PERFIL
    // ==========================================================
    campoVisible.addEventListener("change", function () {
      var estado = document.getElementById("estado-visible");
      estado.textContent = campoVisible.checked ? "visible para la comunidad" : "privado";
    });

    // ==========================================================
    // 7. ESTADOS: CARGANDO Y ÉXITO
    // ==========================================================
    function ponerCargando(estaCargando) {
      botonCrear.disabled = estaCargando;
      botonCrear.setAttribute("aria-busy", estaCargando ? "true" : "false");
      spinner.hidden = !estaCargando;
      textoBoton.textContent = estaCargando ? "Creando tu cuenta..." : "Crear cuenta";
    }

    function mostrarExito() {
      var primerNombre = campoNombre.value.trim().split(" ")[0];
      var titulo = document.getElementById("titulo-exito");

      titulo.textContent = "¡Te damos la bienvenida, " + primerNombre + "!";
      document.getElementById("tarjeta-form").hidden = true;
      document.getElementById("tarjeta-exito").hidden = false;
      titulo.focus();
    }

    // ==========================================================
    // 8. VALIDAR TODO EL FORMULARIO
    // ==========================================================
    function validarTodo() {
      var lista = [
        [campoNombre, validarNombre],
        [campoUsuario, validarUsuario],
        [campoCorreo, validarCorreo],
        [campoClave, validarClave],
        [campoClave2, validarClave2],
        [campoTerminos, validarTerminos]
      ];
      var primerInvalido = null;

      for (var i = 0; i < lista.length; i++) {
        var esValido = revisarCampo(lista[i][0], lista[i][1]);
        if (!esValido && primerInvalido === null) {
          primerInvalido = lista[i][0];
        }
      }

      // Llevamos el foco al primer campo con error
      if (primerInvalido !== null) {
        primerInvalido.focus();
        return false;
      }
      return true;
    }

    // ==========================================================
    // 9. ENVIAR EL FORMULARIO
    // ==========================================================
    formulario.addEventListener("submit", function (evento) {
      evento.preventDefault();

      if (!validarTodo()) {
        return;
      }

      ponerCargando(true);

      // Simulación del envío (solo para ver el diseño del front-end)
      setTimeout(function () {
        ponerCargando(false);
        mostrarExito();
      }, 1200);
    });
  </script>
</body>
</html>
