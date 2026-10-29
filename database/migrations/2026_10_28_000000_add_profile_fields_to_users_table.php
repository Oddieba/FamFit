<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'username')) {
                $table->string('username', 20)->nullable()->unique();
            }

            if (!Schema::hasColumn('users', 'profile_visible')) {
                $table->boolean('profile_visible')->default(false);
            }
        });
    }

    public function down(): void
    {
        $hasLegacyUsers = Schema::hasTable('users')
            && Schema::hasColumn('users', 'role_id');

        if (! $hasLegacyUsers && Schema::hasColumn('users', 'profile_visible')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('profile_visible'));
        }
    }
};
