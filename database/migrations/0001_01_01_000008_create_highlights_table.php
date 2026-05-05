<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('highlights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('bookmark_id')->constrained()->cascadeOnDelete();
            $table->text('text');
            $table->text('note')->nullable();
            $table->string('color', 7)->default('#FBBF24');
            $table->text('page_url');
            $table->text('start_xpath');
            $table->integer('start_offset');
            $table->text('end_xpath');
            $table->integer('end_offset');
            $table->text('surrounding_text')->nullable();
            $table->string('screenshot_path', 500)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();

            $table->index('user_id');
            $table->index('bookmark_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('highlights');
    }
};
