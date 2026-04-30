<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bookmarks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('url');
            $table->string('title', 500)->nullable();
            $table->text('description')->nullable();
            $table->text('excerpt')->nullable();
            $table->string('favicon_url', 500)->nullable();
            $table->string('og_image_url', 500)->nullable();
            $table->string('screenshot_path', 500)->nullable();
            $table->string('site_name')->nullable();
            $table->string('content_type', 50)->default('webpage');
            $table->integer('reading_time')->nullable();
            $table->integer('word_count')->nullable();
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_favorite')->default(false);
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->decimal('read_progress', 5, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->text('ai_summary')->nullable();
            $table->json('ai_keywords')->nullable();
            $table->string('ai_category', 100)->nullable();
            $table->timestamp('scraped_at')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index(['user_id', 'created_at']);
            $table->index(['user_id', 'content_type']);
        });

        Schema::create('bookmark_collection', function (Blueprint $table) {
            $table->foreignId('bookmark_id')->constrained()->cascadeOnDelete();
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->integer('sort_order')->default(0);
            $table->timestamp('added_at')->useCurrent();

            $table->primary(['bookmark_id', 'collection_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookmark_collection');
        Schema::dropIfExists('bookmarks');
    }
};
