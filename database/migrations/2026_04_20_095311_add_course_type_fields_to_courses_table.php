<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->enum('course_type', ['paid', 'free'])->default('paid')->after('level');
            $table->text('benefits')->nullable()->after('course_type');
            $table->text('topics_preview')->nullable()->after('benefits');
            $table->dateTime('schedule_start')->nullable()->after('topics_preview');
            $table->dateTime('schedule_end')->nullable()->after('schedule_start');
            $table->string('meeting_platform')->nullable()->after('schedule_end');
        });
    }

    public function down(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn(['course_type', 'benefits', 'topics_preview', 'schedule_start', 'schedule_end', 'meeting_platform']);
        });
    }
};
