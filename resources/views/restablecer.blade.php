<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear una nueva contraseña</title>
  <script src="{{ asset('js/theme.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
  <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">
</head>

<body>
  <main class="contenedor contenido">
    <section class="tarjeta" style="max-width: 480px; margin: 0 auto;">
      <h1 class="tarjeta-titulo">Crear una nueva contraseña</h1>
      <p class="texto-secundario">Elige una contraseña segura para volver a tu cuenta.</p>

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="grupo-formulario">
          <label class="etiqueta" for="email">Correo electrónico</label>
          <input
            class="campo @error('email') campo-error @enderror"
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $email) }}"
            autocomplete="email"
            required
          >
          @error('email')
            <span class="mensaje-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="grupo-formulario">
          <label class="etiqueta" for="password">Nueva contraseña</label>
          <input
            class="campo @error('password') campo-error @enderror"
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
            required
          >
          @error('password')
            <span class="mensaje-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="grupo-formulario">
          <label class="etiqueta" for="password_confirmation">Confirmar contraseña</label>
          <input
            class="campo"
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            required
          >
        </div>

        <button type="submit" class="boton boton-azul boton-bloque">Guardar contraseña</button>
      </form>

      <p style="margin-top: 20px; margin-bottom: 0;">
        <a href="{{ route('login') }}">Volver a iniciar sesión</a>
      </p>
    </section>
  </main>
</body>
</html>