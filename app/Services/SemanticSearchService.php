<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\Note;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SemanticSearchService
{
    public function __construct(
        protected AIService $ai,
    ) {}

    public function search(int $userId, string $query, int $limit = 20): array
    {
        $embedding = $this->ai->generateEmbedding($query);

        $results = [
            'bookmarks' => $this->searchBookmarks($userId, $query, $limit),
            'notes' => $this->searchNotes($userId, $query, $limit),
        ];

        // Add semantic results if we have an embedding
        if ($embedding) {
            $semanticResults = $this->semanticSearch($userId, $embedding, $limit);
            $results['semantic'] = $semanticResults;
        }

        return $results;
    }

    protected function searchBookmarks(int $userId, string $query, int $limit): Collection
    {
        return Bookmark::where('user_id', $userId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('description', 'ilike', "%{$query}%")
                    ->orWhere('url', 'ilike', "%{$query}%")
                    ->orWhere('site_name', 'ilike', "%{$query}%")
                    ->orWhere('ai_summary', 'ilike', "%{$query}%");
            })
            ->where('is_archived', false)
            ->with(['tags', 'collections'])
            ->limit($limit)
            ->get();
    }

    protected function searchNotes(int $userId, string $query, int $limit): Collection
    {
        return Note::where('user_id', $userId)
            ->where(function ($q) use ($query) {
                $q->where('title', 'ilike', "%{$query}%")
                    ->orWhere('content_plain', 'ilike', "%{$query}%");
            })
            ->where('is_trashed', false)
            ->limit($limit)
            ->get();
    }

    protected function semanticSearch(int $userId, array $embedding, int $limit): Collection
    {
        $vectorString = '[' . implode(',', $embedding) . ']';

        $results = DB::select("
            SELECT
                e.embeddable_type,
                e.embeddable_id,
                1 - (e.embedding <=> ?::vector) as similarity
            FROM embeddings e
            WHERE
                CASE
                    WHEN e.embeddable_type = 'bookmark' THEN
                        e.embeddable_id IN (SELECT id FROM bookmarks WHERE user_id = ? AND is_archived = false)
                    WHEN e.embeddable_type = 'note' THEN
                        e.embeddable_id IN (SELECT id FROM notes WHERE user_id = ? AND is_trashed = false)
                    ELSE false
                END
            ORDER BY e.embedding <=> ?::vector
            LIMIT ?
        ", [$vectorString, $userId, $userId, $vectorString, $limit]);

        return collect($results)->map(function ($row) {
            $model = match ($row->embeddable_type) {
                'bookmark' => Bookmark::with(['tags', 'collections'])->find($row->embeddable_id),
                'note' => Note::find($row->embeddable_id),
                default => null,
            };

            return $model ? [
                'type' => $row->embeddable_type,
                'similarity' => round($row->similarity, 4),
                'item' => $model,
            ] : null;
        })->filter()->values();
    }

    public function findRelated(string $type, int $id, int $limit = 6): Collection
    {
        $embedding = DB::table('embeddings')
            ->where('embeddable_type', $type)
            ->where('embeddable_id', $id)
            ->value('embedding');

        if (!$embedding) {
            return collect();
        }

        $model = match ($type) {
            'bookmark' => Bookmark::find($id),
            'note' => Note::find($id),
            default => null,
        };

        if (!$model) {
            return collect();
        }

        $userId = $model->user_id;

        $results = DB::select("
            SELECT
                e.embeddable_type,
                e.embeddable_id,
                1 - (e.embedding <=> (SELECT embedding FROM embeddings WHERE embeddable_type = ? AND embeddable_id = ?)) as similarity
            FROM embeddings e
            WHERE NOT (e.embeddable_type = ? AND e.embeddable_id = ?)
            AND CASE
                WHEN e.embeddable_type = 'bookmark' THEN
                    e.embeddable_id IN (SELECT id FROM bookmarks WHERE user_id = ?)
                WHEN e.embeddable_type = 'note' THEN
                    e.embeddable_id IN (SELECT id FROM notes WHERE user_id = ?)
                ELSE false
            END
            ORDER BY similarity DESC
            LIMIT ?
        ", [$type, $id, $type, $id, $userId, $userId, $limit]);

        return collect($results)->map(function ($row) {
            $model = match ($row->embeddable_type) {
                'bookmark' => Bookmark::with('tags')->find($row->embeddable_id),
                'note' => Note::find($row->embeddable_id),
                default => null,
            };

            return $model ? [
                'type' => $row->embeddable_type,
                'similarity' => round($row->similarity, 4),
                'item' => $model,
            ] : null;
        })->filter()->values();
    }
}
