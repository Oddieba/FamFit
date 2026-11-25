<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('calendars') || Schema::hasColumn('calendars', 'reminder_minutes_before')) {
            return;
        }

        Schema::table('calendars', function (Blueprint $table) {
            $table->unsignedSmallInteger('reminder_minutes_before')->nullable()->after('start_time');
        });
    }

    public function down(): void
    {
        if (! Schema::hasTable('calendars') || ! Schema::hasColumn('calendars', 'reminder_minutes_before')) {
            return;
        }

        Schema::table('calendars', function (Blueprint $table) {
            $table->dropColumn('reminder_minutes_before');
        });
    }
};
