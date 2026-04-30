<?php

namespace App\Livewire\Search;

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

    public array $results = [];
    public bool $loading = false;
    public ?string $aiAnswer = null;

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
        if (empty($this->query)) {
            return;
        }

        $this->loading = true;

        $searchService = app(SemanticSearchService::class);
        $this->results = $searchService->search(Auth::id(), $this->query, 20);

        $this->loading = false;
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

        // Gather context from search results
        if (!empty($this->results['bookmarks'])) {
            foreach ($this->results['bookmarks']->take(5) as $bookmark) {
                $context[] = [
                    'title' => $bookmark->title ?? $bookmark->url,
                    'content' => $bookmark->ai_summary ?? $bookmark->description ?? $bookmark->excerpt ?? '',
                ];
            }
        }

        if (!empty($this->results['notes'])) {
            foreach ($this->results['notes']->take(5) as $note) {
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
        return view('livewire.search.search-page')
            ->layout('layouts.app', ['title' => 'Search']);
    }
}
