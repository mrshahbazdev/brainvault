<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action', 100);
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->string('subject_type', 100)->nullable();
            $table->json('properties')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['user_id', 'created_at']);
        });

        Schema::create('reading_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('bookmarks_added')->default(0);
            $table->integer('bookmarks_read')->default(0);
            $table->integer('notes_created')->default(0);
            $table->integer('highlights_made')->default(0);
            $table->integer('time_spent_seconds')->default(0);
            $table->json('topics_explored')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'date']);
        });

        Schema::create('imports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('source', 100);
            $table->string('file_path', 500)->nullable();
            $table->string('status', 50)->default('pending');
            $table->integer('total_items')->default(0);
            $table->integer('processed_items')->default(0);
            $table->integer('failed_items')->default(0);
            $table->json('error_log')->nullable();
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imports');
        Schema::dropIfExists('reading_stats');
        Schema::dropIfExists('activities');
    }
};
