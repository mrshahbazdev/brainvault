<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('research_projects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('status', 50)->default('active');
            $table->string('color', 7)->default('#8B5CF6');
            $table->string('icon', 50)->default('beaker');
            $table->date('deadline')->nullable();
            $table->decimal('progress', 5, 2)->default(0);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('research_project_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('research_project_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('itemable_id');
            $table->string('itemable_type', 100);
            $table->integer('sort_order')->default(0);
            $table->text('notes')->nullable();
            $table->timestamp('added_at')->useCurrent();
        });

        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('research_project_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('bookmark_id')->nullable()->constrained()->nullOnDelete();
            $table->string('title', 500);
            $table->text('description')->nullable();
            $table->string('status', 50)->default('pending');
            $table->string('priority', 20)->default('medium');
            $table->timestamp('due_date')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
        Schema::dropIfExists('research_project_items');
        Schema::dropIfExists('research_projects');
    }
};
