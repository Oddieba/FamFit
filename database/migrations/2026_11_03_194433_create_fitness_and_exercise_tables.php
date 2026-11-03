<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Ejercicios
        Schema::create('exercises', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 120)->unique();
            $table->string('name', 120);
            // Nota: MySQL SET no es soportado nativamente por Laravel, usamos JSON/String
            $table->string('environments'); 
            $table->enum('objective', ['strength', 'endurance', 'mobility', 'cardio', 'balance', 'relaxation']);
            $table->string('muscle_group', 60)->nullable();
            $table->string('target_zone', 60)->nullable();
            $table->string('equipment', 200)->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->enum('intensity', ['low', 'medium', 'high'])->default('medium');
            $table->text('instructions');
            $table->text('common_mistakes')->nullable();
            $table->string('responsible_use_note', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 2. Alternativas de ejercicios
        Schema::create('exercise_alternatives', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedBigInteger('alternative_exercise_id');
            $table->string('note', 200)->nullable();

            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('alternative_exercise_id')->references('id')->on('exercises')->onDelete('cascade');
        });

        // 3. Media de ejercicios
        Schema::create('exercise_media', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('exercise_id');
            $table->enum('media_type', ['image', 'sequence', 'avatar_animation']);
            $table->string('url');
            $table->string('alt_text', 200)->nullable();
            $table->unsignedSmallInteger('sort_order')->default(0);
            $table->boolean('is_validated')->default(false);
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
        });

        // 4. Rutinas
        Schema::create('routines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('owner_user_id')->nullable();
            $table->string('name', 120);
            $table->text('description')->nullable();
            $table->enum('environment', ['home', 'outdoor', 'gym']);
            $table->enum('objective', ['strength', 'endurance', 'mobility', 'cardio', 'balance', 'relaxation'])->nullable();
            $table->enum('difficulty', ['beginner', 'intermediate', 'advanced'])->default('beginner');
            $table->unsignedSmallInteger('estimated_minutes')->nullable();
            $table->enum('source', ['preset', 'suggested', 'user'])->default('user');
            $table->enum('visibility', ['private', 'community'])->default('private');
            $table->enum('publication_status', ['not_published', 'pending_review', 'published', 'rejected'])->default('not_published');
            $table->timestamp('published_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('owner_user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 5. Ejercicios por rutina
        Schema::create('routine_exercises', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('routine_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedSmallInteger('position');
            $table->unsignedTinyInteger('sets')->nullable();
            $table->unsignedSmallInteger('reps')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->unsignedSmallInteger('rest_seconds')->nullable();
            $table->string('load_note', 120)->nullable();
            $table->string('notes', 200)->nullable();

            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises');
        });

        // 6. Sesiones de entrenamiento
        Schema::create('workout_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('routine_id')->nullable();
            $table->unsignedBigInteger('schedule_item_id')->nullable(); // Se enlaza luego con el calendario
            $table->enum('environment', ['home', 'outdoor', 'gym'])->nullable();
            $table->enum('status', ['in_progress', 'completed', 'abandoned'])->default('in_progress');
            $table->dateTime('started_at');
            $table->dateTime('ended_at')->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedTinyInteger('perceived_effort')->nullable();
            $table->enum('tolerance', ['easy', 'adequate', 'hard', 'too_hard'])->nullable();
            $table->string('notes', 500)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('set null');
        });

        // 7. Logs de series (Workout Set Logs)
        Schema::create('workout_set_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('session_id');
            $table->unsignedBigInteger('exercise_id');
            $table->unsignedTinyInteger('set_number');
            $table->unsignedSmallInteger('reps')->nullable();
            $table->decimal('weight_kg', 6, 2)->nullable();
            $table->unsignedInteger('duration_seconds')->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->string('notes', 200)->nullable();

            $table->foreign('session_id')->references('id')->on('workout_sessions')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises');
        });

        // 8. Propuestas de progresión
        Schema::create('progression_proposals', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('routine_id');
            $table->unsignedBigInteger('exercise_id')->nullable();
            $table->string('proposal', 300);
            $table->enum('status', ['pending', 'accepted', 'kept', 'reduced'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('cascade');
            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('progression_proposals');
        Schema::dropIfExists('workout_set_logs');
        Schema::dropIfExists('workout_sessions');
        Schema::dropIfExists('routine_exercises');
        Schema::dropIfExists('routines');
        Schema::dropIfExists('exercise_media');
        Schema::dropIfExists('exercise_alternatives');
        Schema::dropIfExists('exercises');
    }
};