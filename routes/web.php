<?php

use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\FamFitCalendarController;
use App\Http\Controllers\CommunityController;
use App\Http\Controllers\QuestController;
use App\Http\Controllers\OnboardingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use App\Http\Controllers\AvatarController;
use App\Http\Controllers\Auth\PasswordResetController;
use App\Models\Routine;
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
            
            // 🔥 AQUÍ ESTÁ LA MAGIA 🔥
            // Preguntamos: ¿Este usuario ya tiene un test guardado?
            if (!Auth::user()->currentAssessment) {
                // Si NO tiene, es su primera vez. Lo mandamos a hacer el test.
                return redirect()->intended(route('onboarding')); 
            }

            // Si SÍ tiene test, lo mandamos directo al menú principal.
            return redirect()->intended(route('menu'));
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

// Recuperación de contraseña
Route::middleware('guest')->group(function () {
    Route::get('/forgot-password', [PasswordResetController::class, 'create'])
        ->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'store'])
        ->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'edit'])
        ->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'update'])
        ->name('password.update');
});

// Rutas públicas adicionales que tenías
Route::get('/progreso', function () {
    return view('progreso');
})->name('progreso');

Route::get('/entrenamientos', function () {
    return view('entrenamientos');
})->name('entrenamientos');



Route::get('/entrenamientos/{entorno}', function ($entorno) {
    /** @var \App\Models\User $user */
    $user = Auth::user();
    $perfil = $user->profile;
    
    // 1. Traducir el objetivo del test (en español) al de la base de datos (en inglés)
    $objetivoDb = 'general_fitness'; // Por defecto
    if ($perfil && $perfil->goals) {
        $objetivoDb = match($perfil->goals) {
            'ganar_musculo' => 'strength',
            'perder_grasa' => 'endurance',
            'resistencia_cardio' => 'cardio',
            'flexibilidad_movilidad' => 'mobility',
            default => 'general_fitness',
        };
    }

    // 2. Traducir el entorno por si acaso (gym, casa, aire)
    $entornoDb = match($entorno) {
        'gym' => 'gym',
        'casa' => 'home',
        'aire' => 'outdoor',
        default => 'home'
    };

    // 3. Buscar las rutinas de ese entorno
    $todasLasRutinas = Routine::where('environment', $entornoDb)->get();

    // 4. Filtrar las rutinas recomendadas (según el objetivo del test)
    $rutinasRecomendadas = Routine::where('environment', $entornoDb)
                                  ->where('objective', $objetivoDb)
                                  ->get();

    return view('rutinas', compact('entorno', 'perfil', 'todasLasRutinas', 'rutinasRecomendadas', 'objetivoDb'));
})->name('entrenamientos.rutinas');

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Solo para usuarios logueados)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {

       // 🛡️ GUARDIA DE SEGURIDAD: Si no ha hecho el test, lo obligamos a hacerlo
    Route::get('/menu', function (Request $request) { // <-- Agregamos Request aquí
        if (!Auth::user()->currentAssessment) {
            return redirect()->route('test')->with('info', 'Debes completar tu test inicial primero.');
        }
        // Si ya hizo el test, cargamos el controlador pasándole el $request
        return app(QuestController::class)->home($request); // <-- Se lo pasamos aquí
    })->name('menu');

    // El resto de rutas (sin duplicar el menú)
    Route::get('/entreno', [QuestController::class, 'training'])->name('entreno');
    Route::post('/entreno/rutinas', [QuestController::class, 'storeRoutine'])->name('entreno.rutinas.store');
    Route::post('/entreno/ejercicios/{exercise}/completar', [QuestController::class, 'completeExercise'])->name('entreno.ejercicios.complete');
    Route::get('/calendario', [FamFitCalendarController::class, 'index'])->name('calendario');
    Route::get('/social', [CommunityController::class, 'index'])->name('social');
    Route::get('/social/personas', [CommunityController::class, 'people'])->name('social.people');
    Route::post('/social/amistades', [CommunityController::class, 'update'])->name('social.friendship');

    // Calendario compartido FamFit
    Route::get('/famfit/calendario', [FamFitCalendarController::class, 'index'])->name('famfit.calendar');
    Route::put('/famfit/calendario/disponibilidad', [FamFitCalendarController::class, 'updateAvailability'])->name('famfit.availability.update');
    Route::post('/famfit/actividades', [FamFitCalendarController::class, 'store'])->name('famfit.activities.store');
    Route::put('/famfit/actividades/{activity}', [FamFitCalendarController::class, 'update'])->name('famfit.activities.update');
    Route::delete('/famfit/actividades/{activity}', [FamFitCalendarController::class, 'destroy'])->name('famfit.activities.destroy');

    // Perfil y Configuración
    Route::get('/perfil', [QuestController::class, 'profile'])->name('perfil');
    Route::put('/perfil', [QuestController::class, 'updateProfile'])->name('perfil.update');
    Route::put('/avatar', [QuestController::class, 'updateAvatar'])->name('avatar.update');
    Route::get('/avatar', [AvatarController::class, 'editar'])->name('avatar.editar');
    Route::post('/avatar', [AvatarController::class, 'guardar'])->name('avatar.guardar');

    Route::get('/configuracion', [QuestController::class, 'settings'])->name('configuracion');
    Route::put('/configuracion', [QuestController::class, 'updateSettings'])->name('configuracion.update');
    Route::put('/configuracion/tema', [QuestController::class, 'updateTheme'])->name('configuracion.theme');
    Route::put('/configuracion/contrasena', [QuestController::class, 'updatePassword'])->name('configuracion.password');

    // Test Inicial y Onboarding (Asegúrate de usar el controlador aquí para pasar la variable $perfil)
    Route::get('/test', [OnboardingController::class, 'index'])->name('test');

    // Rutas protegidas para que no repita el test
    Route::get('/onboarding', function () {
        /** @var \App\Models\User $user */ // 👈 Esto le dice a VS Code que es un User
        $user = Auth::user(); 

        // Si el usuario ya tiene un test, Y NO viene con la intención de repetirlo, lo sacamos.
        if ($user->currentAssessment && !request()->has('retake')) {
            return redirect()->route('menu')->with('info', 'Ya completaste el test inicial.');
        }
        
        // Si no tiene test, o si viene con el ?retake=1, lo dejamos pasar
        return app(OnboardingController::class)->index();
    })->name('onboarding');
    
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

