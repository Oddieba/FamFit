<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/*
|--------------------------------------------------------------------------
| Migración del módulo de Nutrición de FamFit
|--------------------------------------------------------------------------
| Crea las tablas necesarias para:
|  1. comidas              -> cada comida registrada (fecha, tipo, hora, etc.)
|  2. comida_alimentos     -> los alimentos y porciones de cada comida
|  3. fotos_comida         -> la ruta de las fotos de cada comida
|  4. restricciones_usuario -> alergias, intolerancias y alimentos que evita
|  5. agua_registros       -> cada vez que el usuario toma agua
|
| Orden: primero la tabla padre (comidas), luego las que dependen de ella.
*/
return new class extends Migration
{
    /**
     * Crea las tablas (se ejecuta con: php artisan migrate).
     */
    public function up(): void
    {
        // 1. COMIDAS
        // Una fila por comida. Pertenece a un usuario.
        Schema::create('comidas', function (Blueprint $table) {
            $table->id();

            // Dueño de la comida. Si se borra el usuario, se borran sus comidas.
            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('fecha');                    // Día en que se comió
            $table->enum('tipo', [                    // Tipo de comida
                'desayuno', 'almuerzo', 'cena', 'snack',
            ]);
            $table->time('hora')->nullable();         // Hora opcional
            $table->string('descripcion', 120)->nullable(); // Ej. "Arepa con huevo"
            $table->text('notas')->nullable();        // Comentarios opcionales
            $table->enum('origen', ['manual', 'foto'])->default('manual');

            $table->timestamps();                     // created_at y updated_at

            // Índice para consultar rápido "las comidas de este usuario en esta fecha"
            $table->index(['user_id', 'fecha']);
        });

        // 2. COMIDA_ALIMENTOS
        // Los alimentos de cada comida. Si se borra la comida, se borran sus alimentos.
        Schema::create('comida_alimentos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comida_id')
                ->constrained('comidas')
                ->cascadeOnDelete();

            $table->string('nombre', 80);             // Ej. "Huevo"
            $table->string('porcion', 40)->nullable(); // Ej. "2 unidades"

            // Qué tan seguro está el reconocimiento de la foto (0.00 a 1.00).
            // Queda vacío si el alimento se escribió a mano.
            $table->decimal('confianza', 3, 2)->nullable();

            $table->timestamps();
        });

        // 3. FOTOS_COMIDA
        // Las fotos asociadas a una comida. Guardamos solo la ruta del archivo.
        Schema::create('fotos_comida', function (Blueprint $table) {
            $table->id();

            $table->foreignId('comida_id')
                ->constrained('comidas')
                ->cascadeOnDelete();

            $table->string('ruta');                   // Ej. "comidas/5/abc123.jpg"

            $table->timestamps();
        });

        // 4. RESTRICCIONES_USUARIO
        // Alergias, intolerancias y alimentos que el usuario evita.
        Schema::create('restricciones_usuario', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('texto', 60);              // Ej. "maní"
            $table->enum('tipo', [
                'alergia', 'intolerancia', 'evita',
            ]);

            $table->timestamps();

            // Evita guardar dos veces la misma restricción para un usuario
            $table->unique(['user_id', 'texto']);
        });

        // 5. AGUA_REGISTROS
        // Cada registro es un consumo de agua (ej. un vaso de 250 ml).
        // El total del día se calcula sumando las filas.
        Schema::create('agua_registros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->date('fecha');
            $table->unsignedSmallInteger('ml');       // Mililitros consumidos

            $table->timestamps();

            // Índice para sumar rápido el agua de un usuario en un día
            $table->index(['user_id', 'fecha']);
        });
    }

    /**
     * Borra las tablas (se ejecuta con: php artisan migrate:rollback).
     * Se borran en orden inverso para respetar las llaves foráneas.
     */
    public function down(): void
    {
        Schema::dropIfExists('agua_registros');
        Schema::dropIfExists('restricciones_usuario');
        Schema::dropIfExists('fotos_comida');
        Schema::dropIfExists('comida_alimentos');
        Schema::dropIfExists('comidas');
    }
};