<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Bookmark;
use App\Models\Tag;
use App\Models\Topic;
use Illuminate\Support\Facades\Auth;

class KnowledgeGraphController extends Controller
{
    public function index()
    {
        $userId = Auth::id();

        $nodes = [];
        $links = [];
        $nodeIndex = [];

        // Add topic nodes
        $topics = Topic::where('user_id', $userId)->get();
        foreach ($topics as $topic) {
            $nodeId = "topic_{$topic->id}";
            $nodes[] = [
                'id' => $nodeId,
                'label' => $topic->name,
                'type' => 'topic',
                'size' => 12,
            ];
            $nodeIndex[$nodeId] = true;
        }

        // Add tag nodes
        $tags = Tag::where('user_id', $userId)->withCount('bookmarks')->get();
        foreach ($tags as $tag) {
            $nodeId = "tag_{$tag->id}";
            $nodes[] = [
                'id' => $nodeId,
                'label' => $tag->name,
                'type' => 'tag',
                'size' => max(6, min(14, $tag->bookmarks_count * 2)),
            ];
            $nodeIndex[$nodeId] = true;
        }

        // Add bookmark nodes (limit to 50 most recent for performance)
        $bookmarks = Bookmark::where('user_id', $userId)
            ->where('is_archived', false)
            ->with('tags')
            ->latest()
            ->limit(50)
            ->get();

        foreach ($bookmarks as $bookmark) {
            $nodeId = "bookmark_{$bookmark->id}";
            $nodes[] = [
                'id' => $nodeId,
                'label' => \Str::limit($bookmark->title ?? $bookmark->site_name ?? 'Untitled', 30),
                'type' => 'bookmark',
                'size' => 8,
            ];
            $nodeIndex[$nodeId] = true;

            // Link bookmark to its tags
            foreach ($bookmark->tags as $tag) {
                $tagNodeId = "tag_{$tag->id}";
                if (isset($nodeIndex[$tagNodeId])) {
                    $links[] = [
                        'source' => $nodeId,
                        'target' => $tagNodeId,
                    ];
                }
            }

            // Link bookmark to AI category topic if matching
            if ($bookmark->ai_category) {
                $matchingTopic = $topics->first(fn ($t) => strtolower($t->name) === strtolower($bookmark->ai_category));
                if ($matchingTopic) {
                    $links[] = [
                        'source' => $nodeId,
                        'target' => "topic_{$matchingTopic->id}",
                    ];
                }
            }
        }

        // Connect tags that co-occur on same bookmarks
        $tagPairs = [];
        foreach ($bookmarks as $bookmark) {
            $bookmarkTags = $bookmark->tags->pluck('id')->toArray();
            for ($i = 0; $i < count($bookmarkTags); $i++) {
                for ($j = $i + 1; $j < count($bookmarkTags); $j++) {
                    $pair = min($bookmarkTags[$i], $bookmarkTags[$j]) . '_' . max($bookmarkTags[$i], $bookmarkTags[$j]);
                    if (!isset($tagPairs[$pair])) {
                        $tagPairs[$pair] = true;
                        $links[] = [
                            'source' => "tag_{$bookmarkTags[$i]}",
                            'target' => "tag_{$bookmarkTags[$j]}",
                        ];
                    }
                }
            }
        }

        $graphData = ['nodes' => $nodes, 'links' => $links];

        return view('knowledge-graph.index', compact('graphData'));
    }
}
