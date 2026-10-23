<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi progreso | Famfit Quest</title>
    <link rel="stylesheet" href="{{ asset('estilos/progreso.css') }}">
</head>
<body>
    <div class="progress-app">
        <aside class="sidebar">
            <a class="brand" href="{{ route('menu') }}">
                <span class="brand-mark">💚</span>
                <span class="brand-name">Famfit <span>Quest</span></span>
            </a>
            <p class="nav-label">Espacio familiar</p>
            <nav class="nav-list" aria-label="Navegación principal">
                <a class="nav-link" href="{{ route('menu') }}"><span class="nav-icon">⌂</span>Menú principal</a>
                <a class="nav-link" href="{{ route('bienestar') }}"><span class="nav-icon">♡</span>Bienestar</a>
                <a class="nav-link active" href="{{ route('progreso') }}" aria-current="page"><span class="nav-icon">▥</span>Mi progreso</a>
               
                
            </nav>
            <div class="sidebar-note">
                <strong>¡Vas por buen camino!</strong>
                Cada pequeño hábito suma para cuidar de ti y de tu familia.
            </div>
        </aside>

        <main class="main-content">
            <header class="topbar">
                <div class="breadcrumb">Famfit Quest <span aria-hidden="true">/</span> <strong>Mi progreso</strong></div>
                <div class="profile">
                    <span class="notification" aria-label="Notificaciones">♧</span>
                    <span class="profile-avatar" aria-hidden="true">😊</span>
                    <span class="profile-name">Mi perfil</span>
                </div>
            </header>

            <section class="page-heading" aria-labelledby="page-title">
                <div>
                    <p class="eyebrow">Tu camino saludable</p>
                    <h1 id="page-title">Mi progreso</h1>
                    <p class="intro">Mira todo lo que has conseguido con tus hábitos saludables.</p>
                </div>
                <div class="period-switch" aria-label="Periodo de progreso">
                    <button class="period-button" type="button" aria-pressed="false">Semana</button>
                    <button class="period-button" type="button" aria-pressed="true">Mes</button>
                    <button class="period-button" type="button" aria-pressed="false">Año</button>
                </div>
            </section>

            <p class="demo-note"><span aria-hidden="true">●</span> Resumen de ejemplo: tus avances reales aparecerán aquí cuando registres tus actividades.</p>

            <section class="summary-grid" aria-label="Resumen del mes">
                <article class="card summary-card">
                    <div class="summary-top"><span class="summary-label">Días activos</span><span class="summary-icon">📅</span></div>
                    <p class="summary-value">18 <small>/ 30 días</small></p>
                    <div class="summary-foot"><strong>+4 días</strong> respecto al mes pasado</div>
                </article>
                <article class="card summary-card">
                    <div class="summary-top"><span class="summary-label">Hábitos completados</span><span class="summary-icon">✅</span></div>
                    <p class="summary-value">42 <small>hábitos</small></p>
                    <div class="summary-foot"><strong>+12%</strong> respecto al mes pasado</div>
                </article>
                <article class="card summary-card">
                    <div class="summary-top"><span class="summary-label">Tiempo en movimiento</span><span class="summary-icon">🏃</span></div>
                    <p class="summary-value">9.5 <small>horas</small></p>
                    <div class="summary-foot"><strong>+1.5 h</strong> respecto al mes pasado</div>
                </article>
                <article class="card summary-card">
                    <div class="summary-top"><span class="summary-label">Racha actual</span><span class="summary-icon">🔥</span></div>
                    <p class="summary-value">6 <small>días</small></p>
                    <div class="summary-foot">Tu mejor racha: <strong>12 días</strong></div>
                </article>
            </section>

            <section class="content-grid" aria-label="Actividad y constancia">
                <article class="card chart-card">
                    <div class="card-heading">
                        <div><h2>Tu actividad</h2><p>Minutos de actividad por semana</p></div>
                        <span class="activity-total">Este mes</span>
                    </div>
                    <div class="legend" aria-label="Leyenda del gráfico">
                        <span class="legend-item"><span class="legend-dot"></span>Actividad</span>
                        <span class="legend-item"><span class="legend-dot blue"></span>Meta semanal</span>
                    </div>
                    <div class="chart-wrap">
                        <svg viewBox="0 0 600 211" role="img" aria-label="Gráfico de actividad: tendencia ascendente durante cuatro semanas">
                            <defs>
                                <linearGradient id="chart-fill" x1="0" x2="0" y1="0" y2="1">
                                    <stop offset="0%" stop-color="#55a978" stop-opacity=".22" />
                                    <stop offset="100%" stop-color="#55a978" stop-opacity="0" />
                                </linearGradient>
                            </defs>
                            <line class="chart-grid-line" x1="42" y1="28" x2="590" y2="28" />
                            <line class="chart-grid-line" x1="42" y1="76" x2="590" y2="76" />
                            <line class="chart-grid-line" x1="42" y1="124" x2="590" y2="124" />
                            <line class="chart-grid-line" x1="42" y1="172" x2="590" y2="172" />
                            <text class="chart-label" x="4" y="31">180</text>
                            <text class="chart-label" x="4" y="79">120</text>
                            <text class="chart-label" x="10" y="127">60</text>
                            <text class="chart-label" x="20" y="175">0</text>
                            <path class="chart-area" d="M55 137 L185 112 L315 120 L445 72 L575 49 L575 172 L55 172 Z" />
                            <path class="chart-line" d="M55 137 L185 112 L315 120 L445 72 L575 49" />
                            <circle class="chart-point" cx="55" cy="137" r="4" />
                            <circle class="chart-point" cx="185" cy="112" r="4" />
                            <circle class="chart-point" cx="315" cy="120" r="4" />
                            <circle class="chart-point" cx="445" cy="72" r="4" />
                            <circle class="chart-point" cx="575" cy="49" r="4" />
                            <text class="chart-label" x="45" y="199">Sem. 1</text>
                            <text class="chart-label" x="175" y="199">Sem. 2</text>
                            <text class="chart-label" x="305" y="199">Sem. 3</text>
                            <text class="chart-label" x="435" y="199">Sem. 4</text>
                        </svg>
                    </div>
                </article>

                <article class="card activity-card">
                    <div class="card-heading"><div><h2>Constancia semanal</h2><p>Actividad de los últimos 7 días</p></div></div>
                    <div class="activity-summary"><strong>5</strong><span>días activos</span></div>
                    <div class="activity-bars" aria-label="Actividad por día">
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 62%"></div></div><span>L</span></div>
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 86%"></div></div><span>M</span></div>
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 48%"></div></div><span>X</span></div>
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 72%"></div></div><span>J</span></div>
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 95%"></div></div><span>V</span></div>
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 28%"></div></div><span>S</span></div>
                        <div class="activity-bar-column"><div class="activity-bar-track"><div class="activity-bar" style="height: 8%"></div></div><span>D</span></div>
                    </div>
                    <p class="activity-foot">¡Buen trabajo! Estuviste activo la mayoría de los días.</p>
                </article>
            </section>

            <section class="lower-grid" aria-label="Hábitos, logros y nivel">
                <article class="card habits-card">
                    <div class="card-heading"><div><h2>Mis hábitos</h2><p>Avance de tus objetivos semanales</p></div></div>
                    <div class="habit-list">
                        <div class="habit-row"><span class="habit-emoji">💧</span><div><span class="habit-name">Tomar agua</span><div class="progress-track"><div class="progress-fill" style="width: 78%"></div></div></div><span class="habit-value">11/14</span></div>
                        <div class="habit-row"><span class="habit-emoji">🏃</span><div><span class="habit-name">Moverme cada día</span><div class="progress-track"><div class="progress-fill" style="width: 64%"></div></div></div><span class="habit-value">9/14</span></div>
                        <div class="habit-row"><span class="habit-emoji">🥦</span><div><span class="habit-name">Comer saludable</span><div class="progress-track"><div class="progress-fill" style="width: 86%"></div></div></div><span class="habit-value">12/14</span></div>
                        <div class="habit-row"><span class="habit-emoji">😴</span><div><span class="habit-name">Dormir bien</span><div class="progress-track"><div class="progress-fill" style="width: 57%"></div></div></div><span class="habit-value">8/14</span></div>
                    </div>
                    <p class="habit-caption">Sigue así: cada repetición te acerca a tu objetivo.</p>
                </article>

                <article class="card achievements-card">
                    <div class="card-heading"><div><h2>Mis logros</h2><p>Reconocimientos de tu aventura</p></div></div>
                    <div class="achievement-list">
                        <div class="achievement"><span class="achievement-icon">🔥</span><div class="achievement-copy"><strong>Constancia</strong><span>Completa hábitos 5 días seguidos</span></div><span class="achievement-status">Obtenido</span></div>
                        <div class="achievement"><span class="achievement-icon">💧</span><div class="achievement-copy"><strong>Hidratación</strong><span>Toma agua 10 días este mes</span></div><span class="achievement-status">Obtenido</span></div>
                        <div class="achievement"><span class="achievement-icon locked">⭐</span><div class="achievement-copy"><strong>Gran explorador</strong><span>Completa 50 actividades</span></div><span class="achievement-status pending">42/50</span></div>
                    </div>
                </article>

                <article class="card avatar-card">
                    <div class="avatar-head"><h2>Mi aventura</h2><span class="level-tag">NIVEL 4</span></div>
                    <div class="avatar-illustration" aria-label="Personaje de la aventura" role="img">🧑‍🚀</div>
                    <div class="avatar-stat"><span>Experiencia</span><strong>680 / 1000 XP</strong></div>
                    <div class="avatar-xp" role="progressbar" aria-label="Experiencia para el siguiente nivel" aria-valuenow="68" aria-valuemin="0" aria-valuemax="100"><span></span></div>
                    <div class="avatar-stat"><span>Próximo nivel</span><strong>320 XP restantes</strong></div>
                    <p class="avatar-caption">Completa tus hábitos para ganar experiencia y avanzar.</p>
                </article>
            </section>

            <p class="footer-note">Cada pequeño paso cuenta. ¡Sigue cuidando de ti! 💚</p>
        </main>
    </div>
</body>
</html>