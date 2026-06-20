<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->string('snap_token')->nullable()->after('payment_proof');
            $table->string('transaction_id')->nullable()->after('snap_token');
            $table->text('midtrans_response')->nullable()->after('transaction_id');
        });
    }

    public function down(): void
    {
        Schema::table('enrollments', function (Blueprint $table) {
            $table->dropColumn(['snap_token', 'transaction_id', 'midtrans_response']);
        });
    }
};
