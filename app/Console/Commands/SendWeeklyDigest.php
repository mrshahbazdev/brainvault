<?php

namespace App\Console\Commands;

use App\Mail\WeeklyDigest;
use App\Models\Bookmark;
use App\Models\Note;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendWeeklyDigest extends Command
{
    protected $signature = 'brainvault:weekly-digest';

    protected $description = 'Send weekly digest emails to all active users';

    public function handle(): int
    {
        $users = User::whereNotNull('email_verified_at')
            ->where('last_active_at', '>=', now()->subDays(30))
            ->where('weekly_digest_enabled', true)
            ->cursor();

        $count = 0;

        foreach ($users as $user) {
            $weekStart = now()->subWeek();

            $stats = [
                'new_bookmarks' => Bookmark::where('user_id', $user->id)
                    ->where('created_at', '>=', $weekStart)->count(),
                'new_notes' => Note::where('user_id', $user->id)
                    ->where('created_at', '>=', $weekStart)->count(),
                'bookmarks_read' => Bookmark::where('user_id', $user->id)
                    ->where('read_at', '>=', $weekStart)->count(),
                'total_words_read' => Bookmark::where('user_id', $user->id)
                    ->where('read_at', '>=', $weekStart)
                    ->sum('word_count') ?? 0,
                'unread_count' => Bookmark::where('user_id', $user->id)
                    ->where('is_trashed', false)
                    ->where('is_read', false)
                    ->count(),
                'read_later_count' => Bookmark::where('user_id', $user->id)
                    ->where('is_trashed', false)
                    ->where('is_read_later', true)
                    ->count(),
            ];

            if ($stats['new_bookmarks'] === 0 && $stats['new_notes'] === 0) {
                continue;
            }

            $topBookmarks = Bookmark::where('user_id', $user->id)
                ->where('created_at', '>=', $weekStart)
                ->orderByDesc('is_favorite')
                ->orderByDesc('created_at')
                ->limit(5)
                ->get()
                ->map(fn ($b) => ['title' => $b->title ?? $b->url, 'url' => $b->url])
                ->toArray();

            $aiInsights = [];
            $topCategories = Bookmark::where('user_id', $user->id)
                ->where('created_at', '>=', $weekStart)
                ->whereNotNull('ai_category')
                ->select('ai_category')
                ->selectRaw('count(*) as cnt')
                ->groupBy('ai_category')
                ->orderByDesc('cnt')
                ->limit(3)
                ->pluck('ai_category')
                ->toArray();

            if (!empty($topCategories)) {
                $aiInsights[] = 'Your top interests this week: ' . implode(', ', $topCategories);
            }

            if ($stats['total_words_read'] > 10000) {
                $aiInsights[] = 'Impressive! You read over ' . number_format($stats['total_words_read']) . ' words this week.';
            }

            Mail::to($user)->queue(new WeeklyDigest($user, $stats, $topBookmarks, $aiInsights));
            $count++;
        }

        $this->info("Sent weekly digest to {$count} users.");

        return Command::SUCCESS;
    }
}
