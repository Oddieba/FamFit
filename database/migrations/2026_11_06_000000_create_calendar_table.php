<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $hasLegacyTable = Schema::hasTable('famfit_activities');
        $hasCalendarTable = Schema::hasTable('calendars');

        if ($hasLegacyTable && $hasCalendarTable) {
            throw new RuntimeException('Both famfit_activities and calendars exist; resolve the duplicate tables before migrating.');
        }

        if ($hasLegacyTable) {
            Schema::rename('famfit_activities', 'calendars');

            return;
        }

        if ($hasCalendarTable) {
            return;
        }

        Schema::create('calendars', function (Blueprint $table) {
            $table->id();
            $table->string('title', 100);
            $table->string('activity_type', 30);
            $table->date('activity_date');
            $table->time('start_time');
            $table->time('end_time');
            $table->unsignedSmallInteger('duration_minutes');
            $table->text('notes')->nullable();
            $table->string('status', 20)->default('planned');
            $table->unsignedInteger('points')->default(0);
            $table->timestamps();
            $table->index('activity_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('calendars');
        Schema::dropIfExists('famfit_activities');
    }
};
