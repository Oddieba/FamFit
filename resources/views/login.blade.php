<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#F5F7FB">

    <title>Iniciar sesión | FamFit Quest</title>
    <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
    <link rel="stylesheet" href="{{ asset('estilos/estilologin.css') }}">

</head>
<body>

    <main class="contenedor">

        <section class="tarjeta-login tarjeta">

            <!-- Panel de bienvenida -->
            <div class="panel-bienvenida">

                <div class="logo">
                    <span class="icono-logo" aria-hidden="true">F</span>
                    <h2>FamFit Quest</h2>
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

                @if ($errors->any())
                    <div class="login-alerta login-alerta-error" role="alert">
                        {{ $errors->first() }}
                    </div>
                @endif

                @if (session('status'))
                    <div class="login-alerta login-alerta-exito" role="status">
                        {{ session('status') }}
                    </div>
                @endif

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
                            value="{{ old('email') }}"
                            autocomplete="email"
                            required
                        >
                    </div>

                    <div class="grupo-input grupo-formulario">
                        <label class="etiqueta" for="contrasena">
                            Contraseña
                        </label>

                        <div class="campo-con-boton">
                            <input
                                type="password"
                                class="campo"
                                id="contrasena"
                                name="password"
                                placeholder="Ingresa tu contraseña"
                                autocomplete="current-password"
                                required
                            >
                            <button
                                class="ver-clave"
                                type="button"
                                data-objetivo="contrasena"
                                aria-label="Mostrar contraseña"
                                aria-pressed="false"
                            >
                                <svg class="icono-ver" viewBox="0 0 24 24" aria-hidden="true"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                                <svg class="icono-ocultar" viewBox="0 0 24 24" aria-hidden="true"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                            </button>
                        </div>
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

    <script>
        document.querySelectorAll(".ver-clave").forEach(function (boton) {
            boton.addEventListener("click", function () {
                var input = document.getElementById(boton.getAttribute("data-objetivo"));
                var mostrar = input.type === "password";

                input.type = mostrar ? "text" : "password";
                boton.setAttribute("aria-label", mostrar ? "Ocultar contraseña" : "Mostrar contraseña");
                boton.setAttribute("aria-pressed", mostrar ? "true" : "false");
                boton.classList.toggle("mostrando", mostrar);
            });
        });
    </script>

</body>
</html>