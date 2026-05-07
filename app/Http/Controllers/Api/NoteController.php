<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Note;
use App\Services\AIService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * @group Notes
 * @authenticated
 */
class NoteController extends Controller
{
    /**
     * List notes
     *
     * @queryParam search string Search by title. Example: meeting
     * @queryParam bookmark_id integer Filter by bookmark. Example: 1
     * @queryParam is_pinned boolean Filter pinned notes. Example: 1
     * @queryParam per_page integer Items per page. Example: 20
     */
    public function index(Request $request): JsonResponse
    {
        $notes = Auth::user()->notes()
            ->where('is_trashed', false)
            ->when($request->search, fn ($q, $search) => $q->where('title', 'like', "%{$search}%"))
            ->when($request->bookmark_id, fn ($q, $id) => $q->where('bookmark_id', $id))
            ->when($request->task_id, fn ($q, $id) => $q->where('task_id', $id))
            ->when($request->boolean('is_pinned'), fn ($q) => $q->where('is_pinned', true))
            ->with('tags')
            ->latest()
            ->paginate($request->per_page ?? 20);

        return response()->json($notes);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'content_html' => ['nullable', 'string'],
            'content_plain' => ['nullable', 'string'],
            'bookmark_id' => ['nullable', 'exists:bookmarks,id'],
            'task_id' => ['nullable', 'exists:tasks,id'],
            'note_type' => ['nullable', 'in:note,quick,checklist'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $note = Auth::user()->notes()->create($validated);

        return response()->json($note, 201);
    }

    public function show(Note $note): JsonResponse
    {
        $this->authorize('view', $note);

        return response()->json($note->load(['tags', 'bookmark', 'task']));
    }

    public function update(Request $request, Note $note): JsonResponse
    {
        $this->authorize('update', $note);

        $validated = $request->validate([
            'title' => ['nullable', 'string', 'max:500'],
            'content' => ['nullable', 'string'],
            'content_html' => ['nullable', 'string'],
            'content_plain' => ['nullable', 'string'],
            'is_pinned' => ['nullable', 'boolean'],
            'is_archived' => ['nullable', 'boolean'],
            'color' => ['nullable', 'string', 'max:7'],
        ]);

        $note->update($validated);

        return response()->json($note->fresh(['tags', 'bookmark', 'task']));
    }

    public function destroy(Note $note): JsonResponse
    {
        $this->authorize('delete', $note);

        $note->update([
            'is_trashed' => true,
            'trashed_at' => now(),
        ]);

        return response()->json(null, 204);
    }

    public function enhance(Request $request, Note $note, AIService $ai): JsonResponse
    {
        $this->authorize('update', $note);

        $request->validate([
            'action' => ['required', 'in:improve,expand,summarize,format'],
        ]);

        if (!$ai->isConfigured()) {
            return response()->json(['message' => 'AI service is not configured.'], 503);
        }

        $result = $ai->enhanceNote(
            $note->title ?? '',
            $note->content_plain ?? strip_tags($note->content ?? ''),
            $request->action,
        );

        if (!$result) {
            return response()->json(['message' => 'AI enhancement failed.'], 500);
        }

        AuditLog::log('note.ai_enhance', $note, null, [
            'action' => $request->action,
        ]);

        return response()->json([
            'enhanced_content' => $result,
            'action' => $request->action,
        ]);
    }
}
