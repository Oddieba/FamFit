<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FamFit Quest | Bienestar</title>
<link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
<style>
  :root{
    --bg:#050a13; --sidebar:#081323; --panel:#0b1729; --panel2:#0e1c31;
    --border:#1b3555; --text:#f4f7ff; --muted:#a8bbd6; --blue:#2468f2;
    --blue2:#17477f; --teal:#16c4ae; --purple:#8957ff; --gold:#f1b75b;
    --green:#9be76d; --red:#ff777e;
  }
  *{box-sizing:border-box}
  body{margin:0;background:var(--bg);color:var(--text);font-family:Arial,Helvetica,sans-serif;font-size:14px}
  button,input,select{font:inherit}
  button{cursor:pointer}
  .app{min-height:100vh;display:grid;grid-template-columns:230px minmax(0,1fr)}
  .sidebar{background:var(--sidebar);border-right:1px solid var(--border);padding:25px 15px;display:flex;flex-direction:column;gap:25px}
  .brand{font-size:29px;font-weight:900;font-style:italic;letter-spacing:-1px;padding:0 8px 4px}
  .brand span{color:var(--green)}
  .sidebar-toggle{background:#102442;color:#dce9ff;border:1px solid var(--border);border-radius:9px;padding:10px 12px;text-align:left;font-weight:700}
  .nav{display:grid;gap:7px}
  .nav button{min-height:42px}
  .nav-icon{width:23px;flex:0 0 23px;text-align:center}
  .nav-label{overflow:hidden;text-overflow:ellipsis}
  .app.sidebar-collapsed{grid-template-columns:76px minmax(0,1fr)}
  .sidebar-collapsed .sidebar{padding-left:12px;padding-right:12px}
  .sidebar-collapsed .brand{font-size:0;padding-left:6px}
  .sidebar-collapsed .brand:before{content:"F.";font-size:26px}
  .sidebar-collapsed .nav button{justify-content:center;padding:12px 5px}
  .sidebar-collapsed .nav-label,.sidebar-collapsed .sidebar-toggle span,.sidebar-collapsed .side-note{display:none}
  .sidebar-collapsed .sidebar-toggle{text-align:center;padding:10px 4px}

  .nav button{background:transparent;border:0;color:#e0eaff;text-align:left;padding:13px 12px;border-radius:10px;display:flex;align-items:center;gap:10px}
  .nav button:hover{background:#102442}
  .nav button.active{background:var(--blue);color:white}
  .side-note{margin-top:auto;border:1px solid var(--border);border-radius:12px;padding:14px;color:#c6d7ef;line-height:1.5;background:#0b182a}
  main{min-width:0;padding:0 30px 38px}
  .topbar{height:52px;display:flex;align-items:center;justify-content:flex-end;gap:18px}
  .search{width:min(400px,60%);background:#0e1d33;border:1px solid var(--border);border-radius:28px;padding:12px 18px;color:white;outline:none}
  .user{color:#d9e5f8;white-space:nowrap}
  .page-head{display:flex;justify-content:space-between;align-items:flex-start;gap:15px;margin:4px 0 18px}
  h1{font-size:29px;margin:0 0 8px;letter-spacing:-.6px}
  h2{font-size:18px;margin:0 0 7px}
  h3{font-size:15px;margin:0 0 8px}
  p{margin:0;color:var(--muted);line-height:1.5}
  .accent{color:#69a5ff}
  .eyebrow{color:var(--teal);font-size:11px;text-transform:uppercase;font-weight:800;letter-spacing:1.5px;margin-bottom:8px}
  .button{border:0;border-radius:25px;background:var(--blue2);color:white;padding:11px 17px;font-weight:700}
  .button:hover{filter:brightness(1.18)}
  .button.primary{background:var(--blue)}
  .button.ghost{background:#101f35;border:1px solid var(--border)}
  .grid{display:grid;gap:18px}
  .sleep-layout{grid-template-columns:minmax(0,1.6fr) minmax(230px,.8fr)}
  .panel{background:var(--panel);border:1px solid var(--border);border-radius:15px;padding:22px;min-width:0}
  .sleep-main{display:flex;align-items:center;justify-content:space-between;gap:15px;min-height:200px}
  .big-hours{font-size:58px;font-weight:800;letter-spacing:-2px;margin:8px 0}
  .big-hours small{font-size:22px;color:var(--muted);letter-spacing:0}
  .sleep-status{display:inline-flex;align-items:center;gap:7px;border-radius:20px;padding:7px 10px;background:#102b31;color:#7ee6d5;font-size:12px;font-weight:bold}
  .avatar-box{width:145px;min-width:125px;min-height:155px;background:linear-gradient(155deg,#142a48,#102034);border:1px solid #25446b;border-radius:17px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:8px;text-align:center;padding:12px}
  .avatar-face{font-size:60px;line-height:1.2;filter:drop-shadow(0 5px 9px #0005)}
  .avatar-label{font-size:12px;color:#d6e4f7;font-weight:bold}
   .meter{width:104px;height:104px;flex:0 0 104px;border-radius:50%;padding:9px;background:conic-gradient(var(--blue) 0deg, var(--teal) 0deg, #1b2b43 0deg);margin:0 0 10px;display:grid;place-items:center;position:relative}
  .meter::before{content:"";position:absolute;inset:9px;background:var(--panel);border-radius:50%}
  .meter span{position:relative;z-index:1;width:auto;height:auto;background:none;color:var(--text);font-size:15px;font-weight:800}
  .sleep-main .meter-wrap{display:flex;align-items:center;gap:14px}
  .muted{color:var(--muted);font-size:12px}
  .target-number{font-size:36px;font-weight:800;margin:10px 0 4px}
  .target-number small{font-size:16px;color:var(--muted)}
  .reward{margin-top:17px;border-radius:12px;padding:13px;background:#132a25;border:1px solid #245044}
  .reward strong{color:var(--green)}
  .section-title{display:flex;justify-content:space-between;align-items:center;gap:12px;margin-bottom:15px}
  .select{background:#0d1b2e;color:white;border:1px solid var(--border);border-radius:9px;padding:9px 12px;outline:none}
  .stats{grid-template-columns:repeat(3,minmax(0,1fr));margin-top:18px}
  .stat-card{background:var(--panel);border:1px solid var(--border);border-radius:13px;padding:17px}
  .stat-label{color:var(--muted);font-size:12px}
  .stat-value{font-size:25px;font-weight:800;margin:8px 0 4px}
  .stat-value small{font-size:13px;color:var(--muted);font-weight:normal}
  .month-grid{grid-template-columns:minmax(0,1fr);margin-top:18px}
  .table-wrap{overflow-x:auto}
  table{width:100%;border-collapse:collapse;white-space:nowrap}
  th,td{text-align:left;padding:15px 14px;border-bottom:1px solid #1b2c44}
  th{font-size:11px;color:#92a9c8;text-transform:uppercase;letter-spacing:.5px}
  td{font-size:14px}
  tr:last-child td{border-bottom:0}
  .day-cell{color:#e8efff;font-weight:700}
  .bar-cell{min-width:180px}
  .mini-bar{height:9px;background:#1a2b43;border-radius:9px;overflow:hidden}
  .mini-bar span{height:100%;display:block;background:var(--teal);border-radius:9px}
  .tag{font-size:11px;border-radius:20px;padding:5px 8px;background:#14342f;color:#8be6d3}
  .tag.low{background:#39272e;color:#ffabb0}
  .tips{display:grid;gap:12px}
  .tip{display:flex;gap:11px;align-items:flex-start;padding-bottom:12px;border-bottom:1px solid #1b2c44}
  .tip:last-child{border:0;padding-bottom:0}
  .tip-icon{width:34px;height:34px;flex-shrink:0;border-radius:10px;background:#162d4a;display:grid;place-items:center;font-size:17px}
  .tip p{font-size:12px}
  .tabs{display:flex;gap:8px;flex-wrap:wrap;margin:0 0 18px}
  .tab{border:1px solid var(--border);border-radius:10px;padding:11px 16px;background:#0b1729;color:#b7c8e1}
  .tab.active{background:var(--blue);border-color:var(--blue);color:white}
  .mission-grid{grid-template-columns:repeat(3,minmax(0,1fr))}
  .mission{display:flex;flex-direction:column;gap:13px}
  .mission-icon{width:45px;height:45px;border-radius:12px;background:#17365c;display:grid;place-items:center;font-size:23px}
  .mission .button{margin-top:auto}
  .xp{color:var(--green);font-weight:800}
  .progress-line{height:7px;background:#1a2b43;border-radius:9px;overflow:hidden}
  .progress-line span{display:block;height:100%;background:var(--teal);border-radius:9px}
  .hidden{display:none!important}
  .toast{position:fixed;right:22px;bottom:22px;max-width:330px;padding:14px 18px;background:#14342f;border:1px solid #26715f;border-radius:12px;box-shadow:0 12px 35px #0007;display:none;z-index:10}
  .toast.show{display:block}
  .empty-note{font-size:12px;color:var(--muted);margin-top:8px}
  @media(max-width:1050px){.app{grid-template-columns:195px minmax(0,1fr)}main{padding:0 20px 30px}.sleep-layout,.month-grid{grid-template-columns:1fr}.mission-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
  @media(max-width:700px){.app,.app.sidebar-collapsed{display:block;grid-template-columns:1fr}.sidebar{padding:13px;gap:12px;border-right:0;border-bottom:1px solid var(--border)}.sidebar-toggle{align-self:flex-start}.sidebar-collapsed .nav-label{display:inline}.sidebar-collapsed .brand{font-size:24px}.sidebar-collapsed .brand:before{content:none}.sidebar-collapsed .sidebar-toggle span{display:inline}.brand{font-size:24px}.nav{display:flex;overflow-x:auto;gap:5px}.nav button{white-space:nowrap;padding:10px}.side-note{display:none}main{padding:0 14px 25px}.topbar{height:60px}.search{width:65%}.page-head{margin-top:10px}h1{font-size:24px}.sleep-main{align-items:flex-start}.big-hours{font-size:46px}.avatar-box{width:115px;min-width:105px}.avatar-face{font-size:48px}.stats{grid-template-columns:1fr}.mission-grid{grid-template-columns:1fr}.panel{padding:16px}.section-title{align-items:flex-start;flex-direction:column}}
</style>
</head>
<body>
<div class="app">
  <aside class="sidebar">
    <div class="brand">Famfit<span>.</span></div>
    <button class="sidebar-toggle" id="sidebarToggle" aria-label="Contraer menú" aria-expanded="true">☰ <span>Contraer menú</span></button>
    <nav class="nav" aria-label="Navegación principal">
      <button data-page="wellness" class="active"><span class="nav-icon">🏠</span><span class="nav-label">Inicio</span></button>
      <button data-page="progress"><span class="nav-icon">📈</span><span class="nav-label">Mi progreso</span></button>
      <button data-page="training"><span class="nav-icon">🏋️</span><span class="nav-label">Entrenamientos</span></button>
      <button data-page="nutrition"><span class="nav-icon">🥗</span><span class="nav-label">Alimentación</span></button>
      <button data-page="habits"><span class="nav-icon">✅</span><span class="nav-label">Hábitos</span></button>
      <button data-page="stats"><span class="nav-icon">📊</span><span class="nav-label">Estadísticas</span></button>
      <button data-page="avatar"><span class="nav-icon">🧍</span><span class="nav-label">Avatares</span></button>
      <button data-page="social"><span class="nav-icon">👥</span><span class="nav-label">Social</span></button>
      <button data-page="sleep" class="subnav"><span class="nav-icon">🌙</span><span class="nav-label">Sueño</span></button>
      <button data-page="activities" class="subnav"><span class="nav-icon">🧘</span><span class="nav-label">Bienestar activo</span></button>
      <button data-page="store"><span class="nav-icon">🛍️</span><span class="nav-label">Tienda y recompensas</span></button>
      <button data-page="settings"><span class="nav-icon">⚙️</span><span class="nav-label">Configuración</span></button>
      <button data-page="logout"><span class="nav-icon">🚪</span><span class="nav-label">Cerrar sesión</span></button>
    </nav>
    <div class="side-note">💙 <strong>Descansar también es progresar.</strong><br>Pequeños hábitos, grandes cambios.</div>
  </aside>

  <main>
    <header class="topbar">
      
      <div class="user">🔔 &nbsp; 👤 Usuario</div>
    </header>

    <section id="page-wellness">
      <div class="page-head">
        <div><div class="eyebrow">Tu espacio de recuperación</div><h1>¡Cuida tu energía! 🌙</h1><p>El bienestar también se construye mientras descansas.</p></div>
        <button class="button ghost" id="todayBtn">📅 Hoy</button>
      </div>
      <div class="grid sleep-layout">
        <article class="panel tarjeta">
          <div class="eyebrow">Tu descanso de anoche</div>
          <div class="sleep-main">
            <div>
              <div class="muted">Horas dormidas</div>
              <div class="big-hours"><span id="hoursDisplay">7.5</span><small> h</small></div>
              <div id="sleepStatus" class="sleep-status">● Buen descanso</div>
              <p style="margin-top:12px">Registra tu sueño para seguir tu progreso.</p>
            </div>
            <div style="display:flex;align-items:center;gap:14px;flex-wrap:wrap;justify-content:center">
              <div class="meter" id="sleepMeterRing"><span id="sleepMeter">94%</span></div>
              <div class="avatar-box">
                <div id="avatarFace" class="avatar-face">🙂</div>
                <div id="avatarLabel" class="avatar-label">Avatar con energía</div>
                <div class="muted" id="avatarSub">¡Buen trabajo!</div>
              </div>
            </div>
          </div>
          <div class="section-title"><span class="muted" id="sleepMeterLabel">7.5 de 8 horas recomendadas</span><button class="button" id="editSleepBtn">Registrar sueño</button></div>
          <div style="display:flex;justify-content:flex-start;margin:10px 0 4px">
            <button class="button primary" id="goActivitiesBtn">🧘 Explorar relajación e hidratación · Ganar XP</button>
          </div>
          <div id="sleepEditor" class="hidden">
            <label for="sleepInput" class="muted">¿Cuántas horas dormiste anoche?</label>
            <div style="display:flex;gap:9px;margin-top:8px;flex-wrap:wrap">
              <input id="sleepInput" class="select" type="number" min="0" max="16" step=".5" value="7.5" style="width:130px">
              <button class="button primary" id="saveSleepBtn">Guardar registro</button>
            </div>
            <p class="empty-note">Este prototipo guarda los registros en este navegador mediante localStorage.</p>
          </div>
        </article>

        <article class="panel tarjeta">
          <div class="eyebrow">Tu objetivo diario</div>
          <h2>Horas recomendadas</h2>
          <div class="target-number"><span id="targetDisplay">8</span><small> horas</small></div>
          <p>Para la mayoría de adultos, se recomienda dormir al menos 7 horas; la necesidad varía según la edad y la persona.</p>
          <div class="reward">
            <div style="font-size:12px;color:#b6d4c8;margin-bottom:6px">🎁 Recompensa de hoy</div>
            <strong>+30 XP</strong> <span style="color:#d4e8df">por alcanzar tu objetivo</span>
            <p style="margin-top:6px;font-size:12px" id="rewardStatus">¡Estás cerca de desbloquearla!</p>
          </div>
        </article>
      </div>

      <div class="grid stats">
        <article class="stat-card tarjeta"><div class="stat-label">PROMEDIO DEL MES</div><div class="stat-value"><span id="avgStat">7.2</span> <small>h / noche</small></div><p id="avgCaption">Tu promedio mensual</p></article>
        <article class="stat-card tarjeta"><div class="stat-label">HORAS TOTALES</div><div class="stat-value"><span id="totalStat">216</span> <small>horas</small></div><p>Acumuladas en el mes seleccionado</p></article>
        <article class="stat-card tarjeta"><div class="stat-label">OBJETIVO DIARIO</div><div class="stat-value">8 <small>horas</small></div><p>Meta de referencia personalizable</p></article>
      </div>

      <div class="grid month-grid">
        <article class="panel tarjeta">
          <div class="section-title">
            <div><h2>Tu sueño durante el mes</h2><p>Consulta y compara tus registros diarios.</p></div>
            <select id="monthSelect" class="select" aria-label="Seleccionar mes"></select>
          </div>
          <div class="table-wrap">
            <table>
              <thead><tr><th>Día</th><th>Horas</th><th>Registro</th><th>Objetivo</th></tr></thead>
              <tbody id="sleepTable"></tbody>
            </table>
          </div>
          <p class="empty-note">Los días sin registro aparecen como “Sin registrar”; no se cuentan como cero horas.</p>
        </article>

      </div>
    </section>

    <section id="page-progress" class="hidden">
      <div class="page-head"><div><div class="eyebrow">Tu evolución</div><h1>Mi progreso 📈</h1><p>Consulta tus hábitos y constancia en FamFit.</p></div></div>
      <div class="grid stats"><article class="stat-card tarjeta"><div class="stat-label">EXPERIENCIA ACUMULADA</div><div class="stat-value" id="xpProgressStat">0 <small>XP</small></div><p>Gana experiencia completando actividades.</p></article><article class="stat-card"><div class="stat-label">NOCHES REGISTRADAS</div><div class="stat-value" id="nightsProgressStat">0</div><p>Registros de sueño guardados.</p></article><article class="stat-card"><div class="stat-label">PROMEDIO DE SUEÑO</div><div class="stat-value" id="sleepProgressStat">— <small>h</small></div><p>Promedio de noches registradas.</p></article></div>
    </section>
    <section id="page-training" class="hidden"><div class="page-head"><div><div class="eyebrow">Muévete a tu ritmo</div><h1>Entrenamientos 🏋️</h1><p>Consulta rutinas y registra sesiones. Esta sección puede conectarse con tu módulo de entrenamiento.</p></div></div><div class="grid mission-grid"><article class="panel tarjeta"><div class="mission-icon">💪</div><h2>Fuerza básica</h2><p>Rutina de cuerpo completo adaptada a tu nivel.</p></article><article class="panel tarjeta"><div class="mission-icon">🚶</div><h2>Actividad ligera</h2><p>Caminar y moverte durante el día también cuenta.</p></article><article class="panel tarjeta"><div class="mission-icon">🧘</div><h2>Movilidad</h2><p>Movimientos cómodos para acompañar la recuperación.</p></article></div></section>
    <section id="page-nutrition" class="hidden"><div class="page-head"><div><div class="eyebrow">Hábitos equilibrados</div><h1>Alimentación 🥗</h1><p>Espacio preparado para registrar comidas, hidratación y preferencias alimentarias.</p></div></div><article class="panel tarjeta"><h2>Registro de alimentación</h2><p>Esta sección puede ampliarse con el diario de comidas y planificación de menús.</p></article></section>
    <section id="page-habits" class="hidden"><div class="page-head"><div><div class="eyebrow">Pequeñas acciones</div><h1>Hábitos ✅</h1><p>Construye constancia con objetivos alcanzables.</p></div></div><article class="panel tarjeta"><h2>Hábitos del día</h2><p>Próximamente: lista de hábitos configurables y seguimiento diario.</p></article></section>
    <section id="page-stats" class="hidden"><div class="page-head"><div><div class="eyebrow">Datos y tendencias</div><h1>Estadísticas 📊</h1><p>Resumen de sueño y actividades registradas.</p></div></div><div class="grid stats"><article class="stat-card tarjeta"><div class="stat-label">PROMEDIO MENSUAL DE SUEÑO</div><div class="stat-value" id="sleepStatsAvg">— <small>h</small></div></article><article class="stat-card tarjeta"><div class="stat-label">HORAS REGISTRADAS ESTE MES</div><div class="stat-value" id="sleepStatsTotal">0 <small>h</small></div></article></div></section>
    <section id="page-avatar" class="hidden"><div class="page-head"><div><div class="eyebrow">Tu compañero de aventura</div><h1>Avatares 🧍</h1><p>Personaliza tu personaje y observa su estado de energía.</p></div></div><article class="panel tarjeta" style="max-width:360px;text-align:center"><div id="avatarPreview" style="font-size:84px">😎</div><h2 id="avatarPreviewLabel">Avatar con energía</h2><p>El estado visual se adapta a las horas de sueño registradas.</p></article></section>
    <section id="page-social" class="hidden"><div class="page-head"><div><div class="eyebrow">Avanza acompañado</div><h1>Social 👥</h1><p>Retos compartidos, amigos y logros.</p></div></div><article class="panel tarjeta"><h2>Comunidad FamFit</h2><p>En esta sección podrán añadirse amigos, retos grupales y publicaciones con controles de privacidad.</p></article></section>
    <section id="page-store" class="hidden"><div class="page-head"><div><div class="eyebrow">Recompensas virtuales</div><h1>Tienda y recompensas 🛍️</h1><p>Consulta la experiencia acumulada y los objetos que podrás desbloquear.</p></div></div><article class="panel tarjeta"><h2>Tu experiencia</h2><div class="target-number" id="xpStore">0 <small>XP</small></div><p>Las recompensas son virtuales y se ganan completando hábitos saludables.</p></article></section>
    <section id="page-settings" class="hidden"><div class="page-head"><div><div class="eyebrow">Personaliza tu experiencia</div><h1>Configuración ⚙️</h1><p>Preferencias del prototipo.</p></div></div><article class="panel tarjeta"><h2>Objetivo de sueño</h2><p>La meta de demostración es de 8 horas. En una versión conectada a usuarios, esta preferencia podría personalizarse.</p></article></section>
    <section id="page-logout" class="hidden"><div class="page-head"><div><div class="eyebrow">Hasta pronto</div><h1>Cerrar sesión 🚪</h1><p>Este prototipo no tiene autenticación conectada. El cierre de sesión deberá integrarse con Laravel.</p></div></div><button class="button" id="backHomeBtn">Volver al inicio</button></section>

    <section id="page-sleep" class="hidden">
      <div class="page-head"><div><div class="eyebrow">Historial y objetivos</div><h1>Mi sueño 🌙</h1><p>Registra tus noches y observa tus hábitos a lo largo del tiempo.</p></div></div>
      <div class="grid sleep-layout">
        <article class="panel tarjeta"><h2>Registrar descanso</h2><p style="margin:8px 0 18px">Añade o corrige las horas dormidas de una fecha.</p>
          <label class="muted" for="dateInput">Fecha</label><br><input id="dateInput" type="date" class="select" style="margin:8px 0 15px">
          <br><label class="muted" for="sleepInput2">Horas dormidas</label><br><input id="sleepInput2" type="number" min="0" max="16" step=".5" value="7.5" class="select" style="margin:8px 0 15px;width:140px">
          <br><button class="button primary" id="saveDateBtn">Guardar noche</button>
        </article>
        <article class="panel tarjeta"><h2>Tu objetivo</h2><div class="target-number">7–9 <small>horas</small></div><p>Como referencia general para adultos, procura dormir al menos 7 horas y ajustar la meta a tus necesidades.</p><div class="reward"><strong>+30 XP</strong><p style="margin-top:5px">Por cumplir la meta diaria configurada. Los puntos son una recompensa virtual, no una medida de salud.</p></div></article>
      </div>
    </section>

    <section id="page-activities" class="hidden">
      <div class="page-head"><div><div class="eyebrow">Recupera cuerpo y mente</div><h1>Bienestar activo 🧘</h1><p>Completa actividades tranquilas y gana experiencia por mantener hábitos saludables.</p></div></div>
      <div class="tabs" role="tablist">
        <button class="tab active" data-tab="relax">🧘 Relajación</button>
        <button class="tab" data-tab="water">💧 Hidratación</button>
        <button class="tab" data-tab="breaks">🌿 Pausas activas</button>
      </div>
      <div id="tab-relax" class="grid mission-grid">
        <article class="panel tarjeta mission"><div class="mission-icon">🌬️</div><div><h2>Respiración consciente</h2><p>Haz una pausa de 2 minutos y respira con suavidad, sin forzar.</p></div><div class="muted">Duración: 2 min · <span class="xp">+15 XP</span></div><button class="button primary completeMission" data-id="breathing" data-xp="15">Completar actividad</button></article>
        <article class="panel tarjeta mission"><div class="mission-icon">🧘</div><div><h2>Estiramiento suave</h2><p>Realiza movimientos cómodos de cuello, hombros y espalda, sin dolor.</p></div><div class="muted">Duración: 5 min · <span class="xp">+20 XP</span></div><button class="button primary completeMission" data-id="stretch" data-xp="20">Completar actividad</button></article>
        <article class="panel tarjeta mission"><div class="mission-icon">🎧</div><div><h2>Pausa sin pantallas</h2><p>Aléjate de la pantalla, relaja la vista y escucha tu entorno.</p></div><div class="muted">Duración: 5 min · <span class="xp">+15 XP</span></div><button class="button primary completeMission" data-id="screenbreak" data-xp="15">Completar actividad</button></article>
      </div>
      <div id="tab-water" class="grid mission-grid hidden">
        <article class="panel tarjeta mission"><div class="mission-icon">💧</div><div><h2>Tu hidratación diaria</h2><p>Registra vasos de agua a lo largo del día. La necesidad varía con el clima, actividad y salud.</p></div><div class="stat-value"><span id="waterCount">0</span> <small>/ 6 vasos de referencia</small></div><div class="progress-line"><span id="waterProgress" style="width:0%"></span></div><button class="button primary" id="addWater">+ Registrar un vaso · +5 XP</button><p class="empty-note">Evita beber en exceso de golpe; distribuye el agua durante el día.</p></article>
        <article class="panel tarjeta mission"><div class="mission-icon">🚰</div><div><h2>Misión: recuerda beber</h2><p>Haz una pausa para hidratarte cuando tengas sed y acompaña tus comidas con agua.</p></div><div class="muted">Recompensa diaria · <span class="xp">+10 XP</span></div><button class="button primary" id="waterMission">Completar misión</button></article>
      </div>
      <div id="tab-breaks" class="grid mission-grid hidden">
        <article class="panel tarjeta mission"><div class="mission-icon">🌳</div><div><h2>Pausa de movimiento</h2><p>Levántate un momento, camina o cambia de postura si llevas mucho tiempo sentado.</p></div><div class="muted">Duración: 3 min · <span class="xp">+10 XP</span></div><button class="button primary completeMission" data-id="movement" data-xp="10">Completar actividad</button></article>
        <article class="panel tarjeta mission"><div class="mission-icon">👀</div><div><h2>Descanso visual</h2><p>Mira a lo lejos unos segundos y parpadea con naturalidad para descansar la vista.</p></div><div class="muted">Duración: 1 min · <span class="xp">+10 XP</span></div><button class="button primary completeMission" data-id="eyes" data-xp="10">Completar actividad</button></article>
      </div>
    </section>


  </main>
</div>
<div id="toast" class="toast" role="status" aria-live="polite"></div>
<script>
(() => {
  const TARGET = 8;
  const STORAGE_KEY = 'famfit_wellness_demo_v1';
  const today = new Date();
  const dateKey = d => `${d.getFullYear()}-${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
  let data = {};
  let xp = 0;
  let water = 0;
  let waterMissionDone = false;
  let completed = {};
  try {
    const saved = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
    data = saved.sleep || {};
    xp = saved.xp || 0;
    water = saved.waterDate === dateKey(today) ? (saved.water || 0) : 0;
    waterMissionDone = saved.waterMissionDate === dateKey(today) ? !!saved.waterMissionDone : false;
    completed = saved.completedDate === dateKey(today) ? (saved.completed || {}) : {};
  } catch(e) {}
  const persist = () => {
    try { localStorage.setItem(STORAGE_KEY, JSON.stringify({sleep:data,xp,water,waterDate:dateKey(today),waterMissionDone,waterMissionDate:dateKey(today),completed,completedDate:dateKey(today)})); } catch(e) {}
  };
  const $ = id => document.getElementById(id);
  const fmt = n => Number(n).toFixed(1).replace(/\.0$/,'');
  const todayKey = dateKey(today);
  if (!data[todayKey]) data[todayKey] = 7.5;
  $('dateInput').value = todayKey;
  $('sleepInput2').value = data[todayKey];
  $('sleepInput').value = data[todayKey];

  function toast(message) {
    $('toast').textContent = message;
    $('toast').classList.add('show');
    clearTimeout(toast.timer);
    toast.timer = setTimeout(() => $('toast').classList.remove('show'), 3000);
  }
  function updateSleepCard(hours) {
    $('hoursDisplay').textContent = fmt(hours);
    const ratio = Math.min(hours / TARGET * 100,100);
    const degrees = ratio * 3.6;
    $('sleepMeterRing').style.background = `conic-gradient(var(--blue) 0deg, var(--teal) ${degrees}deg, #1b2b43 ${degrees}deg 360deg)`;
    $('sleepMeter').textContent = `${Math.round(ratio)}%`;
    $('sleepMeterLabel').textContent = `${fmt(hours)} de ${TARGET} horas recomendadas`;
    let face, label, sub, status, color;
    if(hours < 6){face='🥱';label='Avatar agotado';sub='Necesitas recuperar energía';status='● Descanso insuficiente';color='#ff9ca4';}
    else if(hours < 7){face='😴';label='Avatar con sueño';sub='Intenta descansar más';status='● Te faltó descanso';color='#ffc477';}
    else if(hours <= 9){face='😎';label='Avatar con energía';sub='¡Buen trabajo!';status='● Buen descanso';color='#7ee6d5';}
    else {face='🙂';label='Avatar descansado';sub='Observa cómo te sientes';status='● Descanso registrado';color='#a8c5ff';}
    $('avatarFace').textContent = face;
    $('avatarLabel').textContent = label;
    $('avatarSub').textContent = sub;
    $('sleepStatus').textContent = status;
    $('sleepStatus').style.color = color;
    $('rewardStatus').textContent = hours >= TARGET ? '¡Meta alcanzada! Recompensa disponible.' : `Te faltan ${fmt(Math.max(TARGET-hours,0))} h para la recompensa.`;
    $('rewardStatus').style.color = hours >= TARGET ? '#9be76d' : '#d4e8df';
  }
  function monthDays(year, month){return new Date(year,month+1,0).getDate();}
  function renderMonthOptions(){
    const select=$('monthSelect');
    const months=[];
    for(let i=0;i<12;i++){const d=new Date(today.getFullYear(),today.getMonth()-i,1);months.push({year:d.getFullYear(),month:d.getMonth()});}
    select.innerHTML=months.map(m=>`<option value="${m.year}-${m.month}">${new Date(m.year,m.month,1).toLocaleDateString('es-CO',{month:'long',year:'numeric'})}</option>`).join('');
    select.value=`${today.getFullYear()}-${today.getMonth()}`;
  }
  function renderTable(){
    const [year,month]= $('monthSelect').value.split('-').map(Number);
    const days=monthDays(year,month);
    const tbody=$('sleepTable');
    let rows='',sum=0,count=0;
    for(let d=1;d<=days;d++){
      const dt=new Date(year,month,d);
      const key=dateKey(dt);
      const has=Object.prototype.hasOwnProperty.call(data,key);
      const hours=has?Number(data[key]):null;
      if(has){sum+=hours;count++;}
      const status=!has?'Sin registrar':hours>=TARGET?'Meta alcanzada':hours>=7?'Cerca de la meta':'Por mejorar';
      const tagClass=has&&hours>=7?'tag':has?'tag low':'muted';
      const bar=has?`<div class="mini-bar"><span style="width:${Math.min(hours/10*100,100)}%"></span></div>`:'—';
      rows+=`<tr><td class="day-cell">${String(d).padStart(2,'0')} ${dt.toLocaleDateString('es-CO',{weekday:'short'}).replace('.','')}</td><td>${has?fmt(hours)+' h':'—'}</td><td class="bar-cell">${bar}</td><td>${has?`<span class="${tagClass}">${status}</span>`:'<span class="muted">Sin registrar</span>'}</td></tr>`;
    }
    tbody.innerHTML=rows;
    $('avgStat').textContent=count?fmt(sum/count):'—';
    $('totalStat').textContent=fmt(sum);
    $('avgCaption').textContent=`Basado en ${count} noche${count===1?'':'s'} registrada${count===1?'':'s'}`;
  }
  function renderWater(){
    $('waterCount').textContent=water;
    $('waterProgress').style.width=Math.min(water/6*100,100)+'%';
    $('addWater').disabled=water>=12;
    $('addWater').textContent=water>=12?'Límite diario alcanzado':'+ Registrar un vaso · +5 XP';
    $('waterMission').disabled=waterMissionDone;
    $('waterMission').textContent=waterMissionDone?'Misión completada ✓':'Completar misión';
  }
  function renderAll(){
    updateSleepCard(Number(data[todayKey] ?? 0));
    renderTable();renderWater();persist();
  }
  function award(amount,message){xp+=amount;persist();toast(`${message} +${amount} XP. Total: ${xp} XP`);}
  document.querySelectorAll('.nav button').forEach(btn=>btn.addEventListener('click',()=>{
    document.querySelectorAll('.nav button').forEach(b=>b.classList.toggle('active',b===btn));
    document.querySelectorAll('main section').forEach(s=>s.classList.add('hidden'));
    const target = $('page-'+btn.dataset.page);
    if(target) target.classList.remove('hidden');
    if(btn.dataset.page==='progress') {
      $('xpProgressStat').innerHTML = `${xp} <small>XP</small>`;
      const entries=Object.values(data).map(Number);
      $('nightsProgressStat').textContent=entries.length;
      $('sleepProgressStat').innerHTML = `${entries.length?fmt(entries.reduce((a,b)=>a+b,0)/entries.length):'—'} <small>h</small>`;
    }
    if(btn.dataset.page==='stats') {
      const [y,m]=$('monthSelect').value.split('-').map(Number);
      const entries=Object.entries(data).filter(([k])=>{const d=new Date(k+'T12:00:00');return d.getFullYear()===y&&d.getMonth()===m;}).map(([,v])=>Number(v));
      $('sleepStatsAvg').innerHTML=`${entries.length?fmt(entries.reduce((a,b)=>a+b,0)/entries.length):'—'} <small>h</small>`;
      $('sleepStatsTotal').innerHTML=`${fmt(entries.reduce((a,b)=>a+b,0))} <small>h</small>`;
    }
    if(btn.dataset.page==='avatar') {
      $('avatarPreview').textContent=$('avatarFace').textContent;
      $('avatarPreviewLabel').textContent=$('avatarLabel').textContent;
    }
    if(btn.dataset.page==='store') $('xpStore').innerHTML=`${xp} <small>XP</small>`;
  }));
  $('sidebarToggle').addEventListener('click',()=>{
    const collapsed=document.querySelector('.app').classList.toggle('sidebar-collapsed');
    $('sidebarToggle').setAttribute('aria-expanded',String(!collapsed));
    $('sidebarToggle').innerHTML=collapsed?'☰ <span>Expandir menú</span>':'☰ <span>Contraer menú</span>';
    $('sidebarToggle').setAttribute('aria-label',collapsed?'Expandir menú':'Contraer menú');
  });
  $('goActivitiesBtn').addEventListener('click',()=>document.querySelector('[data-page="activities"]').click());
  $('backHomeBtn')?.addEventListener('click',()=>document.querySelector('[data-page="wellness"]').click());
  document.querySelectorAll('.tab').forEach(btn=>btn.addEventListener('click',()=>{
    document.querySelectorAll('.tab').forEach(b=>b.classList.toggle('active',b===btn));
    ['relax','water','breaks'].forEach(id=>$('tab-'+id).classList.toggle('hidden',id!==btn.dataset.tab));
  }));
  $('editSleepBtn').addEventListener('click',()=>$('sleepEditor').classList.toggle('hidden'));
  $('saveSleepBtn').addEventListener('click',()=>{
    const n=Number($('sleepInput').value);
    if(!Number.isFinite(n)||n<0||n>16){toast('Ingresa un valor entre 0 y 16 horas.');return;}
    data[todayKey]=n;$('sleepInput2').value=n;renderAll();toast('Registro de sueño actualizado.');
  });
  $('saveDateBtn').addEventListener('click',()=>{
    const key=$('dateInput').value;const n=Number($('sleepInput2').value);
    if(!key||!Number.isFinite(n)||n<0||n>16){toast('Revisa la fecha y las horas ingresadas.');return;}
    data[key]=n;
    if(key===todayKey)$('sleepInput').value=n;
    renderAll();toast('Noche guardada en tu historial.');
  });
  $('monthSelect').addEventListener('change',renderTable);
  $('todayBtn').addEventListener('click',()=>{document.querySelector('[data-page="wellness"]').click();toast('Estás viendo el resumen de bienestar.');});
  document.querySelectorAll('.completeMission').forEach(btn=>btn.addEventListener('click',()=>{
    const id=btn.dataset.id;
    if(completed[id]){toast('Ya completaste esta actividad hoy.');return;}
    completed[id]=true;btn.textContent='Completado ✓';btn.disabled=true;award(Number(btn.dataset.xp),'Actividad completada.');persist();
  }));
  $('addWater').addEventListener('click',()=>{
    if(water>=12)return;
    water++;xp+=5;persist();renderWater();toast('Hidratación registrada. +5 XP');
  });
  $('waterMission').addEventListener('click',()=>{
    if(waterMissionDone)return;
    waterMissionDone=true;xp+=10;persist();renderWater();toast('Misión de hidratación completada. +10 XP');
  });
  renderMonthOptions();
  renderAll();
  document.querySelectorAll('.completeMission').forEach(btn=>{if(completed[btn.dataset.id]){btn.textContent='Completado ✓';btn.disabled=true;}});
})();
</script>
</body>
</html>
