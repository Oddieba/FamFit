<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Soluciona el error del usuario
use App\Models\Profile;            // Soluciona el error de la clase Profile

class OnboardingController extends Controller
{
    public function index()
    {
        return view('test');
    }

    public function store(Request $request)
    {
        $request->validate([
            'edad' => 'required|integer',
            'peso' => 'required|numeric',
            'altura' => 'required|integer',
            'genero' => 'required|string',
            'objetivo' => 'required|string',
            'experiencia_ejercicio' => 'required|string',
        ]);
        
        // Le indicamos explícitamente al editor el tipo de modelo
        /** @var \App\Models\User $user */
        $user = Auth::user();

        if ($user) {
            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'age'                => $request->edad,
                    'weight'             => $request->peso,
                    'height'             => $request->altura,
                    'gender'             => $request->genero,
                    'goals'              => $request->objetivo,
                    'experience'         => $request->experiencia_ejercicio,
                    'availability'       => json_encode($request->disponibilidad),
                    'has_injury'         => $request->tiene_lesion,
                    'injury_description' => $request->descripcion_lesion,
                ]
            );
        }
        if (Auth::check()) {
            dd('¡Alerta! La sesión se perdió antes de llegar aquí.');
        }
        return redirect()->route('menu')->with('status', '¡Perfil configurado con éxito!');
    }
}