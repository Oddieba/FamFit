<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Crear una nueva contraseña</title>
  <link rel="stylesheet" href="{{ asset('estilos/general.css') }}">
</head>

<body>
  <main class="container page-content flex-center" style="min-height: 100vh;">
    <section class="card mx-auto" style="width: min(100%, 480px);">
      <div class="empty-state-icon" aria-hidden="true">🔒</div>
      <h1 class="text-center">Crear una nueva contraseña</h1>
      <p class="text-secondary text-center">Elige una contraseña segura para volver a tu cuenta.</p>

      <form method="POST" action="{{ route('password.update') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $token }}">

        <div class="form-group">
          <label class="form-label" for="email">Correo electrónico</label>
          <input
            class="form-control @error('email') is-invalid @enderror"
            id="email"
            type="email"
            name="email"
            value="{{ old('email', $email) }}"
            autocomplete="email"
            required
          >
          @error('email')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="password">Nueva contraseña</label>
          <input
            class="form-control @error('password') is-invalid @enderror"
            id="password"
            type="password"
            name="password"
            autocomplete="new-password"
            required
          >
          @error('password')
            <span class="form-error">{{ $message }}</span>
          @enderror
        </div>

        <div class="form-group">
          <label class="form-label" for="password_confirmation">Confirmar contraseña</label>
          <input
            class="form-control"
            id="password_confirmation"
            type="password"
            name="password_confirmation"
            autocomplete="new-password"
            required
          >
        </div>

        <button type="submit" class="btn btn-primary btn-block">Guardar contraseña</button>
      </form>

      <p class="text-center mt-4 mb-0">
        <a href="{{ route('login') }}">Volver a iniciar sesión</a>
      </p>
    </section>
  </main>
</body>
</html>