<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            // Resume point and the furthest point the server accepted as really watched (seconds).
            $table->unsignedInteger('position')->default(0)->after('lesson_id');
            $table->unsignedInteger('watched_seconds')->default(0)->after('position');
            $table->unsignedInteger('video_duration')->nullable()->after('watched_seconds');
            $table->timestamp('started_at')->nullable()->after('video_duration');
            $table->timestamp('last_ping_at')->nullable()->after('started_at');
        });
    }

    public function down(): void
    {
        Schema::table('lesson_progress', function (Blueprint $table) {
            $table->dropColumn(['position', 'watched_seconds', 'video_duration', 'started_at', 'last_ping_at']);
        });
    }
};
