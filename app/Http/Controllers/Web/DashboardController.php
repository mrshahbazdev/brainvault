<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $cacheKey = "dashboard_stats_{$user->id}";

        $stats = Cache::remember($cacheKey, 900, function () use ($user) {
            return [
                'total_bookmarks' => $user->bookmarks()->count(),
                'total_notes' => $user->notes()->count(),
                'total_highlights' => $user->highlights()->count(),
                'total_collections' => $user->collections()->count(),
            ];
        });

        $recentBookmarks = $user->bookmarks()
            ->latest()
            ->take(8)
            ->get();

        $recentNotes = $user->notes()
            ->where('is_trashed', false)
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'recentBookmarks', 'recentNotes'));
    }
}
