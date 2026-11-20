<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * How many lessons of the module come before its pre-test, set by admins per module
     * (0 = pre-test first, 1 = after an overview video, ...). The post-test always closes the module.
     */
    public function up(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->unsignedSmallInteger('pretest_after')->default(0)->after('is_free');
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn('pretest_after');
        });
    }
};
