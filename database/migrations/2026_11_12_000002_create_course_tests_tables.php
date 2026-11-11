<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('courses', function (Blueprint $table) {
            $table->unsignedTinyInteger('post_test_pass_score')->default(70)->after('meeting_link');
        });

        Schema::create('course_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            // single | multiple | true_false | essay
            $table->string('type', 20);
            $table->text('question');
            $table->unsignedInteger('order')->default(0);
            $table->timestamps();
        });

        Schema::create('question_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_question_id')->constrained()->cascadeOnDelete();
            $table->string('text', 500);
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            // pre | post
            $table->string('kind', 10);
            $table->unsignedTinyInteger('score');
            $table->boolean('passed');
            $table->json('answers');
            $table->timestamps();
            $table->index(['user_id', 'course_id', 'kind']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('question_options');
        Schema::dropIfExists('course_questions');

        Schema::table('courses', function (Blueprint $table) {
            $table->dropColumn('post_test_pass_score');
        });
    }
};
