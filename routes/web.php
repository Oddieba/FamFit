<?php
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use App\Http\Controllers\OnboardingController;
use Illuminate\Support\Facades\Auth;

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

route::get('/test', function() {
    return view('test');
})->name('test');


Route::post('/login', function (Request $request) {
    // 1. Validamos que el usuario llene los campos correctamente
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Auth::attempt busca al usuario y verifica la contraseña encriptada automáticamente
    if (Auth::attempt($credentials)) {
        // 3. Regeneramos la sesión por seguridad (evita ataques de fijación de sesión)
        $request->session()->regenerate();
        
        // 4. Lo mandamos a la ruta 'test' (puedes cambiar esto por tu ruta de inicio)
        return redirect()->intended(route('test'));
    }

    // 5. Si los datos están mal, regresamos con un error
    return back()->withErrors([
        'email' => 'El correo o la contraseña son incorrectos.',
    ])->onlyInput('email'); // 'onlyInput' mantiene el correo escrito para que no lo vuelvan a teclear
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

Route::get('/onboarding', [OnboardingController::class, 'index'])
    ->middleware(['auth'])
    ->name('onboarding');

Route::post('/onboarding/guardar', [OnboardingController::class, 'store'])
    ->middleware(['auth'])
    ->name('onboarding.guardar');