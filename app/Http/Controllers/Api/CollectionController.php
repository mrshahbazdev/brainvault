<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index(): JsonResponse
    {
        $collections = Auth::user()->collections()
            ->withCount('bookmarks')
            ->orderBy('sort_order')
            ->get();

        return response()->json($collections);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string', 'max:50'],
            'parent_id' => ['nullable', 'exists:collections,id'],
            'visibility' => ['nullable', 'in:private,public,unlisted'],
        ]);

        $validated['slug'] = Str::slug($validated['name']);
        $collection = Auth::user()->collections()->create($validated);

        return response()->json($collection, 201);
    }

    public function show(Collection $collection): JsonResponse
    {
        $this->authorize('view', $collection);

        return response()->json(
            $collection->load(['bookmarks', 'children'])
                ->loadCount('bookmarks')
        );
    }

    public function update(Request $request, Collection $collection): JsonResponse
    {
        $this->authorize('update', $collection);

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['nullable', 'string', 'max:7'],
            'icon' => ['nullable', 'string', 'max:50'],
            'sort_order' => ['nullable', 'integer'],
        ]);

        $collection->update($validated);

        return response()->json($collection->fresh());
    }

    public function destroy(Collection $collection): JsonResponse
    {
        $this->authorize('delete', $collection);

        $collection->delete();

        return response()->json(null, 204);
    }
}
