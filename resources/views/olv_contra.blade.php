<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>¿Olvidaste tu contraseña?</title>
  <link rel="stylesheet" href="{{ asset('estilos/olv_contra.css') }}">
</head>

<body>
  <div class="auth-page">
    <div class="auth-card">
      <div class="auth-icon">🔒</div>
      <h1>¿Olvidaste tu contraseña?</h1>
      <p class="auth-text">
        Escribe tu correo y te enviaremos un enlace para restablecerla.
      </p>

      @if (session('status'))
        <div class="auth-success">{{ session('status') }}</div>
      @endif

      <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <label for="email">Correo electrónico</label>
        <input
          id="email"
          type="email"
          name="email"
          value="{{ old('email') }}"
          placeholder="tucorreo@ejemplo.com"
          required
          autofocus
          class="@error('email') is-invalid @enderror"
        >
        @error('email')
          <span class="auth-error">{{ $message }}</span>
        @enderror

        <button type="submit" class="btn-primary">Enviar enlace</button>
      </form>

      <a href="{{ route('login') }}" class="auth-link">← Volver a iniciar sesión</a>
    </div>
  </div>
</body>
</html>