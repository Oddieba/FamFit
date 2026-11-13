<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Soluciona el error del usuario
use App\Models\Profile;            // Soluciona el error de la clase Profile

class OnboardingController extends Controller
{
    public function index()
{
    // Buscamos el perfil del usuario actual (si es que tiene)
    $perfil = Auth::user()->profile;
    
    // Enviamos ese perfil a la vista
    return view('test', compact('perfil'));
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
        
        /** @var \App\Models\User $user */
        $user = Auth::user();

               if ($user) {
            // 1. Guardar en el Perfil
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

            // 2. Traducir las respuestas para la tabla initial_assessments
            $objetivoTraducido = match($request->objetivo) {
                'ganar_musculo' => 'strength',
                'perder_grasa' => 'endurance',
                'resistencia' => 'endurance',
                'flexibilidad' => 'mobility',
                default => 'general_fitness',
            };

            $experienciaTraducida = match($request->experiencia_ejercicio) {
                'principiante' => 'beginner',
                'intermedio' => 'intermediate',
                'avanzado' => 'advanced',
                default => 'unsure',
            };

            // 3. PRIMERO: Desmarcar tests anteriores
            $user->initialAssessments()->update(['is_current' => false]);

            // 4. SEGUNDO: Crear el nuevo test con los datos YA traducidos
            $user->initialAssessments()->create([
                'questionnaire_version' => '1.0',
                'main_goal'             => $objetivoTraducido, 
                'experience_level'      => $experienciaTraducida, 
                'answers'               => $request->all(), 
                'is_current'            => true,
                'completed_at'          => now(),
            ]);
        }
       
        return redirect()->route('menu')->with('status', '¡Perfil configurado con éxito!');
    }
}