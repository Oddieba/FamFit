<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table): void {
            if (! Schema::hasColumn('users', 'avatar_settings')) {
                $table->json('avatar_settings')->nullable();
            }

            if (! Schema::hasColumn('users', 'preferences')) {
                $table->json('preferences')->nullable();
            }

            if (! Schema::hasColumn('users', 'xp')) {
                $table->unsignedInteger('xp')->default(0);
            }
        });

        if (! Schema::hasTable('quest_routines')) {
            Schema::create('quest_routines', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
                $table->string('name', 80);
                $table->string('goal', 30);
                $table->string('experience', 20);
                $table->timestamps();
            });
        }

        if (! Schema::hasTable('quest_exercises')) {
            Schema::create('quest_exercises', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('quest_routine_id')->constrained()->cascadeOnDelete();
                $table->string('name', 80);
                $table->unsignedTinyInteger('day_of_week');
                $table->unsignedTinyInteger('sets');
                $table->unsignedTinyInteger('reps');
                $table->unsignedSmallInteger('rest_seconds');
                $table->unsignedTinyInteger('position');
                $table->timestamps();
                $table->index(['quest_routine_id', 'day_of_week']);
            });
        }

        if (! Schema::hasTable('quest_exercise_completions')) {
            Schema::create('quest_exercise_completions', function (Blueprint $table): void {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('quest_exercise_id')->constrained()->cascadeOnDelete();
                $table->date('completed_on');
                $table->timestamps();
                $table->unique(['user_id', 'quest_exercise_id', 'completed_on'], 'quest_completion_once_per_day');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('quest_exercise_completions');
        Schema::dropIfExists('quest_exercises');
        Schema::dropIfExists('quest_routines');

        Schema::table('users', function (Blueprint $table): void {
            $columns = [];

            foreach (['avatar_settings', 'preferences', 'xp'] as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $columns[] = $column;
                }
            }

            if ($columns !== []) {
                $table->dropColumn($columns);
            }
        });
    }
};