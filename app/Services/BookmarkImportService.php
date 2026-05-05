<?php

namespace App\Services;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BookmarkImportService
{
    public function importChromeHtml(User $user, string $html): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $links = $xpath->query('//a[@href]');
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            for ($i = 0; $i < $links->length; $i++) {
                $anchor = $links->item($i);
                $url = $anchor->getAttribute('href');
                $title = trim($anchor->textContent);
                $addDate = $anchor->getAttribute('add_date');

                if (!$url || !str_starts_with($url, 'http')) {
                    $skipped++;
                    continue;
                }

                $exists = $user->bookmarks()->where('url', $url)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $user->bookmarks()->create([
                    'url' => Str::limit($url, 2000),
                    'title' => Str::limit($title, 497) ?: null,
                    'site_name' => parse_url($url, PHP_URL_HOST),
                    'favicon_url' => 'https://' . parse_url($url, PHP_URL_HOST) . '/favicon.ico',
                    'content_type' => 'webpage',
                    'created_at' => $addDate ? \Carbon\Carbon::createFromTimestamp($addDate) : now(),
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return ['imported' => $imported, 'skipped' => $skipped, 'total' => $links->length];
    }

    public function importPocketJson(User $user, array $data): array
    {
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            $items = $data['list'] ?? $data;

            foreach ($items as $item) {
                $url = $item['given_url'] ?? $item['resolved_url'] ?? null;
                if (!$url) {
                    $skipped++;
                    continue;
                }

                $exists = $user->bookmarks()->where('url', $url)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $user->bookmarks()->create([
                    'url' => Str::limit($url, 2000),
                    'title' => Str::limit($item['given_title'] ?? $item['resolved_title'] ?? null, 497),
                    'excerpt' => Str::limit($item['excerpt'] ?? null, 500),
                    'site_name' => parse_url($url, PHP_URL_HOST),
                    'word_count' => $item['word_count'] ?? null,
                    'reading_time' => isset($item['word_count']) ? max(1, (int) ceil($item['word_count'] / 238)) : null,
                    'content_type' => 'webpage',
                    'is_favorite' => ($item['favorite'] ?? '0') === '1',
                    'is_read' => ($item['status'] ?? '0') === '1',
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    public function importRaindropJson(User $user, array $data): array
    {
        $imported = 0;
        $skipped = 0;

        DB::beginTransaction();

        try {
            $items = $data['items'] ?? $data;

            foreach ($items as $item) {
                $url = $item['link'] ?? null;
                if (!$url) {
                    $skipped++;
                    continue;
                }

                $exists = $user->bookmarks()->where('url', $url)->exists();
                if ($exists) {
                    $skipped++;
                    continue;
                }

                $user->bookmarks()->create([
                    'url' => Str::limit($url, 2000),
                    'title' => Str::limit($item['title'] ?? null, 497),
                    'description' => $item['excerpt'] ?? $item['note'] ?? null,
                    'og_image_url' => $item['cover'] ?? null,
                    'site_name' => $item['domain'] ?? parse_url($url, PHP_URL_HOST),
                    'content_type' => $item['type'] ?? 'webpage',
                    'is_favorite' => $item['important'] ?? false,
                ]);

                $imported++;
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }

        return ['imported' => $imported, 'skipped' => $skipped];
    }

    public function exportJson(User $user): array
    {
        return $user->bookmarks()
            ->with(['tags', 'collections'])
            ->get()
            ->map(fn (Bookmark $b) => [
                'url' => $b->url,
                'title' => $b->title,
                'description' => $b->description,
                'site_name' => $b->site_name,
                'content_type' => $b->content_type,
                'is_favorite' => $b->is_favorite,
                'is_archived' => $b->is_archived,
                'is_read' => $b->is_read,
                'tags' => $b->tags->pluck('name')->toArray(),
                'collections' => $b->collections->pluck('name')->toArray(),
                'created_at' => $b->created_at->toISOString(),
            ])
            ->toArray();
    }

    public function exportHtml(User $user): string
    {
        $bookmarks = $user->bookmarks()->orderBy('title')->get();

        $html = "<!DOCTYPE NETSCAPE-Bookmark-file-1>\n";
        $html .= "<!-- BrainVault Export -->\n";
        $html .= "<META HTTP-EQUIV=\"Content-Type\" CONTENT=\"text/html; charset=UTF-8\">\n";
        $html .= "<TITLE>BrainVault Bookmarks</TITLE>\n";
        $html .= "<H1>BrainVault Bookmarks</H1>\n";
        $html .= "<DL><p>\n";

        foreach ($bookmarks as $bookmark) {
            $timestamp = $bookmark->created_at->timestamp;
            $title = htmlspecialchars($bookmark->title ?? $bookmark->url);
            $url = htmlspecialchars($bookmark->url);
            $html .= "    <DT><A HREF=\"{$url}\" ADD_DATE=\"{$timestamp}\">{$title}</A>\n";
        }

        $html .= "</DL><p>\n";

        return $html;
    }
}
