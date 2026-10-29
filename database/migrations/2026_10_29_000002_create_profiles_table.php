<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('profiles')) {
            return;
        }

        Schema::create('profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('age');
            $table->decimal('weight', 5, 2);
            $table->unsignedSmallInteger('height');
            $table->string('gender', 30);
            $table->string('goals', 100);
            $table->string('experience', 50);
            $table->json('availability')->nullable();
            $table->boolean('has_injury')->default(false);
            $table->text('injury_description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        $hasLegacyUsers = Schema::hasTable('users')
            && Schema::hasColumn('users', 'role_id');

        if (! $hasLegacyUsers) {
            Schema::dropIfExists('profiles');
        }
    }
};
