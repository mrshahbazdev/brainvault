<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Enable pgvector extension
        DB::statement('CREATE EXTENSION IF NOT EXISTS vector');

        Schema::create('embeddings', function (Blueprint $table) {
            $table->id();
            $table->string('embeddable_type');
            $table->unsignedBigInteger('embeddable_id');
            $table->string('model')->default('text-embedding-3-small');
            $table->timestamps();

            $table->unique(['embeddable_type', 'embeddable_id']);
            $table->index(['embeddable_type', 'embeddable_id']);
        });

        // Add vector column (1536 dimensions for text-embedding-3-small)
        DB::statement('ALTER TABLE embeddings ADD COLUMN embedding vector(1536)');

        // Create HNSW index for fast similarity search
        DB::statement('CREATE INDEX embeddings_embedding_idx ON embeddings USING hnsw (embedding vector_cosine_ops)');
    }

    public function down(): void
    {
        Schema::dropIfExists('embeddings');
    }
};
