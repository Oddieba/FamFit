<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Roles
        Schema::create('roles', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->string('name', 30)->unique();
            $table->string('description', 150)->nullable();
        });

        // 2. Usuarios
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('role_id')->default(1);
            $table->string('username', 30)->unique();
            $table->string('email', 190)->unique();
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->dateTime('terms_accepted_at');
            $table->dateTime('privacy_accepted_at');
            $table->enum('status', ['active', 'suspended', 'pending_deletion'])->default('active');
            $table->string('timezone', 64)->default('UTC');
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamp('last_login_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->softDeletes();
            
            $table->foreign('role_id')->references('id')->on('roles');
        });

        // 3. Recuperación de contraseñas
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->id();
            $table->string('email', 190)->unique();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        // 4. Sesiones
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });

        // 5. Perfiles (Relación 1 a 1 usando el ID del usuario)
            // 5. Perfiles (Relación 1 a 1 usando user_id)
        Schema::create('profiles', function (Blueprint $table) {
            $table->id(); // <--- Ahora sí, la llave primaria normal
            
            // Agregamos explícitamente la columna user_id
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->string('display_name', 60)->nullable();
            $table->string('bio', 300)->nullable();
            $table->date('birth_date')->nullable();
            $table->string('goals', 300)->nullable();
            $table->integer('age')->nullable();
            $table->decimal('weight', 5, 2)->nullable();
            $table->integer('height')->nullable();
            $table->string('gender', 50)->nullable();
            $table->string('experience', 50)->nullable();
            $table->text('availability')->nullable();
            $table->string('has_injury', 10)->nullable();
            $table->text('injury_description')->nullable();
            $table->json('interests')->nullable();
            $table->json('avatar_config')->nullable();
            $table->enum('profile_visibility', ['private', 'friends', 'community'])->default('private');
            $table->enum('schedule_visibility', ['private', 'friends', 'community'])->default('private');
            $table->boolean('show_level')->default(false);
            $table->boolean('show_achievements')->default(false);
            $table->boolean('show_streak')->default(false);
            $table->boolean('show_progress')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 6. Configuración de usuario
        Schema::create('user_settings', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->boolean('notify_reminders')->default(true);
            $table->boolean('notify_social')->default(true);
            $table->boolean('notify_challenges')->default(true);
            $table->boolean('high_contrast')->default(false);
            $table->boolean('reduced_motion')->default(false);
            $table->enum('text_size', ['normal', 'large'])->default('normal');
            $table->enum('preferred_environment', ['home', 'outdoor', 'gym'])->nullable();
            $table->unsignedSmallInteger('sleep_goal_minutes')->nullable();
            $table->unsignedSmallInteger('hydration_step_ml')->default(250);
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });

        // 7. Estadísticas
        Schema::create('user_stats', function (Blueprint $table) {
            $table->unsignedBigInteger('id')->primary();
            $table->unsignedInteger('xp_total')->default(0);
            $table->unsignedSmallInteger('level')->default(1);
            $table->unsignedInteger('coins_balance')->default(0);
            $table->unsignedSmallInteger('current_streak')->default(0);
            $table->unsignedSmallInteger('longest_streak')->default(0);
            $table->date('last_activity_date')->nullable();
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate();

            $table->foreign('id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_stats');
        Schema::dropIfExists('user_settings');
        Schema::dropIfExists('profiles');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('users');
        Schema::dropIfExists('roles');
    }
};