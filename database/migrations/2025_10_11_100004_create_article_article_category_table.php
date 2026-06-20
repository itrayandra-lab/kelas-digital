<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('article_article_category', function (Blueprint $table) {
            $table->unsignedBigInteger('article_id');
            $table->unsignedBigInteger('article_category_id');

            $table->foreign('article_id')
                ->references('id')->on('articles')
                ->onDelete('cascade');

            $table->foreign('article_category_id')
                ->references('id')->on('article_categories')
                ->onDelete('cascade');

            $table->primary(['article_id', 'article_category_id'], 'article_category_primary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('article_article_category');
    }
};
