<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bookmark_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('notes')->nullOnDelete();
            $table->string('title', 500)->nullable();
            $table->text('content')->nullable();
            $table->text('content_html')->nullable();
            $table->text('content_plain')->nullable();
            $table->string('note_type', 50)->default('note');
            $table->boolean('is_pinned')->default(false);
            $table->boolean('is_archived')->default(false);
            $table->boolean('is_trashed')->default(false);
            $table->timestamp('trashed_at')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('cover_image', 500)->nullable();
            $table->integer('word_count')->default(0);
            $table->text('ai_summary')->nullable();
            $table->json('ai_keywords')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('bookmark_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notes');
    }
};
