<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rules\Password;

class RegisteredUserController extends Controller
{
    public function store(Request $request)
    {
        $datos = $request->validate([
            'username' => ['required', 'string', 'min:3', 'max:20', 'regex:/^[a-zA-Z0-9_]+$/', 'unique:users,username'],
            'email' => ['required', 'string', 'email', 'max:190', 'unique:users,email'],
            'password' => [
                'required',
                'confirmed',
                Password::min(8)->letters()->numbers(),
            ],
            'visible' => ['sometimes', 'accepted'],
            'terminos' => ['accepted'],
        ], [
            'username.regex' => 'Usa de 3 a 20 caracteres: letras, números o guion bajo (_), sin espacios.',
            'username.unique' => 'Ese nombre de usuario ya está en uso.',
            'email.unique' => 'Ese correo ya está registrado.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'visible.accepted' => 'La opción de visibilidad no es válida.',
            'terminos.accepted' => 'Debes aceptar los términos y la política de privacidad.',
        ]);

        $atributos = [
            'username' => $datos['username'],
            'email' => $datos['email'],
            'password' => $datos['password'],
            'profile_visible' => $request->boolean('visible'),
        ];

        if (Schema::hasColumn('users', 'name')) {
            $atributos['name'] = $datos['username'];
        }

        foreach (['terms_accepted_at', 'privacy_accepted_at'] as $columna) {
            if (Schema::hasColumn('users', $columna)) {
                $atributos[$columna] = now();
            }
        }

        $usuario = User::create($atributos);

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route('registro')->with('registration_success', true);
    }
}
