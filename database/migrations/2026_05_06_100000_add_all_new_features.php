<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Trash / Recycle Bin + Reading List + Broken Link + Page Snapshot for bookmarks
        Schema::table('bookmarks', function (Blueprint $table) {
            $table->boolean('is_trashed')->default(false)->after('is_read');
            $table->timestamp('trashed_at')->nullable()->after('is_trashed');
            $table->boolean('is_read_later')->default(false)->after('trashed_at');
            $table->timestamp('read_later_reminder_at')->nullable()->after('is_read_later');
            $table->string('link_status', 20)->nullable()->after('scraped_at'); // alive, dead, redirect, unknown
            $table->timestamp('link_checked_at')->nullable()->after('link_status');
            $table->string('snapshot_path', 500)->nullable()->after('link_checked_at');
            $table->timestamp('snapshot_at')->nullable()->after('snapshot_path');
            $table->string('share_token', 64)->unique()->nullable()->after('snapshot_at');

            $table->index(['user_id', 'is_trashed']);
            $table->index(['user_id', 'is_read_later']);
            $table->index(['user_id', 'link_status']);
        });

        // Shared Collections for teams
        Schema::create('collection_team', function (Blueprint $table) {
            $table->foreignId('collection_id')->constrained()->cascadeOnDelete();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->string('permission', 20)->default('view'); // view, edit, manage
            $table->foreignId('shared_by')->constrained('users')->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['collection_id', 'team_id']);
        });

        // Team Activity Feed
        Schema::create('team_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('action', 50); // bookmark_added, collection_created, note_added, etc.
            $table->nullableMorphs('subject');
            $table->json('properties')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['team_id', 'created_at']);
        });

        // User preferences for weekly digest, keyboard shortcuts, etc.
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('weekly_digest_enabled')->default(true)->after('settings');
            $table->json('keyboard_shortcuts')->nullable()->after('weekly_digest_enabled');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['weekly_digest_enabled', 'keyboard_shortcuts']);
        });

        Schema::dropIfExists('team_activities');
        Schema::dropIfExists('collection_team');

        Schema::table('bookmarks', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'is_trashed']);
            $table->dropIndex(['user_id', 'is_read_later']);
            $table->dropIndex(['user_id', 'link_status']);
            $table->dropColumn([
                'is_trashed', 'trashed_at', 'is_read_later', 'read_later_reminder_at',
                'link_status', 'link_checked_at', 'snapshot_path', 'snapshot_at', 'share_token',
            ]);
        });
    }
};
