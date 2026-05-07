<?php

namespace App\Livewire\Bookmarks;

use App\Jobs\CapturePageSnapshot;
use App\Jobs\CheckBrokenLinks;
use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\Tag;
use App\Services\MetadataScraperService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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
    public ?string $duplicateWarning = null;

    public array $selected = [];
    public bool $selectAll = false;

    // Bulk action modals
    public bool $showBulkTagModal = false;
    public bool $showBulkMoveModal = false;
    public string $bulkTagName = '';
    public ?int $bulkMoveCollectionId = null;

    public function mount(): void
    {
        if (request()->boolean('create')) {
            $this->showCreateModal = true;
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedFilter(): void
    {
        $this->resetPage();
    }

    public function updatedNewUrl(): void
    {
        $this->duplicateWarning = null;
        if (!empty($this->newUrl) && filter_var($this->newUrl, FILTER_VALIDATE_URL)) {
            $existing = Auth::user()->bookmarks()
                ->where('is_trashed', false)
                ->where('url', $this->newUrl)
                ->first();
            if ($existing) {
                $this->duplicateWarning = "This URL is already saved: \"{$existing->title}\" (saved " . $existing->created_at->diffForHumans() . ")";
            }
        }
    }

    public function setView(string $view): void
    {
        $this->view = $view;
    }

    public function openCreateModal(): void
    {
        $this->reset(['newUrl', 'newTitle', 'newCollectionId', 'duplicateWarning']);
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
        $this->reset(['newUrl', 'newTitle', 'newCollectionId', 'duplicateWarning']);
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

    public function toggleReadLater(int $id): void
    {
        $bookmark = Auth::user()->bookmarks()->findOrFail($id);
        $bookmark->update(['is_read_later' => !$bookmark->is_read_later]);
    }

    public function trashBookmark(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->update([
            'is_trashed' => true,
            'trashed_at' => now(),
        ]);
    }

    public function restoreBookmark(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->update([
            'is_trashed' => false,
            'trashed_at' => null,
        ]);
    }

    public function permanentDelete(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->delete();
    }

    public function emptyTrash(): void
    {
        Auth::user()->bookmarks()->where('is_trashed', true)->delete();
    }

    public function deleteBookmark(int $id): void
    {
        $this->trashBookmark($id);
    }

    public function captureSnapshot(int $id): void
    {
        $bookmark = Auth::user()->bookmarks()->findOrFail($id);
        CapturePageSnapshot::dispatch($bookmark->id);
        $this->dispatch('notify', message: 'Snapshot capture started...');
    }

    public function checkLinks(): void
    {
        CheckBrokenLinks::dispatch(Auth::id());
        $this->dispatch('notify', message: 'Link check started. Results will appear shortly.');
    }

    public function generateShareLink(int $id): void
    {
        $bookmark = Auth::user()->bookmarks()->findOrFail($id);
        if (!$bookmark->share_token) {
            $bookmark->update(['share_token' => Str::random(64)]);
            $bookmark->refresh();
        }
        $shareUrl = url('/share/bookmark/' . $bookmark->share_token);
        $this->dispatch('copy-to-clipboard', url: $shareUrl);
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
        Auth::user()->bookmarks()->whereIn('id', $this->selected)->update([
            'is_trashed' => true,
            'trashed_at' => now(),
        ]);
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

    public function bulkReadLater(): void
    {
        Auth::user()->bookmarks()->whereIn('id', $this->selected)->update(['is_read_later' => true]);
        $this->selected = [];
        $this->selectAll = false;
    }

    public function openBulkTagModal(): void
    {
        $this->bulkTagName = '';
        $this->showBulkTagModal = true;
    }

    public function applyBulkTag(): void
    {
        if (empty($this->bulkTagName)) {
            return;
        }

        $tag = Auth::user()->tags()->firstOrCreate(
            ['slug' => Str::slug($this->bulkTagName)],
            ['name' => $this->bulkTagName]
        );

        $bookmarks = Auth::user()->bookmarks()->whereIn('id', $this->selected)->get();
        foreach ($bookmarks as $bookmark) {
            $bookmark->tags()->syncWithoutDetaching([$tag->id]);
        }

        $this->showBulkTagModal = false;
        $this->selected = [];
        $this->selectAll = false;
    }

    public function moveBookmarkToCollection(int $bookmarkId, int $collectionId): void
    {
        $bookmark = Auth::user()->bookmarks()->findOrFail($bookmarkId);
        $bookmark->collections()->syncWithoutDetaching([$collectionId]);
        $this->dispatch('notify', message: 'Bookmark moved to collection.');
    }

    public function openBulkMoveModal(): void
    {
        $this->bulkMoveCollectionId = null;
        $this->showBulkMoveModal = true;
    }

    public function applyBulkMove(): void
    {
        if (!$this->bulkMoveCollectionId) {
            return;
        }

        $bookmarks = Auth::user()->bookmarks()->whereIn('id', $this->selected)->get();
        foreach ($bookmarks as $bookmark) {
            $bookmark->collections()->syncWithoutDetaching([$this->bulkMoveCollectionId]);
        }

        $this->showBulkMoveModal = false;
        $this->selected = [];
        $this->selectAll = false;
    }

    protected function getBookmarksQuery()
    {
        return Auth::user()->bookmarks()
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('title', 'like', "%{$this->search}%")
                    ->orWhere('url', 'like', "%{$this->search}%")
                    ->orWhere('description', 'like', "%{$this->search}%");
            }))
            ->when($this->filter === 'favorites', fn ($q) => $q->where('is_favorite', true)->where('is_trashed', false))
            ->when($this->filter === 'archived', fn ($q) => $q->where('is_archived', true)->where('is_trashed', false))
            ->when($this->filter === 'unread', fn ($q) => $q->where('is_read', false)->where('is_trashed', false))
            ->when($this->filter === 'read_later', fn ($q) => $q->where('is_read_later', true)->where('is_trashed', false))
            ->when($this->filter === 'broken', fn ($q) => $q->where('link_status', 'dead')->where('is_trashed', false))
            ->when($this->filter === 'trash', fn ($q) => $q->where('is_trashed', true))
            ->when($this->filter === 'all', fn ($q) => $q->where('is_trashed', false)->where('is_archived', false))
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
            'allTags' => Auth::user()->tags()->orderBy('name')->get(),
            'trashCount' => Auth::user()->bookmarks()->where('is_trashed', true)->count(),
            'brokenCount' => Auth::user()->bookmarks()->where('link_status', 'dead')->where('is_trashed', false)->count(),
            'readLaterCount' => Auth::user()->bookmarks()->where('is_read_later', true)->where('is_trashed', false)->count(),
        ])->layout('layouts.app', ['title' => 'Bookmarks']);
    }
}
