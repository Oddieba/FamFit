<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Niveles
        Schema::create('levels', function (Blueprint $table) {
            $table->unsignedSmallInteger('id')->primary();
            $table->unsignedInteger('xp_required')->unique();
            $table->string('title', 60)->nullable();
        });

        // 2. Logros (Achievements)
        Schema::create('achievements', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->string('description', 300)->nullable();
            $table->string('icon')->nullable();
            $table->enum('condition_type', ['workouts_count', 'streak_days', 'missions_count', 'level_reached', 'friends_count']);
            $table->unsignedInteger('condition_value');
            $table->unsignedSmallInteger('xp_reward')->default(0);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 3. Retos (Challenges)
        Schema::create('challenges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->string('title', 120);
            $table->text('description')->nullable();
            $table->enum('challenge_type', ['individual', 'cooperative', 'competitive']);
            $table->enum('metric', ['workouts', 'active_minutes', 'streak_days', 'missions']);
            $table->unsignedInteger('goal_value');
            $table->dateTime('starts_at');
            $table->dateTime('ends_at');
            $table->enum('visibility', ['private', 'friends', 'community'])->default('friends');
            $table->enum('status', ['draft', 'active', 'finished', 'cancelled'])->default('draft');
            $table->unsignedSmallInteger('reward_xp')->default(0);
            $table->unsignedSmallInteger('reward_coins')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('set null');
        });

        // 4. Objetos de Avatar
        Schema::create('avatar_items', function (Blueprint $table) {
            $table->id();
            $table->string('code', 50)->unique();
            $table->string('name', 100);
            $table->enum('category', ['hair', 'clothing', 'accessory', 'color', 'skin']);
            $table->string('image_url')->nullable();
            $table->unsignedInteger('price_coins')->nullable();
            $table->unsignedSmallInteger('unlock_level')->nullable();
            $table->unsignedBigInteger('unlock_challenge_id')->nullable();
            $table->boolean('is_premium')->default(false);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('unlock_challenge_id')->references('id')->on('challenges')->onDelete('set null');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });

        // 5. Miembros del reto
        Schema::create('challenge_members', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('challenge_id');
            $table->unsignedBigInteger('user_id');
            $table->enum('role', ['owner', 'member'])->default('member');
            $table->enum('invitation_status', ['invited', 'joined', 'declined'])->default('joined');
            $table->unsignedInteger('progress_value')->default(0);
            $table->timestamp('joined_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('reward_granted_at')->nullable();

            $table->foreign('challenge_id')->references('id')->on('challenges')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 6. Amistades (Friendships)
        Schema::create('friendships', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('requester_id');
            $table->unsignedBigInteger('addressee_id');
            $table->enum('status', ['pending', 'accepted', 'rejected'])->default('pending');
            $table->unsignedBigInteger('user_low_id')->virtualAs('least(requester_id, addressee_id)');
            $table->unsignedBigInteger('user_high_id')->virtualAs('greatest(requester_id, addressee_id)');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            $table->foreign('requester_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('addressee_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['user_low_id', 'user_high_id']);
        });

        // 7. Bloqueos
        Schema::create('blocks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('blocker_id');
            $table->unsignedBigInteger('blocked_id');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('blocker_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('blocked_id')->references('id')->on('users')->onDelete('cascade');
            $table->unique(['blocker_id', 'blocked_id']);
        });

        // 8. Reportes y Moderación
        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('reporter_id')->nullable();
            $table->enum('target_type', ['user', 'routine', 'challenge', 'other']);
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->unsignedBigInteger('target_id')->nullable();
            $table->enum('reason', ['harassment', 'harmful_content', 'dangerous_routine', 'misinformation', 'other']);
            $table->string('description', 500)->nullable();
            $table->enum('status', ['open', 'in_review', 'resolved', 'dismissed'])->default('open');
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->string('resolution_note', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('resolved_at')->nullable();

            $table->foreign('reporter_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('moderation_actions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('report_id')->nullable();
            $table->unsignedBigInteger('moderator_id')->nullable();
            $table->unsignedBigInteger('target_user_id')->nullable();
            $table->enum('action', ['hide_content', 'warn', 'suspend_account', 'restore_account', 'dismiss']);
            $table->string('reason', 500)->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('report_id')->references('id')->on('reports')->onDelete('set null');
            $table->foreign('moderator_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('target_user_id')->references('id')->on('users')->onDelete('set null');
        });

        // 9. Logros de usuarios e inventario
        Schema::create('user_achievements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('achievement_id');
            $table->timestamp('unlocked_at')->useCurrent();
            $table->boolean('is_featured')->default(false);

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('achievement_id')->references('id')->on('achievements')->onDelete('cascade');
        });

        Schema::create('user_avatar_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('item_id');
            $table->enum('acquired_by', ['default', 'purchase', 'level_unlock', 'challenge']);
            $table->boolean('is_equipped')->default(false);
            $table->timestamp('acquired_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('avatar_items')->onDelete('cascade');
        });

        // 10. Transacciones (XP y Monedas)
        Schema::create('xp_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('xp_delta');
            $table->enum('source_type', ['workout', 'mission', 'challenge', 'achievement', 'admin_adjustment']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->string('description', 150)->nullable();
            $table->date('occurred_on');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('coin_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->integer('coins_delta');
            $table->enum('source_type', ['workout', 'mission', 'challenge', 'achievement', 'purchase', 'admin_adjustment']);
            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedInteger('balance_after')->nullable();
            $table->string('description', 150)->nullable();
            $table->date('occurred_on');
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 11. Notificaciones y Preferencias
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('type', 50);
            $table->string('title', 120);
            $table->string('body', 300)->nullable();
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('food_preferences', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('kind', ['allergy', 'intolerance', 'avoid', 'preference']);
            $table->string('food_name', 100);
            $table->string('notes', 200)->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        Schema::create('user_restrictions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->enum('restriction_type', ['limitation', 'discomfort', 'other']);
            $table->string('body_area', 60)->nullable();
            $table->string('description', 300);
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });

        // 12. Solicitudes de borrado y reseñas
        Schema::create('data_deletion_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'cancelled'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('processed_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        Schema::create('community_routine_reviews', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('routine_id');
            $table->unsignedBigInteger('reviewer_id')->nullable();
            $table->enum('decision', ['approved', 'rejected', 'changes_requested']);
            $table->string('comment', 500)->nullable();
            $table->timestamp('reviewed_at')->useCurrent();

            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('cascade');
            $table->foreign('reviewer_id')->references('id')->on('users')->onDelete('set null');
        });

        // 13. Recomendaciones y rutinas guardadas
        Schema::create('routine_recommendations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('routine_id');
            $table->unsignedBigInteger('assessment_id')->nullable();
            $table->enum('status', ['pending', 'accepted', 'rejected', 'modified'])->default('pending');
            $table->timestamp('created_at')->useCurrent();
            $table->timestamp('responded_at')->nullable();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('cascade');
            $table->foreign('assessment_id')->references('id')->on('initial_assessments')->onDelete('set null');
        });

        Schema::create('saved_routines', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('routine_id');
            $table->timestamp('saved_at')->useCurrent();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('routine_id')->references('id')->on('routines')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('saved_routines');
        Schema::dropIfExists('routine_recommendations');
        Schema::dropIfExists('community_routine_reviews');
        Schema::dropIfExists('data_deletion_requests');
        Schema::dropIfExists('user_restrictions');
        Schema::dropIfExists('food_preferences');
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('coin_transactions');
        Schema::dropIfExists('xp_transactions');
        Schema::dropIfExists('user_avatar_items');
        Schema::dropIfExists('user_achievements');
        Schema::dropIfExists('moderation_actions');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('blocks');
        Schema::dropIfExists('friendships');
        Schema::dropIfExists('challenge_members');
        Schema::dropIfExists('avatar_items');
        Schema::dropIfExists('challenges');
        Schema::dropIfExists('achievements');
        Schema::dropIfExists('levels');
    }
};