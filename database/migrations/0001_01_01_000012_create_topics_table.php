<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('topics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color', 7)->nullable();
            $table->string('icon', 50)->nullable();
            $table->boolean('ai_generated')->default(false);
            $table->integer('item_count')->default(0);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['user_id', 'slug']);
        });

        Schema::create('topic_connections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('source_topic_id')->constrained('topics')->cascadeOnDelete();
            $table->foreignId('target_topic_id')->constrained('topics')->cascadeOnDelete();
            $table->decimal('strength', 3, 2)->default(0.50);
            $table->boolean('ai_generated')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->unique(['source_topic_id', 'target_topic_id']);
        });

        Schema::create('topicables', function (Blueprint $table) {
            $table->foreignId('topic_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('topicable_id');
            $table->string('topicable_type', 100);
            $table->decimal('relevance_score', 3, 2)->default(0.50);
            $table->boolean('ai_assigned')->default(true);
            $table->timestamp('created_at')->useCurrent();

            $table->primary(['topic_id', 'topicable_id', 'topicable_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('topicables');
        Schema::dropIfExists('topic_connections');
        Schema::dropIfExists('topics');
    }
};
