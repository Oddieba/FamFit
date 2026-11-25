<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoutineSeeder extends Seeder
{
    public function run(): void
    {
        $routines = [
            // --- RUTINAS DE GIMNASIO (Famosas) ---
            [
                'name' => 'Weider: Pecho y Tríceps',
                'description' => 'Rutina clásica enfocada en el volumen del tren superior.',
                'environment' => 'gym',
                'objective' => 'strength',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 60,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'press-banca', 'sets' => 4, 'reps' => 10, 'rest' => 90],
                    ['slug' => 'fondos-parque', 'sets' => 3, 'reps' => 12, 'rest' => 60],
                ]
            ],
            [
                'name' => 'Full Body 5x5 (Fuerza)',
                'description' => 'Ideal para principiantes. Enfocada en fuerza base.',
                'environment' => 'gym',
                'objective' => 'strength',
                'difficulty' => 'beginner',
                'estimated_minutes' => 45,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'sentadilla-barra', 'sets' => 5, 'reps' => 5, 'rest' => 120],
                    ['slug' => 'press-banca', 'sets' => 5, 'reps' => 5, 'rest' => 120],
                    ['slug' => 'peso-muerto', 'sets' => 1, 'reps' => 5, 'rest' => 120],
                ]
            ],
            [
                'name' => 'Upper / Lower (Torso / Pierna)',
                'description' => 'Rutina dividida en tren superior e inferior.',
                'environment' => 'gym',
                'objective' => 'strength',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 50,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'dominadas', 'sets' => 4, 'reps' => 8, 'rest' => 90],
                    ['slug' => 'press-militar', 'sets' => 3, 'reps' => 10, 'rest' => 90],
                    ['slug' => 'curl-biceps', 'sets' => 3, 'reps' => 12, 'rest' => 60],
                ]
            ],

            // --- RUTINAS EN CASA ---
            [
                'name' => 'Calistenia para Principiantes',
                'description' => 'Tonifica todo el cuerpo usando tu propio peso.',
                'environment' => 'home',
                'objective' => 'strength',
                'difficulty' => 'beginner',
                'estimated_minutes' => 30,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'flexiones', 'sets' => 3, 'reps' => 10, 'rest' => 60],
                    ['slug' => 'sentadilla-aire', 'sets' => 3, 'reps' => 15, 'rest' => 60],
                    ['slug' => 'plancha', 'sets' => 3, 'duration' => 30, 'rest' => 45],
                ]
            ],
            [
                'name' => 'Cardio Quema Grasa (En Casa)',
                'description' => 'Rutina de alta intensidad sin necesidad de equipo.',
                'environment' => 'home',
                'objective' => 'endurance',
                'difficulty' => 'intermediate',
                'estimated_minutes' => 20,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'jumping-jacks', 'sets' => 4, 'duration' => 45, 'rest' => 15],
                    ['slug' => 'zancadas', 'sets' => 4, 'reps' => 12, 'rest' => 30],
                ]
            ],

            // --- RUTINAS AL AIRE LIBRE ---
            [
                'name' => 'Trote y Resistencia',
                'description' => 'Mejora tu capacidad pulmonar al aire libre.',
                'environment' => 'outdoor',
                'objective' => 'endurance',
                'difficulty' => 'beginner',
                'estimated_minutes' => 45,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'trote', 'sets' => 1, 'duration' => 2400, 'rest' => 0], // 40 minutos
                ]
            ],
            [
                'name' => 'HIIT en el Parque',
                'description' => 'Intervalos de alta intensidad usando el mobiliario urbano.',
                'environment' => 'outdoor',
                'objective' => 'cardio',
                'difficulty' => 'advanced',
                'estimated_minutes' => 25,
                'source' => 'preset',
                'exercises' => [
                    ['slug' => 'sprints', 'sets' => 8, 'duration' => 30, 'rest' => 60],
                    ['slug' => 'fondos-parque', 'sets' => 3, 'reps' => 10, 'rest' => 60],
                ]
            ],
        ];

        foreach ($routines as $routineData) {
            $exercises = $routineData['exercises'];
            unset($routineData['exercises']);

            // Insertar rutina
            DB::table('routines')->updateOrInsert(
                ['name' => $routineData['name']],
                array_merge($routineData, [
                    'visibility' => 'community',
                    'publication_status' => 'published',
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );

            $routine = DB::table('routines')->where('name', $routineData['name'])->first();

            // Vincular ejercicios
            $position = 1;
            foreach ($exercises as $ex) {
                $exercise = DB::table('exercises')->where('slug', $ex['slug'])->first();
                if ($exercise) {
                    DB::table('routine_exercises')->updateOrInsert(
                        ['routine_id' => $routine->id, 'exercise_id' => $exercise->id],
                        [
                            'position' => $position++,
                            'sets' => $ex['sets'] ?? null,
                            'reps' => $ex['reps'] ?? null,
                            'duration_seconds' => $ex['duration'] ?? null,
                            'rest_seconds' => $ex['rest'] ?? null,
                        ]
                    );
                }
            }
        }
    }
}
