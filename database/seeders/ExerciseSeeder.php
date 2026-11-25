<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            // --- GIMNASIO ---
            ['slug' => 'press-banca', 'name' => 'Press de Banca', 'environments' => 'gym', 'objective' => 'strength', 'muscle_group' => 'Pecho', 'difficulty' => 'intermediate', 'intensity' => 'high', 'instructions' => 'Acuéstate en el banco, baja la barra al pecho y empuja.'],
            ['slug' => 'sentadilla-barra', 'name' => 'Sentadilla con Barra', 'environments' => 'gym', 'objective' => 'strength', 'muscle_group' => 'Piernas', 'difficulty' => 'intermediate', 'intensity' => 'high', 'instructions' => 'Flexiona las rodillas bajando la cadera con la barra en la espalda.'],
            ['slug' => 'peso-muerto', 'name' => 'Peso Muerto', 'environments' => 'gym', 'objective' => 'strength', 'muscle_group' => 'Espalda/Piernas', 'difficulty' => 'advanced', 'intensity' => 'high', 'instructions' => 'Levanta la barra del suelo manteniendo la espalda recta.'],
            ['slug' => 'dominadas', 'name' => 'Dominadas', 'environments' => 'gym', 'objective' => 'strength', 'muscle_group' => 'Espalda', 'difficulty' => 'advanced', 'intensity' => 'high', 'instructions' => 'Cuélgate de la barra y tira de tu cuerpo hacia arriba.'],
            ['slug' => 'press-militar', 'name' => 'Press Militar', 'environments' => 'gym', 'objective' => 'strength', 'muscle_group' => 'Hombros', 'difficulty' => 'intermediate', 'intensity' => 'medium', 'instructions' => 'Empuja la barra desde los hombros hacia arriba.'],
            ['slug' => 'curl-biceps', 'name' => 'Curl de Bíceps', 'environments' => 'gym', 'objective' => 'strength', 'muscle_group' => 'Bíceps', 'difficulty' => 'beginner', 'intensity' => 'low', 'instructions' => 'Flexiona los codos levantando las mancuernas.'],

            // --- EN CASA ---
            ['slug' => 'flexiones', 'name' => 'Flexiones de Pecho', 'environments' => 'home', 'objective' => 'strength', 'muscle_group' => 'Pecho', 'difficulty' => 'beginner', 'intensity' => 'medium', 'instructions' => 'Baja el pecho hasta casi tocar el suelo y empuja.'],
            ['slug' => 'sentadilla-aire', 'name' => 'Sentadilla al Aire', 'environments' => 'home', 'objective' => 'strength', 'muscle_group' => 'Piernas', 'difficulty' => 'beginner', 'intensity' => 'medium', 'instructions' => 'Baja la cadera como si te sentaras en una silla invisible.'],
            ['slug' => 'plancha', 'name' => 'Plancha Abdominal', 'environments' => 'home', 'objective' => 'strength', 'muscle_group' => 'Core', 'difficulty' => 'beginner', 'intensity' => 'low', 'instructions' => 'Mantén el cuerpo recto apoyado en antebrazos y pies.'],
            ['slug' => 'zancadas', 'name' => 'Zancadas', 'environments' => 'home', 'objective' => 'strength', 'muscle_group' => 'Piernas', 'difficulty' => 'beginner', 'intensity' => 'medium', 'instructions' => 'Da un paso al frente y flexiona ambas rodillas.'],
            ['slug' => 'jumping-jacks', 'name' => 'Jumping Jacks', 'environments' => 'home', 'objective' => 'cardio', 'muscle_group' => 'Cuerpo Completo', 'difficulty' => 'beginner', 'intensity' => 'medium', 'instructions' => 'Salta abriendo piernas y brazos simultáneamente.'],

            // --- AL AIRE LIBRE ---
            ['slug' => 'trote', 'name' => 'Trote Continuo', 'environments' => 'outdoor', 'objective' => 'endurance', 'muscle_group' => 'Cardio', 'difficulty' => 'beginner', 'intensity' => 'medium', 'instructions' => 'Corre a un ritmo constante y moderado.'],
            ['slug' => 'sprints', 'name' => 'Sprints', 'environments' => 'outdoor', 'objective' => 'cardio', 'muscle_group' => 'Piernas', 'difficulty' => 'advanced', 'intensity' => 'high', 'instructions' => 'Corre a máxima velocidad por intervalos cortos.'],
            ['slug' => 'fondos-parque', 'name' => 'Fondos en Paralelas', 'environments' => 'outdoor', 'objective' => 'strength', 'muscle_group' => 'Tríceps', 'difficulty' => 'intermediate', 'intensity' => 'high', 'instructions' => 'Usa las barras del parque para subir y bajar tu peso.'],
        ];

        foreach ($exercises as $ex) {
            DB::table('exercises')->updateOrInsert(
                ['slug' => $ex['slug']],
                array_merge($ex, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}
