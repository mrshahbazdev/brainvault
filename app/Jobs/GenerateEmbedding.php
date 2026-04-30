<?php

namespace App\Jobs;

use App\Models\Bookmark;
use App\Models\Note;
use App\Services\AIService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class GenerateEmbedding implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $backoff = 60;

    public function __construct(
        protected int $modelId,
        protected string $modelType,
    ) {}

    public function handle(AIService $ai): void
    {
        if (!$ai->isConfigured()) {
            return;
        }

        $text = $this->getTextForEmbedding();
        if (!$text) {
            return;
        }

        $embedding = $ai->generateEmbedding($text);
        if (!$embedding) {
            return;
        }

        $vectorString = '[' . implode(',', $embedding) . ']';

        DB::table('embeddings')->updateOrInsert(
            [
                'embeddable_type' => $this->modelType,
                'embeddable_id' => $this->modelId,
            ],
            [
                'embedding' => $vectorString,
                'model' => 'text-embedding-3-small',
                'updated_at' => now(),
                'created_at' => now(),
            ],
        );
    }

    protected function getTextForEmbedding(): ?string
    {
        return match ($this->modelType) {
            'bookmark' => $this->getBookmarkText(),
            'note' => $this->getNoteText(),
            default => null,
        };
    }

    protected function getBookmarkText(): ?string
    {
        $bookmark = Bookmark::find($this->modelId);
        if (!$bookmark) {
            return null;
        }

        $parts = array_filter([
            $bookmark->title,
            $bookmark->description,
            $bookmark->excerpt,
            $bookmark->ai_summary,
            is_array($bookmark->ai_keywords) ? implode(', ', $bookmark->ai_keywords) : null,
        ]);

        return implode("\n", $parts) ?: null;
    }

    protected function getNoteText(): ?string
    {
        $note = Note::find($this->modelId);
        if (!$note) {
            return null;
        }

        $parts = array_filter([
            $note->title,
            $note->content_plain ?? strip_tags($note->content ?? ''),
        ]);

        return implode("\n", $parts) ?: null;
    }
}
