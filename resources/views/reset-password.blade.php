<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer contraseña | FamFit Quest</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #F5F7FB;
            padding: 20px;
        }

        .reset-container {
            width: 100%;
            max-width: 420px;
            background: #FFFFFF;
            padding: 35px;
            border-radius: 18px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
        }

        .logo {
            text-align: center;
            color: #2864F0;
            font-size: 28px;
            font-weight: bold;
            margin-bottom: 12px;
        }

        h2 {
            text-align: center;
            color: #263238;
            margin-bottom: 10px;
        }

        .description {
            text-align: center;
            color: #777;
            font-size: 14px;
            margin-bottom: 25px;
            line-height: 1.5;
        }

        .form-group {
            margin-bottom: 18px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #333;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #DDE2EB;
            border-radius: 9px;
            outline: none;
            font-size: 14px;
        }

        input:focus {
            border-color: #2864F0;
            box-shadow: 0 0 0 3px rgba(40, 100, 240, 0.1);
        }

        .btn-reset {
            width: 100%;
            padding: 14px;
            background: #2864F0;
            color: white;
            border: none;
            border-radius: 9px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.2s;
        }

        .btn-reset:hover {
            background: #1D4FC5;
        }

        .back-login {
            display: block;
            text-align: center;
            margin-top: 22px;
            color: #2864F0;
            text-decoration: none;
            font-size: 14px;
        }

        .error {
            color: #DC2626;
            font-size: 13px;
            margin-top: 6px;
        }

        .success {
            background: #E8F8E5;
            color: #287A32;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-size: 14px;
        }
    </style>
</head>

<body>

    <div class="reset-container">

        <div class="logo">FamFit Quest</div>

        <h2>Nueva contraseña</h2>

        <p class="description">
            Crea una contraseña nueva y segura para recuperar el acceso a tu cuenta.
        </p>

        @if (session('status'))
            <div class="success">
                {{ session('status') }}
            </div>
        @endif

        <form method="POST" action="{{ route('password.update') }}">
            @csrf

            <input type="hidden" name="token" value="{{ $token }}">

            <div class="form-group">
                <label for="email">Correo electrónico</label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    value="{{ old('email', $email) }}"
                    required
                    autocomplete="email"
                >

                @error('email')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Nueva contraseña</label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Mínimo 8 caracteres"
                >

                @error('password')
                    <p class="error">{{ $message }}</p>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">
                    Confirmar nueva contraseña
                </label>

                <input
                    type="password"
                    id="password_confirmation"
                    name="password_confirmation"
                    required
                    minlength="8"
                    autocomplete="new-password"
                    placeholder="Repite tu contraseña"
                >
            </div>

            <button type="submit" class="btn-reset">
                Cambiar contraseña
            </button>
        </form>

        <a href="{{ route('login') }}" class="back-login">
            Volver al inicio de sesión
        </a>

    </div>

</body>
</html>