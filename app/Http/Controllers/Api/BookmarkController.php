<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Jobs\ProcessBookmarkAI;
use App\Models\Bookmark;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Bookmarks
 * @authenticated
 */
class BookmarkController extends Controller
{
    /**
     * List bookmarks
     *
     * Get paginated list of the authenticated user's bookmarks with optional filters.
     *
     * @queryParam search string Search bookmarks by title. Example: Laravel
     * @queryParam collection_id integer Filter by collection. Example: 1
     * @queryParam is_favorite boolean Filter favorites. Example: 1
     * @queryParam is_archived boolean Filter archived. Example: 0
     * @queryParam per_page integer Items per page (default 20). Example: 20
     */
    public function index(Request $request): JsonResponse
    {
        $bookmarks = Auth::user()->bookmarks()
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%")->orWhere('url', 'like', "%{$search}%"))
            ->when($request->content_type, fn ($q, $type) => $q->where('content_type', $type))
            ->when($request->boolean('is_favorite'), fn ($q) => $q->where('is_favorite', true))
            ->when($request->boolean('is_archived'), fn ($q) => $q->where('is_archived', true))
            ->with(['tags', 'collections'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($bookmarks);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'url' => ['required', 'url', 'max:2000'],
            'title' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'excerpt' => ['nullable', 'string'],
            'favicon_url' => ['nullable', 'url', 'max:500'],
            'og_image_url' => ['nullable', 'url', 'max:500'],
            'site_name' => ['nullable', 'string', 'max:255'],
            'content_type' => ['nullable', 'string', 'max:50'],
            'reading_time' => ['nullable', 'integer'],
            'word_count' => ['nullable', 'integer'],
            'collection_ids' => ['nullable', 'array'],
            'collection_ids.*' => ['exists:collections,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:100'],
        ]);

        $bookmark = Auth::user()->bookmarks()->firstWhere('url', $validated['url']);
        $isNew = false;

        if ($bookmark) {
            $bookmark->update($validated);
        } else {
            $bookmark = Auth::user()->bookmarks()->create($validated);
            $isNew = true;
        }

        if (!empty($validated['collection_ids'])) {
            $bookmark->collections()->syncWithoutDetaching($validated['collection_ids']);
        }

        if ($isNew) {
            // Dispatch AI processing job only for new bookmarks
            ProcessBookmarkAI::dispatch($bookmark->id);
        }

        return response()->json($bookmark->load(['tags', 'collections']), $isNew ? 201 : 200);
    }

    public function show(Bookmark $bookmark): JsonResponse
    {
        $this->authorize('view', $bookmark);

        return response()->json($bookmark->load(['tags', 'collections', 'notes', 'highlights']));
    }

    public function update(Request $request, Bookmark $bookmark): JsonResponse
    {
        $this->authorize('update', $bookmark);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:500'],
            'description' => ['nullable', 'string'],
            'is_favorite' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
            'is_read' => ['nullable', 'boolean'],
            'content_type' => ['nullable', 'string', 'max:50'],
        ]);

        $bookmark->update($validated);

        if ($request->has('is_read') && $request->boolean('is_read') && !$bookmark->read_at) {
            $bookmark->update(['read_at' => now()]);
        }

        return response()->json($bookmark->fresh(['tags', 'collections']));
    }

    public function destroy(Bookmark $bookmark): JsonResponse
    {
        $this->authorize('delete', $bookmark);

        $bookmark->delete();

        return response()->json(null, 204);
    }
}
