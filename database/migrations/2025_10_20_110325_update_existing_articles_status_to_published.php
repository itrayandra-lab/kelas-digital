<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Update all existing articles to published status
        // This fixes WordPress migrated articles that were set to draft by default
        DB::table('articles')
            ->where('status', 'draft')
            ->update([
                'status' => 'published',
                'published_at' => DB::raw('created_at'), // Use created_at as published_at
                'updated_at' => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert articles back to draft status
        DB::table('articles')
            ->where('status', 'published')
            ->whereNull('scheduled_at') // Only revert non-scheduled articles
            ->update([
                'status' => 'draft',
                'published_at' => null,
                'updated_at' => now(),
            ]);
    }
};
