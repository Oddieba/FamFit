<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'name')) {
            Schema::table('users', function (Blueprint $table): void {
                $table->string('name')->default('');
            });
        }

        $nameSource = Schema::hasColumn('users', 'username')
            ? DB::raw("COALESCE(NULLIF(username, ''), email)")
            : DB::raw('email');

        DB::table('users')
            ->whereNull('name')
            ->orWhere('name', '')
            ->update(['name' => $nameSource]);
    }

    public function down(): void
    {
        $hasLegacyUsers = Schema::hasTable('users')
            && Schema::hasColumn('users', 'role_id');

        if ($hasLegacyUsers && Schema::hasColumn('users', 'name')) {
            Schema::table('users', fn (Blueprint $table) => $table->dropColumn('name'));
        }
    }
};
