<?php

namespace App\Livewire\Analytics;

use App\Models\Bookmark;
use App\Models\Highlight;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AnalyticsDashboard extends Component
{
    public string $period = '30';

    public function render()
    {
        $userId = Auth::id();

        // Overview stats
        $stats = [
            'total_bookmarks' => Bookmark::where('user_id', $userId)->count(),
            'total_notes' => Note::where('user_id', $userId)->where('is_trashed', false)->count(),
            'total_highlights' => Highlight::where('user_id', $userId)->count(),
            'read_bookmarks' => Bookmark::where('user_id', $userId)->where('is_read', true)->count(),
            'favorites' => Bookmark::where('user_id', $userId)->where('is_favorite', true)->count(),
        ];

        // Bookmarks over time (last N days)
        $bookmarksByDay = Bookmark::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays((int) $this->period))
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        // Top domains
        $topDomains = Bookmark::where('user_id', $userId)
            ->whereNotNull('site_name')
            ->select('site_name', DB::raw('count(*) as count'))
            ->groupBy('site_name')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Content type distribution
        $contentTypes = Bookmark::where('user_id', $userId)
            ->select('content_type', DB::raw('count(*) as count'))
            ->groupBy('content_type')
            ->get()
            ->pluck('count', 'content_type')
            ->toArray();

        // AI categories distribution
        $aiCategories = Bookmark::where('user_id', $userId)
            ->whereNotNull('ai_category')
            ->select('ai_category', DB::raw('count(*) as count'))
            ->groupBy('ai_category')
            ->orderByDesc('count')
            ->limit(10)
            ->get();

        // Reading stats
        $readingStats = [
            'total_words' => Bookmark::where('user_id', $userId)->sum('word_count') ?? 0,
            'total_reading_time' => Bookmark::where('user_id', $userId)->sum('reading_time') ?? 0,
            'avg_reading_time' => Bookmark::where('user_id', $userId)->whereNotNull('reading_time')->avg('reading_time') ?? 0,
        ];

        // Streak calculation
        $streak = $this->calculateStreak($userId);

        return view('livewire.analytics.analytics-dashboard', compact(
            'stats', 'bookmarksByDay', 'topDomains', 'contentTypes', 'aiCategories', 'readingStats', 'streak'
        ))->layout('layouts.app', ['title' => 'Analytics']);
    }

    protected function calculateStreak(int $userId): int
    {
        $dates = Bookmark::where('user_id', $userId)
            ->where('created_at', '>=', now()->subDays(365))
            ->select(DB::raw('DATE(created_at) as date'))
            ->distinct()
            ->orderByDesc('date')
            ->pluck('date')
            ->toArray();

        if (empty($dates)) {
            return 0;
        }

        $streak = 0;
        $expected = now()->format('Y-m-d');

        foreach ($dates as $date) {
            if ($date === $expected) {
                $streak++;
                $expected = now()->subDays($streak)->format('Y-m-d');
            } else {
                break;
            }
        }

        return $streak;
    }
}
