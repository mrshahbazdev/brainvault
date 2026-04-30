<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('collections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('collections')->nullOnDelete();
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            $table->string('color', 7)->default('#6366F1');
            $table->string('icon', 50)->default('folder');
            $table->string('cover_image', 500)->nullable();
            $table->boolean('is_default')->default(false);
            $table->boolean('is_smart')->default(false);
            $table->json('smart_rules')->nullable();
            $table->integer('sort_order')->default(0);
            $table->string('visibility', 20)->default('private');
            $table->string('share_slug', 100)->unique()->nullable();
            $table->integer('item_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'slug']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('collections');
    }
};
