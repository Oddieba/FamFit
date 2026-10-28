<?php
use App\Models\User;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('login');
})->name('login');

Route::post('/logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->name('logout');

Route::get('/menu', function() {
    return view('menu');
})->middleware('auth')->name('menu');

//credenciales en base de datos
Route::post('/login', function (Request $request) {

    $credenciales = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required'],
    ]);

    if (Auth::attempt($credenciales)) {
        // Regenerar la sesión por seguridad
        $request->session()->regenerate();

        // Entrar al menú
        return redirect()->route('menu');
    }

    // Si las credenciales son incorrectas
    return back()
        ->withErrors([
            'email' => 'El correo o la contraseña son incorrectos.',
        ])
        ->onlyInput('email');

})->name('login.post');

route::get('/bienestar', function() {
    return view('bienestar');
})->name('bienestar');



Route::get('/registro', function () {
    return view('registro');
})->name('registro');

Route::post('/registro', [RegisteredUserController::class, 'store'])
    ->name('registro.store');

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
Route::get('configuracion', function () {
    return view('configuracion');
})->middleware('auth')->name('configuracion');

Route::delete('/cuenta', function (Request $request) {
    $usuario = $request->user();

    DB::transaction(function () use ($usuario): void {
        DB::table('password_reset_tokens')->where('email', $usuario->email)->delete();
        DB::table('users')->where('id', $usuario->id)->delete();
    });

    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();

    return redirect()->route('login');
})->middleware('auth')->name('cuenta.destroy');
