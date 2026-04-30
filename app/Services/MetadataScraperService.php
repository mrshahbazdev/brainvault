<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class MetadataScraperService
{
    public function scrape(string $url): array
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'BrainVault/1.0 (+https://brainvault.app)',
                    'Accept' => 'text/html,application/xhtml+xml',
                ])
                ->get($url);

            if (!$response->successful()) {
                return $this->fallback($url);
            }

            $html = $response->body();
            $contentType = $response->header('Content-Type', '');

            if (!str_contains($contentType, 'text/html')) {
                return $this->fallback($url, $contentType);
            }

            return $this->parseHtml($html, $url);
        } catch (\Exception $e) {
            return $this->fallback($url);
        }
    }

    protected function parseHtml(string $html, string $url): array
    {
        $dom = new \DOMDocument();
        @$dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xpath = new \DOMXPath($dom);

        $title = $this->extractTitle($xpath, $dom);
        $description = $this->extractDescription($xpath);
        $ogImage = $this->extractMetaContent($xpath, 'og:image');
        $siteName = $this->extractMetaContent($xpath, 'og:site_name');
        $favicon = $this->extractFavicon($xpath, $url);
        $excerpt = $this->extractExcerpt($xpath);
        $wordCount = $this->estimateWordCount($html);
        $readingTime = max(1, (int) ceil($wordCount / 238));
        $contentType = $this->detectContentType($xpath, $url);

        return [
            'title' => $title ? Str::limit($title, 497) : null,
            'description' => $description ? Str::limit($description, 1000) : null,
            'excerpt' => $excerpt ? Str::limit($excerpt, 500) : null,
            'og_image_url' => $this->absoluteUrl($ogImage, $url),
            'favicon_url' => $this->absoluteUrl($favicon, $url),
            'site_name' => $siteName ?: parse_url($url, PHP_URL_HOST),
            'word_count' => $wordCount,
            'reading_time' => $readingTime,
            'content_type' => $contentType,
            'scraped_at' => now(),
        ];
    }

    protected function extractTitle(\DOMXPath $xpath, \DOMDocument $dom): ?string
    {
        $ogTitle = $this->extractMetaContent($xpath, 'og:title');
        if ($ogTitle) {
            return $ogTitle;
        }

        $twitterTitle = $this->extractMetaByName($xpath, 'twitter:title');
        if ($twitterTitle) {
            return $twitterTitle;
        }

        $titleNodes = $dom->getElementsByTagName('title');
        if ($titleNodes->length > 0) {
            return trim($titleNodes->item(0)->textContent);
        }

        $h1 = $xpath->query('//h1');
        if ($h1->length > 0) {
            return trim($h1->item(0)->textContent);
        }

        return null;
    }

    protected function extractDescription(\DOMXPath $xpath): ?string
    {
        $ogDesc = $this->extractMetaContent($xpath, 'og:description');
        if ($ogDesc) {
            return $ogDesc;
        }

        return $this->extractMetaByName($xpath, 'description');
    }

    protected function extractFavicon(\DOMXPath $xpath, string $url): ?string
    {
        $icons = $xpath->query('//link[@rel="icon" or @rel="shortcut icon" or @rel="apple-touch-icon"]/@href');
        if ($icons->length > 0) {
            return $icons->item(0)->nodeValue;
        }

        $parsedUrl = parse_url($url);
        return ($parsedUrl['scheme'] ?? 'https') . '://' . ($parsedUrl['host'] ?? '') . '/favicon.ico';
    }

    protected function extractExcerpt(\DOMXPath $xpath): ?string
    {
        $paragraphs = $xpath->query('//article//p | //main//p | //div[@class="content"]//p | //p');
        $text = '';

        for ($i = 0; $i < min(3, $paragraphs->length); $i++) {
            $content = trim($paragraphs->item($i)->textContent);
            if (strlen($content) > 50) {
                $text .= $content . ' ';
            }
        }

        return $text ?: null;
    }

    protected function extractMetaContent(\DOMXPath $xpath, string $property): ?string
    {
        $nodes = $xpath->query("//meta[@property='{$property}']/@content");
        return $nodes->length > 0 ? trim($nodes->item(0)->nodeValue) : null;
    }

    protected function extractMetaByName(\DOMXPath $xpath, string $name): ?string
    {
        $nodes = $xpath->query("//meta[@name='{$name}']/@content");
        return $nodes->length > 0 ? trim($nodes->item(0)->nodeValue) : null;
    }

    protected function estimateWordCount(string $html): int
    {
        $text = strip_tags($html);
        $text = preg_replace('/\s+/', ' ', $text);
        return str_word_count($text);
    }

    protected function detectContentType(\DOMXPath $xpath, string $url): string
    {
        $ogType = $this->extractMetaContent($xpath, 'og:type');

        if ($ogType === 'article') {
            return 'article';
        }
        if ($ogType === 'video' || str_contains($url, 'youtube.com') || str_contains($url, 'vimeo.com')) {
            return 'video';
        }
        if (str_contains($url, 'github.com')) {
            return 'repository';
        }
        if (preg_match('/\.(pdf)$/i', parse_url($url, PHP_URL_PATH) ?? '')) {
            return 'pdf';
        }
        if (preg_match('/\.(png|jpg|jpeg|gif|svg|webp)$/i', parse_url($url, PHP_URL_PATH) ?? '')) {
            return 'image';
        }

        return 'webpage';
    }

    protected function absoluteUrl(?string $relative, string $base): ?string
    {
        if (!$relative) {
            return null;
        }
        if (str_starts_with($relative, 'http://') || str_starts_with($relative, 'https://')) {
            return $relative;
        }
        if (str_starts_with($relative, '//')) {
            return 'https:' . $relative;
        }

        $parsedBase = parse_url($base);
        $scheme = $parsedBase['scheme'] ?? 'https';
        $host = $parsedBase['host'] ?? '';

        if (str_starts_with($relative, '/')) {
            return "{$scheme}://{$host}{$relative}";
        }

        $path = $parsedBase['path'] ?? '/';
        $dir = dirname($path);
        return "{$scheme}://{$host}{$dir}/{$relative}";
    }

    protected function fallback(string $url, ?string $contentType = null): array
    {
        $parsedUrl = parse_url($url);
        $host = $parsedUrl['host'] ?? '';

        return [
            'title' => null,
            'description' => null,
            'excerpt' => null,
            'og_image_url' => null,
            'favicon_url' => "https://{$host}/favicon.ico",
            'site_name' => $host,
            'word_count' => null,
            'reading_time' => null,
            'content_type' => $contentType ? Str::before($contentType, ';') : 'webpage',
            'scraped_at' => now(),
        ];
    }
}
