<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('code', 40)->unique();
            $table->string('discount_type', 10); // percent | fixed
            $table->unsignedInteger('discount_value');
            $table->foreignId('course_id')->nullable()->constrained()->cascadeOnDelete(); // null: every paid course
            $table->unsignedInteger('max_uses')->nullable(); // null: unlimited
            $table->unsignedInteger('used_count')->default(0);
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::table('enrollments', function (Blueprint $table) {
            $table->foreignId('voucher_id')->nullable()->after('payment_method')->constrained()->nullOnDelete();
            $table->unsignedInteger('paid_amount')->nullable()->after('voucher_id');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropConstrainedForeignId('voucher_id');
            $table->dropColumn('paid_amount');
        });

        Schema::dropIfExists('vouchers');
    }
};
