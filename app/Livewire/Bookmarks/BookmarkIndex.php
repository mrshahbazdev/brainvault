<?php

namespace App\Livewire\Bookmarks;

use App\Models\Bookmark;
use App\Models\Collection;
use App\Services\MetadataScraperService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class BookmarkIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filter = 'all';

    #[Url]
    public string $sort = 'newest';

    #[Url]
    public string $view = 'grid';

    public ?int $collectionId = null;
    public ?string $contentType = null;

    public bool $showCreateModal = false;
    public string $newUrl = '';
    public string $newTitle = '';
    public ?int $newCollectionId = null;

    public array $selected = [];
    public bool $selectAll = false;

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function openCreateModal(): void
    {
        $this->reset(['newUrl', 'newTitle', 'newCollectionId']);
        $this->showCreateModal = true;
    }

    public function createBookmark(MetadataScraperService $scraper): void
    {
        $this->validate([
            'newUrl' => ['required', 'url', 'max:2000'],
            'newTitle' => ['nullable', 'string', 'max:500'],
        ]);

        $metadata = $scraper->scrape($this->newUrl);

        $bookmark = Auth::user()->bookmarks()->create(array_merge($metadata, [
            'url' => $this->newUrl,
            'title' => $this->newTitle ?: $metadata['title'],
        ]));

        if ($this->newCollectionId) {
            $bookmark->collections()->attach($this->newCollectionId);
        }

        $this->showCreateModal = false;
        $this->reset(['newUrl', 'newTitle', 'newCollectionId']);
        $this->dispatch('bookmark-created');
    }

    public function toggleFavorite(int $id): void
    {
        $bookmark = Auth::user()->bookmarks()->findOrFail($id);
        $bookmark->update(['is_favorite' => !$bookmark->is_favorite]);
    }

    public function toggleArchive(int $id): void
    {
        $bookmark = Auth::user()->bookmarks()->findOrFail($id);
        $bookmark->update(['is_archived' => !$bookmark->is_archived]);
    }

    public function deleteBookmark(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->delete();
    }

    public function toggleSelectAll(): void
    {
        $this->selectAll = !$this->selectAll;
        if ($this->selectAll) {
            $this->selected = $this->getBookmarksQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function bulkDelete(): void
    {
        Auth::user()->bookmarks()->whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function bulkArchive(): void
    {
        Auth::user()->bookmarks()->whereIn('id', $this->selected)->update(['is_archived' => true]);
        $this->selected = [];
        $this->selectAll = false;
    }

    public function bulkFavorite(): void
    {
        Auth::user()->bookmarks()->whereIn('id', $this->selected)->update(['is_favorite' => true]);
        $this->selected = [];
        $this->selectAll = false;
    }

    protected function getBookmarksQuery()
    {
        return Auth::user()->bookmarks()
            ->when($this->search, fn ($q) => $q->where('title', 'like', "%{$this->search}%")->orWhere('url', 'like', "%{$this->search}%")->orWhere('description', 'like', "%{$this->search}%"))
            ->when($this->filter === 'favorites', fn ($q) => $q->where('is_favorite', true))
            ->when($this->filter === 'archived', fn ($q) => $q->where('is_archived', true))
            ->when($this->filter === 'unread', fn ($q) => $q->where('is_read', false))
            ->when($this->filter === 'all', fn ($q) => $q->where('is_archived', false))
            ->when($this->collectionId, fn ($q) => $q->whereHas('collections', fn ($q2) => $q2->where('collections.id', $this->collectionId)))
            ->when($this->contentType, fn ($q) => $q->where('content_type', $this->contentType))
            ->when($this->sort === 'newest', fn ($q) => $q->latest())
            ->when($this->sort === 'oldest', fn ($q) => $q->oldest())
            ->when($this->sort === 'title', fn ($q) => $q->orderBy('title'))
            ->when($this->sort === 'site', fn ($q) => $q->orderBy('site_name'));
    }

    public function render()
    {
        return view('livewire.bookmarks.bookmark-index', [
            'bookmarks' => $this->getBookmarksQuery()->with(['tags', 'collections'])->paginate(24),
            'collections' => Auth::user()->collections()->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Bookmarks']);
    }
}
