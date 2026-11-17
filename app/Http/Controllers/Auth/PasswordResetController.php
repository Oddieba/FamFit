<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Http\Controllers\Auth\RegisteredUserController;
use Illuminate\Auth\Events\PasswordReset;
class PasswordResetController extends Controller
{
    // Mostrar formulario para solicitar recuperación
    public function create()
    {
        return view('forgot-password');
    }

    // Enviar enlace al correo
   
public function store(Request $request)
{
    $request->validate([
        'email' => ['required', 'email'],
    ]);

    $status = Password::sendResetLink(
        $request->only('email')
    );

    if ($status === Password::RESET_LINK_SENT) {
        return back()->with('status', __($status));
    }

    return back()
        ->withErrors(['email' => __($status)])
        ->onlyInput('email');
}


    // Mostrar formulario para cambiar contraseña
    public function edit(Request $request, string $token)
    {
      return view('restablecer', [
            'token' => $token,
            'email' => $request->email,
        ]);
    }

    // Guardar la nueva contraseña
    public function update(Request $request)
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $status = Password::reset(
            $request->only(
                'email',
                'password',
                'password_confirmation',
                'token'
            ),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                event(new PasswordReset($user));
            }
        );

        return $status == Password::PASSWORD_RESET
            ? redirect()->route('login')->with('status', __($status))
            : back()->withErrors(['email' => [__($status)]]);
    }
}