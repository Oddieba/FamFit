<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    // 1. Muestra la vista del test inicial
    public function index()
    {
        return view('test');
    }

    // 2. Recibe, valida y guarda los datos del test
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

        $user = auth()->user();
        if ($user) {
            $user->update([
                'edad' => $request->edad,
                'peso' => $request->peso,
                'altura' => $request->altura,
                'genero' => $request->genero,
                'objetivo' => $request->objetivo,
                'experiencia' => $request->experiencia_ejercicio,
                'disponibilidad' => json_encode($request->disponibilidad),
                'tiene_lesion' => $request->tiene_lesion,
                'descripcion_lesion' => $request->descripcion_lesion,
            ]);
        }

        return redirect()->route('menu')->with('status', '¡Perfil configurado con éxito!');
    }
}