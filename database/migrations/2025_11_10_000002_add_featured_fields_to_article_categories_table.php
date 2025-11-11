<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->boolean('is_featured_section')->default(false)->after('description');
            $table->datetime('featured_at')->nullable()->after('is_featured_section');
            $table->string('theme_color')->nullable()->after('featured_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('article_categories', function (Blueprint $table) {
            $table->dropColumn(['is_featured_section', 'featured_at', 'theme_color']);
        });
    }
};
