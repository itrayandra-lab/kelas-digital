<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * A paid course with free chapters ("sebagian gratis"): anyone who starts it for free studies those
     * chapters fully, tests included, and pays to open the rest.
     */
    public function up(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->boolean('is_free')->default(false)->after('order');
        });
    }

    public function down(): void
    {
        Schema::table('course_sections', function (Blueprint $table) {
            $table->dropColumn('is_free');
        });
    }
};
