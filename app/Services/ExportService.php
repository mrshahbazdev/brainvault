<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\Note;
use Illuminate\Support\Collection;

class ExportService
{
    public function exportBookmarksMarkdown(int $userId): string
    {
        $bookmarks = Bookmark::where('user_id', $userId)
            ->where('is_trashed', false)
            ->with('tags', 'collections')
            ->orderBy('created_at', 'desc')
            ->get();

        $md = "# BrainVault Bookmarks Export\n\n";
        $md .= "Exported on: " . now()->format('Y-m-d H:i:s') . "\n";
        $md .= "Total: {$bookmarks->count()} bookmarks\n\n---\n\n";

        foreach ($bookmarks as $bookmark) {
            $md .= "## " . ($bookmark->title ?? $bookmark->url) . "\n\n";
            $md .= "- **URL:** [{$bookmark->url}]({$bookmark->url})\n";
            if ($bookmark->site_name) {
                $md .= "- **Site:** {$bookmark->site_name}\n";
            }
            if ($bookmark->ai_category) {
                $md .= "- **Category:** {$bookmark->ai_category}\n";
            }
            $tags = $bookmark->tags->pluck('name')->implode(', ');
            if ($tags) {
                $md .= "- **Tags:** {$tags}\n";
            }
            $collections = $bookmark->collections->pluck('name')->implode(', ');
            if ($collections) {
                $md .= "- **Collections:** {$collections}\n";
            }
            if ($bookmark->description) {
                $md .= "\n> {$bookmark->description}\n";
            }
            if ($bookmark->ai_summary) {
                $md .= "\n**AI Summary:** {$bookmark->ai_summary}\n";
            }
            $md .= "\n---\n\n";
        }

        return $md;
    }

    public function exportBookmarksJson(int $userId): array
    {
        return Bookmark::where('user_id', $userId)
            ->where('is_trashed', false)
            ->with('tags', 'collections')
            ->get()
            ->map(fn ($b) => [
                'title' => $b->title,
                'url' => $b->url,
                'description' => $b->description,
                'site_name' => $b->site_name,
                'category' => $b->ai_category,
                'tags' => $b->tags->pluck('name')->toArray(),
                'collections' => $b->collections->pluck('name')->toArray(),
                'ai_summary' => $b->ai_summary,
                'is_favorite' => $b->is_favorite,
                'is_read' => $b->is_read,
                'created_at' => $b->created_at?->toIso8601String(),
            ])
            ->toArray();
    }

    public function exportNotesMarkdown(int $userId): string
    {
        $notes = Note::where('user_id', $userId)
            ->where('is_trashed', false)
            ->with('tags')
            ->orderBy('created_at', 'desc')
            ->get();

        $md = "# BrainVault Notes Export\n\n";
        $md .= "Exported on: " . now()->format('Y-m-d H:i:s') . "\n";
        $md .= "Total: {$notes->count()} notes\n\n---\n\n";

        foreach ($notes as $note) {
            $md .= "## " . ($note->title ?? 'Untitled Note') . "\n\n";
            $tags = $note->tags->pluck('name')->implode(', ');
            if ($tags) {
                $md .= "**Tags:** {$tags}\n\n";
            }
            $md .= ($note->content_plain ?? strip_tags($note->content ?? '')) . "\n\n";
            $md .= "---\n\n";
        }

        return $md;
    }

    public function exportObsidianVault(int $userId): array
    {
        $bookmarks = Bookmark::where('user_id', $userId)
            ->where('is_trashed', false)
            ->with('tags')
            ->get();

        $files = [];

        foreach ($bookmarks as $bookmark) {
            $filename = preg_replace('/[^\w\s-]/', '', $bookmark->title ?? 'Untitled') . '.md';
            $tags = $bookmark->tags->pluck('name')->map(fn ($t) => "#$t")->implode(' ');

            $content = "---\n";
            $content .= "url: {$bookmark->url}\n";
            $content .= "category: " . ($bookmark->ai_category ?? '') . "\n";
            $content .= "created: " . ($bookmark->created_at?->format('Y-m-d') ?? '') . "\n";
            $content .= "tags: [" . $bookmark->tags->pluck('name')->implode(', ') . "]\n";
            $content .= "---\n\n";
            $content .= "# " . ($bookmark->title ?? $bookmark->url) . "\n\n";
            if ($bookmark->description) {
                $content .= "> {$bookmark->description}\n\n";
            }
            if ($bookmark->ai_summary) {
                $content .= "## AI Summary\n\n{$bookmark->ai_summary}\n\n";
            }
            $content .= "## Source\n\n[{$bookmark->url}]({$bookmark->url})\n";

            $files[$filename] = $content;
        }

        return $files;
    }

    public function exportNotionCsv(int $userId): string
    {
        $bookmarks = Bookmark::where('user_id', $userId)
            ->where('is_trashed', false)
            ->with('tags', 'collections')
            ->get();

        $csv = "Name,URL,Tags,Category,Description,Created\n";

        foreach ($bookmarks as $bookmark) {
            $name = str_replace('"', '""', $bookmark->title ?? $bookmark->url);
            $url = $bookmark->url;
            $tags = $bookmark->tags->pluck('name')->implode(', ');
            $category = $bookmark->ai_category ?? '';
            $desc = str_replace('"', '""', $bookmark->description ?? '');
            $created = $bookmark->created_at?->format('Y-m-d') ?? '';

            $csv .= "\"{$name}\",\"{$url}\",\"{$tags}\",\"{$category}\",\"{$desc}\",\"{$created}\"\n";
        }

        return $csv;
    }
}
