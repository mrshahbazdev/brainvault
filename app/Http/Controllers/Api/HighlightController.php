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
            ->when($request->page_url, fn ($q, $url) => $q->where('page_url', $url))
            ->with(['bookmark', 'tags'])
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($highlights);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'bookmark_id' => ['required', 'exists:bookmarks,id'],
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

        $highlight = Auth::user()->highlights()->create($validated);

        return response()->json($highlight, 201);
    }

    public function show(Highlight $highlight): JsonResponse
    {
        $this->authorize('view', $highlight);

        return response()->json($highlight->load(['bookmark', 'tags']));
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
