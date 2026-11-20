<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Where a learner comes from, asked at sign-up: student, employee or general public, plus the university or
     * company name, so admins can tell a mixed class apart. Empty for accounts made before this was asked.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('origin_type', 20)->nullable()->after('email');
            $table->string('institution', 150)->nullable()->after('origin_type')->index();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['origin_type', 'institution']);
        });
    }
};
