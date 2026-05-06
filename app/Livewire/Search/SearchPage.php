<?php

namespace App\Livewire\Search;

use App\Models\Bookmark;
use App\Services\AIService;
use App\Services\SemanticSearchService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;

class SearchPage extends Component
{
    #[Url]
    public string $query = '';

    #[Url]
    public string $type = 'all';

    // Advanced filters
    #[Url]
    public string $dateFrom = '';

    #[Url]
    public string $dateTo = '';

    #[Url]
    public string $domain = '';

    #[Url]
    public string $tagFilter = '';

    #[Url]
    public string $readStatus = '';

    #[Url]
    public string $category = '';

    public bool $showAdvancedFilters = false;

    public array $results = [];
    public bool $loading = false;
    public ?string $aiAnswer = null;
    public array $relatedSuggestions = [];

    public function updatedQuery(): void
    {
        if (strlen($this->query) >= 2) {
            $this->search();
        } else {
            $this->results = [];
            $this->aiAnswer = null;
        }
    }

    public function search(): void
    {
        if (empty($this->query) && empty($this->domain) && empty($this->tagFilter) && empty($this->dateFrom)) {
            return;
        }

        $this->loading = true;

        // Build query for direct DB search with advanced filters
        $bookmarksQuery = Auth::user()->bookmarks()
            ->where('is_trashed', false)
            ->when($this->query, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', "%{$this->query}%")
                        ->orWhere('url', 'like', "%{$this->query}%")
                        ->orWhere('description', 'like', "%{$this->query}%")
                        ->orWhere('ai_summary', 'like', "%{$this->query}%")
                        ->orWhere('ai_category', 'like', "%{$this->query}%");
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->where('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('created_at', '<=', $this->dateTo . ' 23:59:59'))
            ->when($this->domain, fn ($q) => $q->where('url', 'like', "%{$this->domain}%"))
            ->when($this->category, fn ($q) => $q->where('ai_category', $this->category))
            ->when($this->readStatus === 'read', fn ($q) => $q->where('is_read', true))
            ->when($this->readStatus === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($this->tagFilter, function ($q) {
                $q->whereHas('tags', fn ($tq) => $tq->where('name', 'like', "%{$this->tagFilter}%"));
            })
            ->with('tags')
            ->latest()
            ->limit(50)
            ->get();

        $notesQuery = Auth::user()->notes()
            ->where('is_trashed', false)
            ->when($this->query, function ($q) {
                $q->where(function ($sub) {
                    $sub->where('title', 'like', "%{$this->query}%")
                        ->orWhere('content_plain', 'like', "%{$this->query}%");
                });
            })
            ->when($this->dateFrom, fn ($q) => $q->where('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('created_at', '<=', $this->dateTo . ' 23:59:59'))
            ->with('tags')
            ->latest()
            ->limit(20)
            ->get();

        $this->results = [
            'bookmarks' => $bookmarksQuery,
            'notes' => $notesQuery,
        ];

        // Generate related content suggestions
        if ($bookmarksQuery->isNotEmpty()) {
            $categories = $bookmarksQuery->pluck('ai_category')->filter()->unique()->take(3)->values()->toArray();
            $this->relatedSuggestions = $categories;
        }

        $this->loading = false;
    }

    public function toggleAdvancedFilters(): void
    {
        $this->showAdvancedFilters = !$this->showAdvancedFilters;
    }

    public function clearFilters(): void
    {
        $this->reset(['dateFrom', 'dateTo', 'domain', 'tagFilter', 'readStatus', 'category']);
        if ($this->query) {
            $this->search();
        }
    }

    public function askAI(): void
    {
        if (empty($this->query)) {
            return;
        }

        $ai = app(AIService::class);
        if (!$ai->isConfigured()) {
            $this->aiAnswer = 'AI features require an OpenAI API key. Add it in your .env file.';
            return;
        }

        $context = [];

        if (!empty($this->results['bookmarks'])) {
            foreach (collect($this->results['bookmarks'])->take(5) as $bookmark) {
                $context[] = [
                    'title' => $bookmark->title ?? $bookmark->url,
                    'content' => $bookmark->ai_summary ?? $bookmark->description ?? $bookmark->excerpt ?? '',
                ];
            }
        }

        if (!empty($this->results['notes'])) {
            foreach (collect($this->results['notes'])->take(5) as $note) {
                $context[] = [
                    'title' => $note->title ?? 'Untitled Note',
                    'content' => $note->content_plain ?? strip_tags($note->content ?? ''),
                ];
            }
        }

        $this->aiAnswer = $ai->askKnowledgeBase($this->query, $context);
    }

    public function render()
    {
        $categories = Auth::user()->bookmarks()
            ->where('is_trashed', false)
            ->whereNotNull('ai_category')
            ->distinct()
            ->pluck('ai_category')
            ->sort()
            ->values();

        return view('livewire.search.search-page', [
            'categories' => $categories,
        ])->layout('layouts.app', ['title' => 'Search']);
    }
}
