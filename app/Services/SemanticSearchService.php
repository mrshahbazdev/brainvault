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
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('description', 'like', "%{$query}%")
                    ->orWhere('url', 'like', "%{$query}%")
                    ->orWhere('site_name', 'like', "%{$query}%")
                    ->orWhere('ai_summary', 'like', "%{$query}%");
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
                $q->where('title', 'like', "%{$query}%")
                    ->orWhere('content_plain', 'like', "%{$query}%");
            })
            ->where('is_trashed', false)
            ->limit($limit)
            ->get();
    }

    protected function semanticSearch(int $userId, array $embedding, int $limit): Collection
    {
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return $this->semanticSearchPgsql($userId, $embedding, $limit);
        }

        return $this->semanticSearchFallback($userId, $embedding, $limit);
    }

    protected function semanticSearchPgsql(int $userId, array $embedding, int $limit): Collection
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

        return $this->hydrateResults($results);
    }

    protected function semanticSearchFallback(int $userId, array $embedding, int $limit): Collection
    {
        $bookmarkIds = Bookmark::where('user_id', $userId)
            ->where('is_archived', false)
            ->pluck('id');

        $noteIds = Note::where('user_id', $userId)
            ->where('is_trashed', false)
            ->pluck('id');

        $rows = DB::table('embeddings')
            ->where(function ($q) use ($bookmarkIds, $noteIds) {
                $q->where(function ($q2) use ($bookmarkIds) {
                    $q2->where('embeddable_type', 'bookmark')
                        ->whereIn('embeddable_id', $bookmarkIds);
                })->orWhere(function ($q2) use ($noteIds) {
                    $q2->where('embeddable_type', 'note')
                        ->whereIn('embeddable_id', $noteIds);
                });
            })
            ->whereNotNull('embedding')
            ->get();

        $scored = $rows->map(function ($row) use ($embedding) {
            $stored = json_decode($row->embedding, true);
            if (!is_array($stored)) {
                return null;
            }

            return (object) [
                'embeddable_type' => $row->embeddable_type,
                'embeddable_id' => $row->embeddable_id,
                'similarity' => $this->cosineSimilarity($embedding, $stored),
            ];
        })
            ->filter()
            ->sortByDesc('similarity')
            ->take($limit)
            ->values();

        return $this->hydrateResults($scored);
    }

    protected function cosineSimilarity(array $a, array $b): float
    {
        $dotProduct = 0.0;
        $normA = 0.0;
        $normB = 0.0;

        for ($i = 0, $len = count($a); $i < $len; $i++) {
            $dotProduct += $a[$i] * $b[$i];
            $normA += $a[$i] * $a[$i];
            $normB += $b[$i] * $b[$i];
        }

        $denominator = sqrt($normA) * sqrt($normB);

        return $denominator > 0 ? $dotProduct / $denominator : 0.0;
    }

    protected function hydrateResults($results): Collection
    {
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
        $driver = DB::getDriverName();

        if ($driver === 'pgsql') {
            return $this->findRelatedPgsql($type, $id, $userId, $limit);
        }

        return $this->findRelatedFallback($type, $id, $userId, $embedding, $limit);
    }

    protected function findRelatedPgsql(string $type, int $id, int $userId, int $limit): Collection
    {
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

        return $this->hydrateResults($results);
    }

    protected function findRelatedFallback(string $type, int $id, int $userId, string $embedding, int $limit): Collection
    {
        $sourceEmbedding = json_decode($embedding, true);
        if (!is_array($sourceEmbedding)) {
            return collect();
        }

        $rows = DB::table('embeddings')
            ->where(function ($q) use ($type, $id) {
                $q->where('embeddable_type', '!=', $type)
                    ->orWhere('embeddable_id', '!=', $id);
            })
            ->where(function ($q) use ($userId) {
                $q->where(function ($q2) use ($userId) {
                    $q2->where('embeddable_type', 'bookmark')
                        ->whereIn('embeddable_id', Bookmark::where('user_id', $userId)->pluck('id'));
                })->orWhere(function ($q2) use ($userId) {
                    $q2->where('embeddable_type', 'note')
                        ->whereIn('embeddable_id', Note::where('user_id', $userId)->pluck('id'));
                });
            })
            ->whereNotNull('embedding')
            ->get();

        $scored = $rows->map(function ($row) use ($sourceEmbedding) {
            $stored = json_decode($row->embedding, true);
            if (!is_array($stored)) {
                return null;
            }

            return (object) [
                'embeddable_type' => $row->embeddable_type,
                'embeddable_id' => $row->embeddable_id,
                'similarity' => $this->cosineSimilarity($sourceEmbedding, $stored),
            ];
        })
            ->filter()
            ->sortByDesc('similarity')
            ->take($limit)
            ->values();

        return $this->hydrateResults($scored);
    }
}
