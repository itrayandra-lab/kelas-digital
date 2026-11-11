<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            // graded | pending (has essays waiting for an admin to mark them)
            $table->string('status', 10)->default('graded')->after('kind');
            $table->unsignedTinyInteger('score')->nullable()->change();
            $table->json('essay_marks')->nullable()->after('answers');
            $table->timestamp('reviewed_at')->nullable()->after('essay_marks');
        });
    }

    public function down(): void
    {
        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['status', 'essay_marks', 'reviewed_at']);
        });
    }
};
