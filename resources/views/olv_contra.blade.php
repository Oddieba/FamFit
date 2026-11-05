<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>¿Olvidaste tu contraseña?</title>
  <script src="{{ asset('js/theme.js') }}"></script>
  <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
  <link rel="stylesheet" href="{{ asset('estilos/tema-oscuro.css') }}">
</head>

<body>
  <main class="contenedor contenido">
    <section class="tarjeta tarjeta-principal" style="max-width: 480px; margin: 0 auto;">
      <h1>🔒 ¿Olvidaste tu contraseña?</h1>
      <p class="texto-secundario">
        Escribe tu correo y te enviaremos un enlace para restablecerla.
      </p>

      @if (session('status'))
        <div class="alerta alerta-exito" role="status">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="grupo-formulario">
          <label class="etiqueta" for="email">Correo electrónico</label>
          <input
            class="campo @error('email') campo-error @enderror"
            id="email"
            type="email"
            name="email"
            value="{{ old('email') }}"
            placeholder="tucorreo@ejemplo.com"
            autocomplete="email"
            required
            autofocus
            @error('email') aria-invalid="true" aria-describedby="email-error" @enderror
          >
          @error('email')
            <span class="mensaje-error" id="email-error">{{ $message }}</span>
          @enderror
        </div>

        <button type="submit" class="boton boton-azul boton-bloque">Enviar enlace</button>
      </form>

      <p style="margin-top: 20px; margin-bottom: 0;">
        <a href="{{ route('login') }}">← Volver a iniciar sesión</a>
      </p>
    </section>
  </main>
</body>
</html>