<?php

namespace App\Console\Commands;

use App\Models\Bookmark;
use App\Models\Note;
use Illuminate\Console\Command;

class CleanupTrash extends Command
{
    protected $signature = 'brainvault:cleanup-trash';

    protected $description = 'Permanently delete items that have been in trash for more than 30 days';

    public function handle(): int
    {
        $cutoff = now()->subDays(30);

        $bookmarkCount = Bookmark::where('is_trashed', true)
            ->where('trashed_at', '<', $cutoff)
            ->delete();

        $noteCount = Note::where('is_trashed', true)
            ->where('trashed_at', '<', $cutoff)
            ->delete();

        $this->info("Permanently deleted {$bookmarkCount} bookmarks and {$noteCount} notes from trash.");

        return Command::SUCCESS;
    }
}
