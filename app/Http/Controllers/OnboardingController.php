<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OnboardingController extends Controller
{
    public function index()
    {
        return view('test');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'edad' => ['required', 'integer', 'between:10,100'],
            'peso' => ['required', 'numeric', 'between:1,500'],
            'altura' => ['required', 'integer', 'between:50,300'],
            'genero' => ['required', 'string', 'max:30'],
            'objetivo' => ['required', 'string', 'max:100'],
            'experiencia_ejercicio' => ['required', 'string', 'max:50'],
            'disponibilidad' => ['nullable', 'array'],
            'disponibilidad.*' => ['string', 'max:20'],
            'tiene_lesion' => ['required', 'in:si,no'],
            'descripcion_lesion' => ['nullable', 'string', 'max:2000'],
        ]);

        $user = $request->user();
        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            [
                'age' => $data['edad'],
                'weight' => $data['peso'],
                'height' => $data['altura'],
                'gender' => $data['genero'],
                'goals' => $data['objetivo'],
                'experience' => $data['experiencia_ejercicio'],
                'availability' => $data['disponibilidad'] ?? [],
                'has_injury' => $data['tiene_lesion'] === 'si',
                'injury_description' => $data['descripcion_lesion'] ?? null,
            ]
        );

        return redirect()->route('menu')->with('status', 'Tu perfil se configuro correctamente.');
    }
}