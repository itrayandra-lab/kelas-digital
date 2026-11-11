<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('course_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::table('lessons', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('course_id')->constrained('course_sections')->cascadeOnDelete();
            $table->string('type', 10)->default('video')->after('title');
            $table->longText('content')->nullable()->after('youtube_video_id');
            $table->string('youtube_video_id')->nullable()->change();
            $table->string('module')->nullable()->change();
        });

        Schema::create('lesson_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('lesson_id')->constrained()->cascadeOnDelete();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();
            $table->unique(['user_id', 'lesson_id']);
        });

        // Turn the free-text "module" of existing lessons into real sections, keeping their display order.
        $lessons = DB::table('lessons')->orderBy('course_id')->orderBy('module')->orderBy('order')->get();
        $sectionIds = [];
        $orderPerCourse = [];

        foreach ($lessons as $lesson) {
            $title = trim((string) $lesson->module) ?: 'Materi';
            $key = $lesson->course_id.'|'.$title;

            if (! isset($sectionIds[$key])) {
                $orderPerCourse[$lesson->course_id] = ($orderPerCourse[$lesson->course_id] ?? 0) + 1;
                $sectionIds[$key] = DB::table('course_sections')->insertGetId([
                    'course_id' => $lesson->course_id,
                    'title' => $title,
                    'order' => $orderPerCourse[$lesson->course_id],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            DB::table('lessons')->where('id', $lesson->id)->update(['section_id' => $sectionIds[$key]]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('lesson_progress');

        Schema::table('lessons', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
            $table->dropColumn(['type', 'content']);
        });

        Schema::dropIfExists('course_sections');
    }
};
