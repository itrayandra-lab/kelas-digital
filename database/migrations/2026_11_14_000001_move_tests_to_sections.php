<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('course_questions', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('course_id')->constrained('course_sections')->cascadeOnDelete();
            // pre | post
            $table->string('kind', 10)->default('post')->after('section_id');
            // Model answer for essays, used by the AI grader.
            $table->text('answer_key')->nullable()->after('question');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->foreignId('section_id')->nullable()->after('course_id')->constrained('course_sections')->cascadeOnDelete();
            // One question at a time: which question is on screen and when its timer started.
            $table->unsignedSmallInteger('current_index')->default(0)->after('status');
            $table->timestamp('question_started_at')->nullable()->after('current_index');
            $table->json('ai_feedback')->nullable()->after('essay_marks');
        });

        // Course-level tests become tests of the first section: the existing questions are the post-test,
        // and their choice questions are copied as the pre-test (pre-tests have no essays).
        foreach (DB::table('courses')->pluck('id') as $courseId) {
            $sectionId = DB::table('course_sections')->where('course_id', $courseId)->orderBy('order')->orderBy('id')->value('id');

            if (! $sectionId) {
                continue;
            }

            DB::table('quiz_attempts')->where('course_id', $courseId)->update(['section_id' => $sectionId]);

            foreach (DB::table('course_questions')->where('course_id', $courseId)->whereNull('section_id')->orderBy('order')->get() as $q) {
                DB::table('course_questions')->where('id', $q->id)->update(['section_id' => $sectionId, 'kind' => 'post']);

                if ($q->type === 'essay') {
                    continue;
                }

                $copyId = DB::table('course_questions')->insertGetId([
                    'course_id' => $courseId,
                    'section_id' => $sectionId,
                    'kind' => 'pre',
                    'type' => $q->type,
                    'question' => $q->question,
                    'order' => $q->order,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                foreach (DB::table('question_options')->where('course_question_id', $q->id)->orderBy('id')->get() as $o) {
                    DB::table('question_options')->insert([
                        'course_question_id' => $copyId,
                        'text' => $o->text,
                        'is_correct' => $o->is_correct,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }
    }

    public function down(): void
    {
        DB::table('course_questions')->where('kind', 'pre')->delete();

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
            $table->dropColumn(['current_index', 'question_started_at', 'ai_feedback']);
        });

        Schema::table('course_questions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('section_id');
            $table->dropColumn(['kind', 'answer_key']);
        });
    }
};
