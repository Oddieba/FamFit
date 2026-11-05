<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Misiones
        Schema::create('missions', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 300)->nullable();
            $table->enum('category', ['hydration', 'relaxation', 'active_break', 'healthy_eating', 'rest_habit', 'plan_day']);
            $table->enum('completion_mode', ['button', 'timer', 'manual_confirm']);
            $table->unsignedInteger('target_value')->nullable();
            $table->enum('target_unit', ['ml', 'seconds', 'count'])->nullable();
            $table->unsignedSmallInteger('xp_reward')->default(0);
            $table->unsignedSmallInteger('coin_reward')->default(0);
            $table->unsignedTinyInteger('daily_reward_cap')->default(1);
            $table->boolean('allows_photo')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });

        // 2. Test Inicial
        Schema::create('initial_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('questionnaire_version', 20);
            $table->enum('main_goal', ['general_fitness', 'strength', 'endurance', 'mobility', 'healthy_habits', 'other'])->nullable();
            $table->enum('experience_level', ['unsure', 'beginner', 'intermediate', 'advanced'])->default('unsure');
            $table->unsignedTinyInteger('sessions_per_week')->nullable();
            $table->unsignedSmallInteger('session_minutes')->nullable();
            $table->string('preferred_environments')->nullable();
            $table->json('equipment')->nullable();
            $table->json('sleep_habits')->nullable();
            $table->json('social_interests')->nullable();
            $table->json('answers');
            $table->boolean('is_current')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 3. Horario (Calendario)
        Schema::create('habit_schedule_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('item_type', ['routine', 'mission', 'habit', 'rest']);
            $table->unsignedBigInteger('routine_id')->nullable();
            $table->unsignedBigInteger('mission_id')->nullable();
            $table->string('title', 120);
            $table->date('scheduled_date');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('status', ['planned', 'completed', 'postponed', 'rescheduled', 'missed'])->default('planned');
            $table->unsignedBigInteger('rescheduled_from_id')->nullable();
            $table->boolean('suggested_by_system')->default(false);
            $table->unsignedSmallInteger('reminder_minutes_before')->nullable();
            $table->timestamp('reminder_sent_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('set null');
            $table->foreign('mission_id')->references('id')->on('missions')->onDelete('set null');
            $table->foreign('rescheduled_from_id')->references('id')->on('habit_schedule_items')->onDelete('set null');
        });

        // Relación tardía para workout_sessions
        Schema::table('workout_sessions', function (Blueprint $table) {
            $table->foreign('schedule_item_id')->references('id')->on('habit_schedule_items')->onDelete('set null');
        });

        // 4. Disponibilidad semanal (Schedules)
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedTinyInteger('weekday'); // 1 = Lunes, 7 = Domingo
            $table->time('start_time');
            $table->time('end_time');
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 5. Comidas
        Schema::create('meal_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('meal_type', ['breakfast', 'lunch', 'dinner', 'snack']);
            $table->dateTime('eaten_at');
            $table->string('description', 300)->nullable();
            $table->enum('source', ['manual', 'photo_scan'])->default('manual');
            $table->string('photo_path')->nullable();
            $table->boolean('user_corrected')->default(false);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 6. Ítems de comidas
        Schema::create('meal_log_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('meal_log_id');
            $table->string('food_name', 120);
            $table->string('portion_description', 60)->nullable();
            $table->unsignedSmallInteger('calories_kcal')->nullable();
            $table->decimal('detection_confidence', 3, 2)->nullable();
            $table->boolean('is_user_confirmed')->default(true);

            $table->foreign('meal_log_id')->references('id')->on('meal_logs')->onDelete('cascade');
        });

        // 7. Sueño
        Schema::create('sleep_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->date('sleep_date');
            $table->dateTime('bedtime');
            $table->dateTime('wake_time');
            // Columna generada en MySQL
            $table->integer('duration_minutes')->storedAs('timestampdiff(MINUTE, bedtime, wake_time)');
            $table->unsignedTinyInteger('quality_rating')->nullable();
            $table->enum('source', ['manual', 'device'])->default('manual');
            $table->string('notes', 300)->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_id', 'sleep_date']);
        });

        // 8. Logs de misiones
        Schema::create('mission_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('mission_id');
            $table->timestamp('completed_at')->useCurrent();
            $table->unsignedInteger('value')->nullable();
            $table->string('photo_path')->nullable();
            $table->string('notes', 300)->nullable();
            $table->boolean('reward_granted')->default(false);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('mission_id')->references('id')->on('missions');
        });
    }

    public function down(): void
    {
        Schema::table('workout_sessions', function (Blueprint $table) {
            $table->dropForeign(['schedule_item_id']);
        });
        Schema::dropIfExists('mission_logs');
        Schema::dropIfExists('sleep_logs');
        Schema::dropIfExists('meal_log_items');
        Schema::dropIfExists('meal_logs');
        Schema::dropIfExists('schedules');
        Schema::dropIfExists('habit_schedule_items');
        Schema::dropIfExists('initial_assessments');
        Schema::dropIfExists('missions');
    }
};