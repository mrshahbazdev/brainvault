<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('highlights', function (Blueprint $table) {
            $table->foreignId('task_id')->nullable()->after('bookmark_id')->constrained()->nullOnDelete();
            $table->index('task_id');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->foreignId('task_id')->nullable()->after('bookmark_id')->constrained()->nullOnDelete();
            $table->index('task_id');
        });
    }

    public function down(): void
    {
        Schema::table('highlights', function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_id');
        });

        Schema::table('notes', function (Blueprint $table) {
            $table->dropConstrainedForeignId('task_id');
        });
    }
};
