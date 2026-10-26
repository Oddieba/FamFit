<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Famfit - Menú Principal</title>
  <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">

  <style>
    /* ---------- General ---------- */
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
      font-family: "Segoe UI", Arial, sans-serif;
    }

    body {
      background-color: #f0f2f5;
      color: #1a1a2e;
    }

    .app {
      display: flex;
      min-height: 100vh;
    }

    /* ---------- Menú lateral (izquierda y derecha) ---------- */
    .menu-lateral {
      width: 200px;
      padding: 24px 12px;
      display: flex;
      flex-direction: column;
      background-color: #ffffff;
      border-right: 1px solid #e0e4e8;
      box-shadow: 2px 0 8px rgba(0,0,0,0.04);
    }

    .menu-lateral.derecho {
      border-right: none;
      border-left: 1px solid #e0e4e8;
      box-shadow: -2px 0 8px rgba(0,0,0,0.04);
    }

    .logo {
      font-size: 28px;
      font-style: italic;
      font-weight: 900;
      margin-bottom: 30px;
      padding-left: 8px;
      color: #1a1a2e;
    }

    .menu-lateral nav {
      flex: 1;
      display: flex;
      flex-direction: column;
      gap: 6px;
    }

    .opcion {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      width: 100%;
      padding: 18px 10px;
      border: none;
      border-radius: 14px;
      background: transparent;
      color: #555;
      font-size: 12px;
      font-weight: 500;
      text-align: center;
      text-decoration: none;
      cursor: pointer;
      transition: all 0.2s ease;
      gap: 8px;
    }

    .opcion .icono-opcion {
      font-size: 32px;
      line-height: 1;
    }

    .opcion:hover {
      background-color: #f0f4ff;
      color: #1f6bff;
    }

    .opcion.activa {
      background-color: #1f6bff;
      color: #ffffff;
    }

    .opcion.activa .icono-opcion {
      filter: brightness(0) invert(1);
    }

    /* ---------- Contenido central ---------- */
    .contenido {
      flex: 1;
      padding: 30px 40px;
      background-color: #f0f2f5;
      overflow-y: auto;
    }

    /* Barra superior */
    .barra {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 30px;
    }

    .buscador {
      width: 380px;
      padding: 12px 18px;
      border: 1px solid #d0d5dd;
      border-radius: 25px;
      background-color: #ffffff;
      color: #1a1a2e;
      outline: none;
      font-size: 14px;
    }

    .buscador::placeholder {
      color: #999;
    }

    .usuario-info {
      display: flex;
      align-items: center;
      gap: 10px;
      font-size: 14px;
      color: #333;
    }

    .usuario-info .campana {
      font-size: 20px;
      cursor: pointer;
    }

    /* ---------- Secciones ---------- */
    .seccion {
      display: none;
    }

    .seccion.activa {
      display: block;
    }

    /* ---------- Saludo ---------- */
    .saludo {
      margin-bottom: 24px;
    }

    .saludo h2 {
      font-size: 30px;
      margin-bottom: 6px;
      color: #1a1a2e;
    }

    .subtitulo {
      color: #666;
      margin-bottom: 20px;
      font-size: 15px;
    }

    .subtitulo span {
      color: #1f6bff;
    }

    /* ---------- Avatar central ---------- */
    .avatar-container {
      display: flex;
      flex-direction: column;
      align-items: center;
      margin-bottom: 30px;
      padding: 30px;
      background: #ffffff;
      border-radius: 20px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.06);
    }

    .avatar {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, #1f6bff, #3fa0ff);
      display: flex;
      justify-content: center;
      align-items: center;
      font-size: 48px;
      color: #fff;
      margin-bottom: 14px;
      box-shadow: 0 6px 20px rgba(31, 107, 255, 0.3);
      border: 4px solid #ffffff;
      outline: 3px solid #1f6bff;
    }

    .avatar-nombre {
      font-size: 22px;
      font-weight: 700;
      color: #1a1a2e;
      margin-bottom: 4px;
    }

    .avatar-nivel {
      font-size: 13px;
      color: #888;
      background: #f0f4ff;
      padding: 4px 14px;
      border-radius: 20px;
      color: #1f6bff;
      font-weight: 600;
    }

    /* ---------- Estadísticas del usuario ---------- */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
      margin-bottom: 24px;
    }

    .stat-card {
      padding: 22px;
      border: 1px solid #e0e4e8;
      border-radius: 14px;
      background-color: #ffffff;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .stat-card h3 {
      margin: 12px 0 6px;
      font-size: 16px;
      color: #1a1a2e;
    }

    .stat-card p {
      font-size: 13px;
      color: #888;
      margin-bottom: 14px;
    }

    .stat-icon {
      width: 52px;
      height: 52px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 12px;
      font-size: 26px;
      margin: 0 auto;
    }

    .azul   { background-color: #e8f0ff; color: #1f6bff; }
    .verde  { background-color: #e6f9f4; color: #14b89c; }
    .morado { background-color: #eee8ff; color: #5b5cf0; }
    .lila   { background-color: #f0e8ff; color: #7a5cf0; }
    .naranja { background-color: #fff3e0; color: #f59e0b; }
    .rojo   { background-color: #ffe8e8; color: #ef4444; }

    .boton {
      width: 100%;
      padding: 10px;
      border: none;
      border-radius: 20px;
      background-color: #1f6bff;
      color: #ffffff;
      font-weight: 600;
      font-size: 14px;
      cursor: pointer;
      transition: background 0.2s;
    }

    .boton:hover {
      background-color: #1558d9;
    }

    /* ---------- Fila inferior - Progreso ---------- */
    .fila-inferior {
      display: flex;
      gap: 18px;
    }

    .caja-progreso {
      flex: 2;
      padding: 22px;
      border: 1px solid #e0e4e8;
      border-radius: 14px;
      background-color: #ffffff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .caja-progreso h3 {
      font-size: 18px;
      color: #1a1a2e;
      margin-bottom: 10px;
    }

    .anillos {
      display: flex;
      justify-content: space-around;
      margin-top: 20px;
    }

    .item-anillo {
      display: flex;
      align-items: center;
      gap: 12px;
    }

    .item-anillo small {
      display: block;
      font-size: 12px;
      color: #999;
    }

    .item-anillo strong {
      font-size: 18px;
      color: #1a1a2e;
    }

    .anillo {
      width: 70px;
      height: 70px;
      display: flex;
      justify-content: center;
      align-items: center;
      border-radius: 50%;
      background: conic-gradient(var(--color) var(--p), #e0e4e8 0);
      position: relative;
    }

    .anillo::before {
      content: "";
      position: absolute;
      width: 54px;
      height: 54px;
      border-radius: 50%;
      background-color: #ffffff;
    }

    .anillo span {
      position: relative;
      font-size: 20px;
      font-weight: 700;
      color: #1a1a2e;
    }

    .caja-frase {
      flex: 1;
      display: flex;
      align-items: flex-end;
      padding: 22px;
      border-radius: 14px;
      background: linear-gradient(180deg, #a8b8d0, #f0c878);
      box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .caja-frase p {
      font-size: 18px;
      font-weight: 600;
      color: #ffffff;
      text-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    /* ---------- Stats adicionales ---------- */
    .stats-adicionales {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 18px;
      margin-bottom: 24px;
    }

    .stat-adicional {
      padding: 20px;
      border: 1px solid #e0e4e8;
      border-radius: 14px;
      background-color: #ffffff;
      box-shadow: 0 2px 8px rgba(0,0,0,0.03);
    }

    .stat-adicional h4 {
      font-size: 14px;
      color: #888;
      margin-bottom: 8px;
    }

    .stat-adicional .valor {
      font-size: 28px;
      font-weight: 700;
      color: #1a1a2e;
    }

    .stat-adicional .valor span {
      font-size: 14px;
      color: #999;
      font-weight: 400;
    }

    .barra-progreso {
      width: 100%;
      height: 8px;
      background: #e0e4e8;
      border-radius: 4px;
      margin-top: 10px;
      overflow: hidden;
    }

    .barra-progreso .fill {
      height: 100%;
      border-radius: 4px;
      background: #1f6bff;
    }

    .barra-progreso .fill.verde { background: #14b89c; }
    .barra-progreso .fill.morado { background: #5b5cf0; }
    .barra-progreso .fill.naranja { background: #f59e0b; }

    /* ---------- Pantallas pequeñas ---------- */
    @media (max-width: 1100px) {
      .menu-lateral {
        width: 160px;
      }
      .opcion .icono-opcion {
        font-size: 26px;
      }
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }

    @media (max-width: 900px) {
      .app { flex-direction: column; }
      .menu-lateral {
        width: 100%;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: center;
        border-right: none;
        border-bottom: 1px solid #e0e4e8;
      }
      .menu-lateral.derecho {
        border-left: none;
        border-top: 1px solid #e0e4e8;
      }
      .opcion {
        width: auto;
        padding: 12px 16px;
      }
      .stats-grid { grid-template-columns: 1fr; }
      .fila-inferior { flex-direction: column; }
      .buscador { width: 200px; }
    }
  </style>
</head>
<body>

  <div class="app">

    <!-- ========== MENÚ IZQUIERDO ========== -->
    <aside class="menu-lateral">
      <div class="logo">Famfit</div>
      <nav>
        <button class="opcion activa" data-seccion="inicio">
          <span class="icono-opcion"></span>
          Inicio
        </button>
        <a class="opcion" href="{{ route('progreso') }}" data-seccion="progreso">progreso</a>

         <button class="opcion" data-seccion="Retos">
          <span class="icono-opcion">🏆</span>
          Retos
        <button class="opcion" data-seccion="entrenamientos">
          <span class="icono-opcion">🏋️</span>
          Entrenamientos
        </button>
        <button class="opcion" data-seccion="alimentacion">
          <span class="icono-opcion">🥗</span>
          Alimentación
        </button>
        <button class="opcion" data-seccion="habitos">
          <span class="icono-opcion">✅</span>
          Hábitos
        </button>
        <button class="opcion" data-seccion="estadisticas">
          <span class="icono-opcion">📈</span>
          Estadísticas
        </button>
         <button class="opcion" data-seccion="avatares">
          <span class="icono-opcion"></span>
          avatares
        </button>
        <button class="opcion" data-seccion="social">
          <span class="icono-opcion">👥</span>
          Social
        </button>
        <button class="opcion" data-seccion="bienestar">
          <span class="icono-opcion">🧘</span>
          Bienestar
        </button>
          <a class="opcion" href="{{ route('configuracion') }}" data-seccion="configuracion">configuracion</a>
          
        <button class="opcion" data-seccion="sesion">
          <span class="icono-opcion">🚪</span>
          Cerrar sesión
        </button>
      </nav>
    </aside>

    <!-- ========== CONTENIDO CENTRAL ========== -->
    <main class="contenido">

      <!-- Barra superior -->
      <div class="barra">
        <input type="text" class="buscador" placeholder="Buscar en Famfit...">
        <div class="usuario-info">
          <span class="campana">🔔</span>
          <span>👤 Misael</span>
        </div>
      </div>

      <!-- Sección Inicio -->
      <div class="seccion activa" id="inicio">

        <!-- Saludo -->
        <div class="saludo">
          <h2>¡Hola, Campeón! 👋</h2>
          <p class="subtitulo">Sigue construyendo <span>la mejor versión de ti.</span></p>
        </div>

        <!-- Avatar central -->
        <div class="avatar-container">
          <div class="avatar">🏆</div>
          <div class="avatar-nombre">Misael</div>
          <div class="avatar-nivel">Nivel 12 · Campeón</div>
        </div>

        <!-- Estadísticas del usuario -->
        <div class="stats-grid">
          <div class="stat-card tarjeta">
            <div class="stat-icon azul">️</div>
            <h3>Entrenamiento</h3>
            <p>Entrena tu cuerpo, fortalece tu mente.</p>
            <button class="boton">Ver rutina</button>
          </div>
          <div class="stat-card tarjeta">
            <div class="stat-icon verde">🥗</div>
            <h3>Alimentación</h3>
            <p>Comer bien también es entrenar.</p>
            <button class="boton">Ver plan</button>
          </div>
          <div class="stat-card tarjeta">
            <div class="stat-icon morado">✅</div>
            <h3>Hábitos</h3>
            <p>Pequeños hábitos, grandes cambios.</p>
            <button class="boton">Ver hábitos</button>
          </div>
          <div class="stat-card tarjeta">
            <div class="stat-icon lila">📊</div>
            <h3>Estadísticas</h3>
            <p>Tu progreso en tiempo real.</p>
            <button class="boton">Ver progreso</button>
          </div>
        </div>

        <!-- Stats adicionales -->
        <div class="stats-adicionales">
          <div class="stat-adicional tarjeta">
            <h4>Entrenamientos esta semana</h4>
            <div class="valor">2 <span>/ 5</span></div>
            <div class="barra-progreso"><div class="fill" style="width: 40%"></div></div>
          </div>
          <div class="stat-adicional tarjeta">
            <h4>Comidas saludables</h4>
            <div class="valor">4 <span>/ 7</span></div>
            <div class="barra-progreso"><div class="fill verde" style="width: 57%"></div></div>
          </div>
          <div class="stat-adicional tarjeta">
            <h4>Horas de sueño</h4>
            <div class="valor">6 <span>/ 8</span></div>
            <div class="barra-progreso"><div class="fill morado" style="width: 75%"></div></div>
          </div>
          <div class="stat-adicional tarjeta">
            <h4>Racha actual</h4>
            <div class="valor">12 <span>días 🔥</span></div>
            <div class="barra-progreso"><div class="fill naranja" style="width: 80%"></div></div>
          </div>
        </div>

        <!-- Fila inferior -->
        <div class="fila-inferior">
          <div class="caja-progreso tarjeta">
            <h3>Tu progreso esta semana</h3>
            <div class="anillos">
              <div class="item-anillo">
                <div class="anillo" style="--color: #1f6bff; --p: 40%;">
                  <span>2</span>
                </div>
                <div>
                  <small>Entrenamientos</small>
                  <strong>2 / 5</strong>
                </div>
              </div>
              <div class="item-anillo">
                <div class="anillo" style="--color: #14b89c; --p: 57%;">
                  <span>4</span>
                </div>
                <div>
                  <small>Comidas saludables</small>
                  <strong>4 / 7</strong>
                </div>
              </div>
              <div class="item-anillo">
                <div class="anillo" style="--color: #5b5cf0; --p: 75%;">
                  <span>6</span>
                </div>
                <div>
                  <small>Horas de sueño</small>
                  <strong>6 / 8</strong>
                </div>
              </div>
            </div>
          </div>
          <div class="caja-frase tarjeta">
            <p>Disciplina hoy, resultados mañana.</p>
          </div>
        </div>

      </div>

    </main>

    <!-- ========== MENÚ DERECHO ========== -->
    <aside class="menu-lateral derecho">
      <div class="logo" style="visibility:hidden;">Famfit</div>
      <nav>
        <button class="opcion" data-seccion="retos">
          <span class="icono-opcion"></span>
          Retos
        </button>
        <button class="opcion" data-seccion="logros">
          <span class="icono-opcion">🏅</span>
          Logros
        </button>
        <button class="opcion" data-seccion="amigos">
          <span class="icono-opcion"></span>
          Amigos
        </button>
        <button class="opcion" data-seccion="mensajes">
          <span class="icono-opcion">💬</span>
          Mensajes
        </button>
        <button class="opcion" data-seccion="tienda">
          <span class="icono-opcion">🛒</span>
          Tienda
        </button>
        <button class="opcion" data-seccion="premium">
          <span class="icono-opcion">⭐</span>
          Premium
        </button>
        <button class="opcion" data-seccion="recetas">
          <span class="icono-opcion">🍳</span>
          Recetas
        </button>
        <button class="opcion" data-seccion="rutinas">
          <span class="icono-opcion">📋</span>
          Rutinas
        </button>
        <button class="opcion" data-seccion="calendario">
          <span class="icono-opcion">📅</span>
          Calendario
        </button>
         <a class="opcion" href="#" data-seccion="ayuda">ayuda</a>
        <button class="opcion" data-seccion="perfil">
          <span class="icono-opcion">👤</span>
          Mi perfil
        </button>
      </nav>
    </aside>

  </div>

  <script>
    // Navegación entre secciones
    document.querySelectorAll('.opcion').forEach(btn => {
      btn.addEventListener('click', function () {
        document.querySelectorAll('.opcion').forEach(b => b.classList.remove('activa'));
        this.classList.add('activa');
      });
    });
  </script>

</body>
</html>