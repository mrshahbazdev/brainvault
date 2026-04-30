<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Bookmarks</h1>
            <p class="text-sm text-gray-500 mt-1">{{ Auth::user()->bookmarks()->count() }} total bookmarks</p>
        </div>
        <button wire:click="openCreateModal"
                class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Add Bookmark
        </button>
    </div>

    {{-- Filters & Search Bar --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
            {{-- Search --}}
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search bookmarks..."
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Filter Tabs --}}
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1">
                @foreach(['all' => 'All', 'favorites' => 'Favorites', 'unread' => 'Unread', 'archived' => 'Archived'] as $key => $label)
                    <button wire:click="$set('filter', '{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === $key ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Sort --}}
            <select wire:model.live="sort" class="px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-600 dark:text-gray-400 focus:ring-2 focus:ring-primary-500">
                <option value="newest">Newest</option>
                <option value="oldest">Oldest</option>
                <option value="title">Title A-Z</option>
                <option value="site">Site A-Z</option>
            </select>

            {{-- View Toggle --}}
            <div class="flex items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1">
                <button wire:click="setView('grid')" class="p-1.5 rounded-lg {{ $view === 'grid' ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" /></svg>
                </button>
                <button wire:click="setView('list')" class="p-1.5 rounded-lg {{ $view === 'list' ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500' }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" /></svg>
                </button>
            </div>
        </div>

        {{-- Bulk Actions --}}
        @if(count($selected) > 0)
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($selected) }} selected</span>
                <button wire:click="bulkFavorite" class="px-3 py-1.5 text-xs font-medium bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/30 transition-colors">Favorite</button>
                <button wire:click="bulkArchive" class="px-3 py-1.5 text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/30 transition-colors">Archive</button>
                <button wire:click="bulkDelete" wire:confirm="Delete selected bookmarks?" class="px-3 py-1.5 text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/30 transition-colors">Delete</button>
            </div>
        @endif
    </div>

    {{-- Grid View --}}
    @if($view === 'grid')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($bookmarks as $bookmark)
                <div class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200" wire:key="bookmark-{{ $bookmark->id }}">
                    {{-- Image --}}
                    <div class="relative aspect-video bg-gray-100 dark:bg-surface-800">
                        @if($bookmark->og_image_url)
                            <img src="{{ $bookmark->og_image_url }}" alt="" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                @if($bookmark->favicon_url)
                                    <img src="{{ $bookmark->favicon_url }}" alt="" class="w-8 h-8 rounded" onerror="this.style.display='none'">
                                @else
                                    <svg class="w-8 h-8 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                @endif
                            </div>
                        @endif
                        {{-- Checkbox --}}
                        <div class="absolute top-2 left-2 opacity-0 group-hover:opacity-100 transition-opacity">
                            <input type="checkbox" wire:model.live="selected" value="{{ $bookmark->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                        </div>
                        {{-- Favorite --}}
                        <button wire:click="toggleFavorite({{ $bookmark->id }})" class="absolute top-2 right-2 p-1.5 rounded-lg bg-white/80 dark:bg-surface-800/80 backdrop-blur-sm opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4 {{ $bookmark->is_favorite ? 'text-amber-500 fill-current' : 'text-gray-400' }}" fill="{{ $bookmark->is_favorite ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                        </button>
                        {{-- Content type badge --}}
                        @if($bookmark->content_type !== 'webpage')
                            <span class="absolute bottom-2 left-2 px-2 py-0.5 text-xs font-medium bg-white/80 dark:bg-surface-800/80 backdrop-blur-sm rounded-md text-gray-600 dark:text-gray-400 capitalize">{{ $bookmark->content_type }}</span>
                        @endif
                    </div>
                    {{-- Content --}}
                    <div class="p-4">
                        <div class="flex items-start gap-2 mb-2">
                            @if($bookmark->favicon_url)
                                <img src="{{ $bookmark->favicon_url }}" alt="" class="w-4 h-4 rounded mt-0.5 flex-shrink-0" onerror="this.style.display='none'">
                            @endif
                            <span class="text-xs text-gray-500 truncate">{{ $bookmark->site_name ?? parse_url($bookmark->url, PHP_URL_HOST) }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 mb-1">
                            <a href="{{ $bookmark->url }}" target="_blank" class="hover:text-primary-600 dark:hover:text-primary-400">{{ $bookmark->title ?? 'Untitled' }}</a>
                        </h3>
                        @if($bookmark->description)
                            <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $bookmark->description }}</p>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ $bookmark->created_at->diffForHumans() }}</span>
                            <div class="flex items-center gap-1">
                                @if($bookmark->reading_time)
                                    <span class="text-xs text-gray-400">{{ $bookmark->reading_time }} min</span>
                                @endif
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-36 bg-white dark:bg-surface-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                                        <button wire:click="toggleArchive({{ $bookmark->id }})" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">
                                            {{ $bookmark->is_archived ? 'Unarchive' : 'Archive' }}
                                        </button>
                                        <button wire:click="deleteBookmark({{ $bookmark->id }})" wire:confirm="Delete this bookmark?" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- Tags --}}
                        @if($bookmark->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($bookmark->tags->take(3) as $tag)
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-md">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">No bookmarks found</h3>
                    <p class="mt-1 text-sm text-gray-500">Start saving bookmarks with the button above or the Chrome extension.</p>
                </div>
            @endforelse
        </div>
    @else
        {{-- List View --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($bookmarks as $bookmark)
                <div class="flex items-center gap-4 px-4 py-3 hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors group" wire:key="bookmark-list-{{ $bookmark->id }}">
                    <input type="checkbox" wire:model.live="selected" value="{{ $bookmark->id }}" class="w-4 h-4 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                    @if($bookmark->favicon_url)
                        <img src="{{ $bookmark->favicon_url }}" alt="" class="w-5 h-5 rounded flex-shrink-0" onerror="this.style.display='none'">
                    @endif
                    <div class="flex-1 min-w-0">
                        <a href="{{ $bookmark->url }}" target="_blank" class="text-sm font-medium text-gray-900 dark:text-white truncate block hover:text-primary-600 dark:hover:text-primary-400">{{ $bookmark->title ?? 'Untitled' }}</a>
                        <span class="text-xs text-gray-500 truncate block">{{ Str::limit($bookmark->url, 60) }}</span>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:block">{{ $bookmark->created_at->diffForHumans() }}</span>
                    <button wire:click="toggleFavorite({{ $bookmark->id }})" class="p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4 {{ $bookmark->is_favorite ? 'text-amber-500 fill-current' : 'text-gray-400' }}" fill="{{ $bookmark->is_favorite ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </button>
                    <button wire:click="deleteBookmark({{ $bookmark->id }})" wire:confirm="Delete?" class="p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            @empty
                <div class="py-16 text-center">
                    <p class="text-sm text-gray-500">No bookmarks found.</p>
                </div>
            @endforelse
        </div>
    @endif

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $bookmarks->links() }}
    </div>

    {{-- Create Bookmark Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4" x-data x-init="$el.querySelector('input[type=url]')?.focus()">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-md p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Add Bookmark</h2>
                <form wire:submit="createBookmark">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">URL</label>
                            <input wire:model="newUrl" type="url" required placeholder="https://example.com"
                                   class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                            @error('newUrl') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Title (optional)</label>
                            <input wire:model="newTitle" type="text" placeholder="Auto-detected from page"
                                   class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Collection (optional)</label>
                            <select wire:model="newCollectionId"
                                    class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="">No collection</option>
                                @foreach($collections as $collection)
                                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">Cancel</button>
                        <button type="submit"
                                class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createBookmark">Save Bookmark</span>
                            <span wire:loading wire:target="createBookmark">Saving...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
