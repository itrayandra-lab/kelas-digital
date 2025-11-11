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
        Schema::table('articles', function (Blueprint $table) {
            $table->unsignedBigInteger('views_count')->default(0)->after('published_at');
            $table->boolean('is_recommended')->default(false)->after('views_count');
            $table->datetime('recommended_at')->nullable()->after('is_recommended');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn(['views_count', 'is_recommended', 'recommended_at']);
        });
    }
};
