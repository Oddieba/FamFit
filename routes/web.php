<?php
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/logout', function (Request $request) {
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::get('/menu', function() {
    return view('menu');
})->name('menu');

// Ruta para procesar el login manual
Route::post('/login', function (Request $request) {
    // 1. Definimos los datos fijos que tú quieres exigir
    $usuarioCorrecto = "1@famfit.com";
    $passwordCorrecto = "1";

    // 2. Comparamos lo que escribió el usuario con tus datos fijos
    if ($request->input('email') === $usuarioCorrecto && $request->input('password') === $passwordCorrecto) {
        // Si coinciden, los dejamos pasar (por ejemplo, los mandamos al menú)
        return redirect()->route('menu'); // O la ruta a donde quieras enviarlos
    }

    // Si fallan, los regresamos al login con un error
    return back()->withErrors(['email' => 'Las credenciales son incorrectas.']);
})->name('login.post');

route::get('/bienestar', function() {
    return view('bienestar');
})->name('bienestar');

route::get('/progreso', function() {
    return view('progreso');
})->name('progreso');

Route::get('/registro', function () {
    return view('registro');
})->name('registro');

Route::get('/olv_contra', function () {
    return view('olv_contra');
})->name('olv_contra');

Route::post('/forgot-password', function (Request $request) {
    $request->validate(['email' => ['required', 'email']]);

    $status = Password::sendResetLink($request->only('email'));

    if ($status === Password::RESET_THROTTLED) {
        return back()->withErrors(['email' => __($status)]);
    }

    return back()->with('status', 'Si existe una cuenta con ese correo, recibirás un enlace para restablecer tu contraseña.');
})->name('password.email');

Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return view('restablecer', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->name('password.reset');

Route::post('/reset-password', function (Request $request) {
    $request->validate([
        'token' => ['required'],
        'email' => ['required', 'email'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ]);

    $status = Password::reset(
        $request->only('email', 'password', 'password_confirmation', 'token'),
        function (User $user, string $password): void {
            $user->forceFill([
                'password' => Hash::make($password),
                'remember_token' => Str::random(60),
            ])->save();

            event(new PasswordReset($user));
        }
    );

    if ($status === Password::PASSWORD_RESET) {
        return redirect()->route('login')->with('status', 'Tu contraseña se actualizó correctamente. Ya puedes iniciar sesión.');
    }

    return back()->withInput($request->only('email'))->withErrors(['email' => __($status)]);
})->name('password.update');

route::get('/entrenamientos', function() {
    return view('entrenamientos');
})->name('entrenamientos');