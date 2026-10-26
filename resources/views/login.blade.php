<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Famfit Quest | Bienestar familiar</title>
    <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/estilologin.css') }}">

</head>
<body>

    <main class="contenedor">

        <section class="tarjeta-login tarjeta">

            <!-- Panel de bienvenida -->
            <div class="panel-bienvenida">

                <div class="logo">
                    <span class="icono-logo">💚</span>
                    <h2>Famfit Quest</h2>
                </div>

                <div class="ilustracion">
                    <div class="circulo circulo-uno"></div>
                    <div class="circulo circulo-dos"></div>

                    <div class="personajes">
                        👨‍👩‍👧
                        <span class="corazon">💚</span>
                    </div>
                </div>

                <h1>¡Crezcamos juntos!</h1>

                <p class="descripcion">
                    Pequeños hábitos, grandes logros.
                    Convierte cada día en una nueva aventura
                    para cuidar de ti y de tu familia.
                </p>

                <div class="beneficios">

                    <div class="beneficio">
                        <span>🏃</span>
                        <p>Muévete cada día</p>
                    </div>

                    <div class="beneficio">
                        <span>⭐</span>
                        <p>Supera tus retos</p>
                    </div>

                    <div class="beneficio">
                        <span>👨‍👩‍👧</span>
                        <p>Comparte en familia</p>
                    </div>

                </div>

            </div>

            <!-- Panel del formulario -->
            <div class="panel-formulario">

                <div class="encabezado">
                    <span class="saludo">¡Qué bueno verte!</span>

                    <h2>Inicia sesión</h2>

                    <p>Continúa tu aventura saludable.</p>
                </div>

                <form action="{{ route('login.post') }}" method="post">
                    @csrf
                    <div class="grupo-input grupo-formulario">
                        <label class="etiqueta" for="correo">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            class="campo"
                            id="correo"
                            name="email"
                            placeholder="Ingresa tu correo"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="grupo-input grupo-formulario">
                        <label class="etiqueta" for="contrasena">
                            Contraseña
                        </label>

                        <input
                            type="password"
                            class="campo"
                            id="contrasena"
                            name="password"
                            placeholder="Ingresa tu contraseña"
                            autocomplete="current-password"
                            required
                        >
                    </div>

                    <div class="opciones">

                        <label class="recordar">
                            <input type="checkbox" name="recordar">
                            Recordarme
                        </label>

                        <a href="{{ route('olv_contra') }}">¿Olvidaste tu contraseña?</a>

                    </div>

                    <button type="submit" class="boton-login boton boton-azul">
                        Iniciar sesión
                        <span>→</span>
                    </button>

                </form>

                <div class="separador">
                    <span>¡Tu bienestar comienza aquí!</span>
                </div>

                <p class="registro">
                    ¿No tienes una cuenta?
                    <a href="{{ route('registro') }}">Regístrate aquí</a>
                </p>

                <div class="mensaje-inferior">
                    🌱 Cada pequeño paso cuenta.
                </div>

            </div>

        </section>

        <footer>
            <p>
                © 2026 Famfit Quest |
                Bienestar para toda la familia 💚
            </p>
        </footer>

    </main>

</body>
</html>