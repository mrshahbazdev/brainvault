<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\AIService;
use App\Services\SemanticSearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request, SemanticSearchService $searchService): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:1|max:500',
            'type' => 'nullable|in:all,bookmarks,notes',
            'limit' => 'nullable|integer|min:1|max:50',
        ]);

        $results = $searchService->search(
            $request->user()->id,
            $request->input('query'),
            $request->input('limit', 20),
        );

        return response()->json($results);
    }

    public function related(Request $request, SemanticSearchService $searchService): JsonResponse
    {
        $request->validate([
            'type' => 'required|in:bookmark,note',
            'id' => 'required|integer',
            'limit' => 'nullable|integer|min:1|max:20',
        ]);

        $related = $searchService->findRelated(
            $request->input('type'),
            $request->input('id'),
            $request->input('limit', 6),
        );

        return response()->json(['related' => $related]);
    }

    public function ask(Request $request, AIService $ai, SemanticSearchService $searchService): JsonResponse
    {
        $request->validate([
            'question' => 'required|string|min:2|max:1000',
        ]);

        $userId = $request->user()->id;
        $question = $request->input('question');

        // Get search results as context
        $searchResults = $searchService->search($userId, $question, 10);

        $context = [];

        if (!empty($searchResults['bookmarks'])) {
            foreach ($searchResults['bookmarks']->take(5) as $bookmark) {
                $context[] = [
                    'title' => $bookmark->title ?? $bookmark->url,
                    'content' => $bookmark->ai_summary ?? $bookmark->description ?? '',
                ];
            }
        }

        if (!empty($searchResults['notes'])) {
            foreach ($searchResults['notes']->take(5) as $note) {
                $context[] = [
                    'title' => $note->title ?? 'Untitled',
                    'content' => $note->content_plain ?? strip_tags($note->content ?? ''),
                ];
            }
        }

        $answer = $ai->askKnowledgeBase($question, $context);

        return response()->json([
            'answer' => $answer,
            'sources' => array_map(fn ($c) => $c['title'], $context),
        ]);
    }
}
