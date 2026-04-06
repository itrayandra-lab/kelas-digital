<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('share_domains', function (Blueprint $table) {
            $table->id();
            $table->string('domain_name', 255);
            $table->string('webhook_url', 255);
            $table->string('api_key', 64)->nullable();
            $table->string('status', 255)->default('inactive'); 
            $table->timestamp('created_at')->nullable();
            $table->timestamp('updated_at')->nullable();

            // Add indexes for better performance
            $table->index('domain_name');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('share_domains');
    }
};