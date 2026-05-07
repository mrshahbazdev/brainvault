<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Highlight;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HighlightController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $highlights = Auth::user()->highlights()
            ->when($request->bookmark_id, fn ($q, $id) => $q->where('bookmark_id', $id))
            ->when($request->task_id, fn ($q, $id) => $q->where('task_id', $id))
            ->when($request->page_url, function ($q) use ($request) {
                $url = strtok($request->page_url, '#');
                $url = rtrim($url, '/');
                $q->where(function ($q2) use ($url) {
                    $q2->where('page_url', $url)
                       ->orWhere('page_url', $url . '/')
                       ->orWhere('page_url', 'LIKE', $url . '#%')
                       ->orWhere('page_url', 'LIKE', $url . '/#%');
                });
            })
            ->with(['bookmark', 'task', 'tags'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($highlights);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bookmark_id' => ['nullable', 'exists:bookmarks,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'text' => ['required', 'string'],
            'note' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
            'page_url' => ['required', 'url'],
            'start_xpath' => ['required', 'string'],
            'start_offset' => ['required', 'integer'],
            'end_xpath' => ['required', 'string'],
            'end_offset' => ['required', 'integer'],
            'surrounding_text' => ['nullable', 'string'],
        ]);

        // Auto-link to existing bookmark for this URL if no bookmark_id provided
        if (empty($validated['bookmark_id']) && !empty($validated['page_url'])) {
            $bookmark = Auth::user()->bookmarks()->where('url', $validated['page_url'])->first();
            if ($bookmark) {
                $validated['bookmark_id'] = $bookmark->id;
            }
        }

        $highlight = Auth::user()->highlights()->create($validated);

        return response()->json($highlight, 201);
    }

    public function show(Highlight $highlight): JsonResponse
    {
        $this->authorize('view', $highlight);

        return response()->json($highlight->load(['bookmark', 'task', 'tags']));
    }

    public function update(Request $request, Highlight $highlight): JsonResponse
    {
        $this->authorize('update', $highlight);

        $validated = $request->validate([
            'note' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $highlight->update($validated);

        return response()->json($highlight->fresh());
    }

    public function destroy(Highlight $highlight): JsonResponse
    {
        $this->authorize('delete', $highlight);

        $highlight->delete();

        return response()->json(null, 204);
    }
}
