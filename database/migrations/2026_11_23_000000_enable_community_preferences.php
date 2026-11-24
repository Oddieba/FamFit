<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasColumn('users', 'profile_visible')) {
            Schema::table('users', fn (Blueprint $table) => $table->boolean('profile_visible')->default(false));
        }
        if (! Schema::hasColumn('users', 'preferences')) {
            Schema::table('users', fn (Blueprint $table) => $table->json('preferences')->nullable());
        }
    }

    public function down(): void
    {
        // Configuración también usa estas columnas y algunas instalaciones ya las tenían.
    }
};
