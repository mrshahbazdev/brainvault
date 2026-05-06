<?php

namespace App\Jobs;

use App\Events\BookmarkProcessed;
use App\Models\Bookmark;
use App\Models\Tag;
use App\Services\AIService;
use App\Services\MetadataScraperService;
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ProcessBookmarkAI implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 30;

    public function __construct(
        protected int $bookmarkId,
    ) {}

    public function handle(AIService $ai, MetadataScraperService $scraper): void
    {
        $bookmark = Bookmark::find($this->bookmarkId);
        if (!$bookmark) {
            return;
        }

        // Scrape content if not already done
        if (!$bookmark->scraped_at) {
            try {
                $metadata = $scraper->scrape($bookmark->url);
                $bookmark->update([
                    'title' => $bookmark->title ?: ($metadata['title'] ?? null),
                    'description' => $bookmark->description ?: ($metadata['description'] ?? null),
                    'favicon_url' => $metadata['favicon'] ?? null,
                    'og_image_url' => $metadata['image'] ?? null,
                    'site_name' => $metadata['site_name'] ?? null,
                    'word_count' => $metadata['word_count'] ?? null,
                    'reading_time' => $metadata['reading_time'] ?? null,
                    'content_type' => $metadata['content_type'] ?? 'webpage',
                    'excerpt' => $metadata['excerpt'] ?? null,
                    'scraped_at' => now(),
                ]);
            } catch (\Exception $e) {
                Log::warning('Scraping failed for bookmark', [
                    'bookmark_id' => $bookmark->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        if (!$ai->isConfigured()) {
            return;
        }

        $title = $bookmark->title ?? '';
        $content = $bookmark->description ?? $bookmark->excerpt ?? '';

        if (empty($title) && empty($content)) {
            return;
        }

        $textForAI = "{$title}\n{$content}";

        // Generate AI summary
        if (!$bookmark->ai_summary) {
            $summary = $ai->generateSummary($title, $content, $bookmark->url);
            if ($summary) {
                $bookmark->ai_summary = $summary;
            }
        }

        // Extract keywords
        if (empty($bookmark->ai_keywords)) {
            $keywords = $ai->extractKeywords($title, $content);
            if (!empty($keywords)) {
                $bookmark->ai_keywords = $keywords;
            }
        }

        // Suggest category
        if (!$bookmark->ai_category) {
            $category = $ai->suggestCategory($title, $content);
            if ($category) {
                $bookmark->ai_category = $category;
            }
        }

        $bookmark->save();

        // Smart Tagging — auto-attach tags from AI keywords
        if (!empty($bookmark->ai_keywords) && $bookmark->tags()->count() === 0) {
            $userId = $bookmark->user_id;
            $tagIds = [];
            foreach (array_slice($bookmark->ai_keywords, 0, 5) as $keyword) {
                $slug = Str::slug($keyword);
                if (empty($slug)) {
                    continue;
                }
                $tag = Tag::firstOrCreate(
                    ['user_id' => $userId, 'slug' => $slug],
                    ['name' => $keyword, 'color' => null]
                );
                $tag->increment('usage_count');
                $tagIds[] = $tag->id;
            }
            if (!empty($tagIds)) {
                $bookmark->tags()->syncWithoutDetaching($tagIds);
            }
        }

        // Broadcast real-time notification
        BookmarkProcessed::dispatch($bookmark);

        // Generate embedding for semantic search
        GenerateEmbedding::dispatch($this->bookmarkId, 'bookmark');
    }
}
