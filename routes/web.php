<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\OnboardingController;
use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\PasswordResetController;
/*
|--------------------------------------------------------------------------
| Rutas Públicas (No requieren iniciar sesión)
|--------------------------------------------------------------------------
*/

// Página de inicio (Muestra el login)
Route::get('/', function () {
    return view('login');
})->name('login');

// Procesar el inicio de sesión
Route::post('/login', function (Request $request) {
    // 1. Validamos los datos
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    // 2. Intentamos iniciar sesión
    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        return redirect()->intended(route('test')); // Si funciona, va al test
    }

    // 3. Si falla, regresa con error
    return back()->withErrors([
        'email' => 'El correo o la contraseña son incorrectos.',
    ])->onlyInput('email');
})->name('login.post');

// Cerrar sesión
Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect()->route('login');
})->name('logout');

// Registro
Route::get('/registro', function () {
    return view('registro');
})->name('registro');

Route::post('/registro', [RegisteredUserController::class, 'store'])
    ->name('registro.store');

// Recuperar contraseña
Route::get('/forgot-password', function () {
    return view('forgot-password');
})->name('forgot-password');



// Mostrar formulario de restablecimiento
Route::get('/reset-password/{token}', function (string $token, Request $request) {
    return view('restablecer', [
        'token' => $token,
        'email' => $request->query('email'),
    ]);
})->name('password.reset');

// Guardar nueva contraseña
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
        return redirect()
            ->route('login')
            ->with('status', 'Tu contraseña se actualizó correctamente. Ya puedes iniciar sesión.');
    }

    return back()
        ->withInput($request->only('email'))
        ->withErrors(['email' => __($status)]);
})->name('password.update');

// Rutas públicas adicionales que tenías
Route::get('/progreso', function () {
    return view('progreso');
})->name('progreso');

Route::get('/entrenamientos', function () {
    return view('entrenamientos');
})->name('entrenamientos');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Solo para usuarios logueados)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

    // Menú y funciones principales de FamFit Quest
    Route::get('/menu', [QuestController::class, 'home'])->name('menu');
    Route::get('/entreno', [QuestController::class, 'training'])->name('entreno');
    Route::post('/entreno/rutinas', [QuestController::class, 'storeRoutine'])->name('entreno.rutinas.store');
    Route::post('/entreno/ejercicios/{exercise}/completar', [QuestController::class, 'completeExercise'])->name('entreno.ejercicios.complete');
    Route::get('/calendario', [QuestController::class, 'calendar'])->name('calendario');
    Route::get('/social', [QuestController::class, 'social'])->name('social');

    // Perfil y Configuración
    Route::get('/perfil', [QuestController::class, 'profile'])->name('perfil');
    Route::put('/perfil', [QuestController::class, 'updateProfile'])->name('perfil.update');
    Route::put('/avatar', [QuestController::class, 'updateAvatar'])->name('avatar.update');
    
    Route::get('/configuracion', [QuestController::class, 'settings'])->name('configuracion');
    Route::put('/configuracion', [QuestController::class, 'updateSettings'])->name('configuracion.update');
    Route::put('/configuracion/tema', [QuestController::class, 'updateTheme'])->name('configuracion.theme');
    Route::put('/configuracion/contrasena', [QuestController::class, 'updatePassword'])->name('configuracion.password');

    // Test Inicial y Onboarding
    Route::get('/test', function () {
        return view('test');
    })->name('test');

    Route::get('/onboarding', [OnboardingController::class, 'index'])->name('onboarding');
    Route::post('/onboarding/guardar', [OnboardingController::class, 'store'])->name('onboarding.guardar');

    // Eliminar cuenta
    Route::delete('/cuenta', function (Request $request) {
        $request->validate([
            'current_password' => ['required', 'current_password'],
        ], [
            'current_password.required' => 'Escribe tu contraseña para confirmar la eliminación.',
            'current_password.current_password' => 'La contraseña ingresada no es correcta.',
        ]);

        $usuario = $request->user();

        DB::transaction(function () use ($usuario): void {
            if (Schema::hasTable('password_reset_tokens')) {
                DB::table('password_reset_tokens')->where('email', $usuario->email)->delete();
            }
            DB::table('users')->where('id', $usuario->id)->delete();
        });

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    })->name('cuenta.destroy');
});
Route::get('/forgot-password', [PasswordResetController::class, 'create'])
    ->name('password.request');

Route::post('/forgot-password', [PasswordResetController::class, 'store'])
    ->name('password.email');

Route::get('/reset-password/{token}', [
    PasswordResetController::class,
    'edit'
])->name('password.reset');

Route::post('/reset-password', [
    PasswordResetController::class,
    'update'
])->name('password.update');