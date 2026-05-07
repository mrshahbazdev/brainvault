<div x-data @copy-to-clipboard.window="
    navigator.clipboard.writeText($event.detail.url).then(() => {
        $dispatch('notify', { message: 'Share link copied to clipboard!' });
    }).catch(() => {
        prompt('Copy this share link:', $event.detail.url);
    })
">
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Bookmarks') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ Auth::user()->bookmarks()->where('is_trashed', false)->count() }} {{ __('total bookmarks') }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="checkLinks"
                    class="flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-surface-800 border border-gray-200 dark:border-gray-700 rounded-xl hover:bg-gray-50 dark:hover:bg-surface-700 transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" /></svg>
                {{ __('Check Links') }}
            </button>
            <button wire:click="openCreateModal"
                    class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                {{ __('Add Bookmark') }}
            </button>
        </div>
    </div>

    {{-- Filters & Search Bar --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
        <div class="flex flex-col lg:flex-row items-start lg:items-center gap-4">
            {{-- Search --}}
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search bookmarks...') }}"
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
            </div>

            {{-- Filter Tabs --}}
            <div class="flex flex-wrap items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1">
                @foreach([
                    'all' => __('All'),
                    'favorites' => __('Favorites'),
                    'unread' => __('Unread'),
                    'read_later' => __('Read Later') . ($readLaterCount > 0 ? " ({$readLaterCount})" : ''),
                    'archived' => __('Archived'),
                    'broken' => __('Broken') . ($brokenCount > 0 ? " ({$brokenCount})" : ''),
                    'trash' => __('Trash') . ($trashCount > 0 ? " ({$trashCount})" : ''),
                ] as $key => $label)
                    <button wire:click="$set('filter', '{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === $key ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            {{-- Sort --}}
            <select wire:model.live="sort" class="px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-600 dark:text-gray-400 focus:ring-2 focus:ring-primary-500">
                <option value="newest">{{ __('Newest') }}</option>
                <option value="oldest">{{ __('Oldest') }}</option>
                <option value="title">{{ __('Title A-Z') }}</option>
                <option value="site">{{ __('Site A-Z') }}</option>
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

        {{-- Trash Actions --}}
        @if($filter === 'trash')
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-500">{{ __('Items in trash are permanently deleted after 30 days.') }}</span>
                <button wire:click="emptyTrash" wire:confirm="{{ __('Permanently delete all items in trash?') }}"
                        class="px-3 py-1.5 text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 transition-colors">
                    {{ __('Empty Trash') }}
                </button>
            </div>
        @endif

        {{-- Bulk Actions --}}
        @if(count($selected) > 0)
            <div class="flex items-center gap-3 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($selected) }} {{ __('selected') }}</span>
                <button wire:click="bulkFavorite" class="px-3 py-1.5 text-xs font-medium bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/30 transition-colors">{{ __('Favorite') }}</button>
                <button wire:click="bulkArchive" class="px-3 py-1.5 text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/30 transition-colors">{{ __('Archive') }}</button>
                <button wire:click="bulkReadLater" class="px-3 py-1.5 text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/30 transition-colors">{{ __('Read Later') }}</button>
                <button wire:click="openBulkTagModal" class="px-3 py-1.5 text-xs font-medium bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-900/30 transition-colors">{{ __('Tag') }}</button>
                <button wire:click="openBulkMoveModal" class="px-3 py-1.5 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/30 transition-colors">{{ __('Move to Collection') }}</button>
                <button wire:click="bulkDelete" wire:confirm="{{ __('Move selected bookmarks to trash?') }}" class="px-3 py-1.5 text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/30 transition-colors">{{ __('Trash') }}</button>
            </div>
        @endif
    </div>

    {{-- Grid View --}}
    @if($view === 'grid')
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
            @forelse($bookmarks as $bookmark)
                <div class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200 relative"
                     :class="{ 'z-30': menuOpen }"
                     wire:key="bookmark-{{ $bookmark->id }}"
                     x-data="{ showPreview: false, menuOpen: false }"
                     @mouseenter="showPreview = true" @mouseleave="showPreview = false">
                    {{-- Image --}}
                    <div class="relative aspect-video bg-gray-100 dark:bg-surface-800 rounded-t-2xl overflow-hidden">
                        @if($bookmark->og_image_url)
                            <img src="{{ $bookmark->og_image_url }}" alt="" class="w-full h-full object-cover">
                        @elseif(preg_match('/\.(jpeg|jpg|gif|png|webp|svg)$/i', parse_url($bookmark->url, PHP_URL_PATH) ?? ''))
                            <img src="{{ $bookmark->url }}" alt="" class="w-full h-full object-contain bg-gray-900">
                        @else
                            <div class="w-full h-full flex items-center justify-center">
                                @if($bookmark->favicon_url)
                                    <img src="{{ $bookmark->favicon_url }}" alt="" class="w-16 h-16 rounded shadow-sm" onerror="this.src='https://www.google.com/s2/favicons?domain={{ parse_url($bookmark->url, PHP_URL_HOST) }}&sz=128'">
                                @else
                                    <img src="https://www.google.com/s2/favicons?domain={{ parse_url($bookmark->url, PHP_URL_HOST) }}&sz=128" alt="" class="w-16 h-16 rounded shadow-sm" onerror="this.style.display='none'">
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
                        {{-- Link Status Badge --}}
                        @if($bookmark->link_status === 'dead')
                            <span class="absolute bottom-2 right-2 px-2 py-0.5 text-xs font-medium bg-red-500/80 text-white backdrop-blur-sm rounded-md">{{ __('Broken') }}</span>
                        @endif
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
                            @if($bookmark->is_read_later)
                                <span class="ml-auto px-1.5 py-0.5 text-[10px] font-medium bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-md">{{ __('Read Later') }}</span>
                            @endif
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 mb-1">
                            <a href="{{ $bookmark->url }}" target="_blank" class="hover:text-primary-600 dark:hover:text-primary-400">{{ $bookmark->title ?? __('Untitled') }}</a>
                        </h3>
                        @if($bookmark->description)
                            <p class="text-xs text-gray-500 line-clamp-2 mb-2">{{ $bookmark->description }}</p>
                        @endif
                        <div class="flex items-center justify-between">
                            <span class="text-xs text-gray-400">{{ $bookmark->created_at->diffForHumans() }}</span>
                            <div class="flex items-center gap-1">
                                @if($bookmark->reading_time)
                                    <span class="text-xs text-gray-400">{{ $bookmark->reading_time }} {{ __('min') }}</span>
                                @endif
                                <div class="relative">
                                    <button @click="menuOpen = !menuOpen" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                                    </button>
                                    <div x-show="menuOpen" @click.away="menuOpen = false" class="absolute right-0 mt-1 w-40 bg-white dark:bg-surface-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                                        <button wire:click="toggleReadLater({{ $bookmark->id }})" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">
                                            {{ $bookmark->is_read_later ? __('Remove from Read Later') : __('Read Later') }}
                                        </button>
                                        <button wire:click="toggleArchive({{ $bookmark->id }})" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">
                                            {{ $bookmark->is_archived ? __('Unarchive') : __('Archive') }}
                                        </button>
                                        <button wire:click="captureSnapshot({{ $bookmark->id }})" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">
                                            {{ __('Save Snapshot') }}
                                        </button>
                                        <button wire:click="generateShareLink({{ $bookmark->id }})" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">
                                            {{ __('Share Link') }}
                                        </button>
                                        @if($filter === 'trash')
                                            <button wire:click="restoreBookmark({{ $bookmark->id }})" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-green-600 hover:bg-green-50 dark:hover:bg-green-900/20">
                                                {{ __('Restore') }}
                                            </button>
                                            <button wire:click="permanentDelete({{ $bookmark->id }})" wire:confirm="{{ __('Permanently delete?') }}" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                {{ __('Delete Forever') }}
                                            </button>
                                        @else
                                            <button wire:click="trashBookmark({{ $bookmark->id }})" @click="menuOpen = false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">
                                                {{ __('Move to Trash') }}
                                            </button>
                                        @endif
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

                    {{-- Hover Preview Tooltip --}}
                    <div x-show="showPreview" x-transition
                         class="absolute -top-2 left-full ml-2 w-72 bg-white dark:bg-surface-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 p-4 z-50 hidden lg:block"
                         style="display: none;">
                        @if($bookmark->ai_summary)
                            <p class="text-xs text-gray-600 dark:text-gray-300 mb-2"><strong>{{ __('AI Summary:') }}</strong> {{ Str::limit($bookmark->ai_summary, 200) }}</p>
                        @endif
                        @if($bookmark->ai_keywords && is_array($bookmark->ai_keywords))
                            <div class="flex flex-wrap gap-1">
                                @foreach(array_slice($bookmark->ai_keywords, 0, 5) as $kw)
                                    <span class="px-1.5 py-0.5 text-[10px] bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 rounded">{{ $kw }}</span>
                                @endforeach
                            </div>
                        @endif
                        @if(!$bookmark->ai_summary && !$bookmark->ai_keywords)
                            <p class="text-xs text-gray-400">{{ Str::limit($bookmark->url, 100) }}</p>
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                        @if($filter === 'trash')
                            {{ __('Trash is empty') }}
                        @elseif($filter === 'broken')
                            {{ __('No broken links found') }}
                        @elseif($filter === 'read_later')
                            {{ __('No items in reading list') }}
                        @else
                            {{ __('No bookmarks found') }}
                        @endif
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Start saving bookmarks with the button above or the Chrome extension.') }}</p>
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
                        <a href="{{ $bookmark->url }}" target="_blank" class="text-sm font-medium text-gray-900 dark:text-white truncate block hover:text-primary-600 dark:hover:text-primary-400">{{ $bookmark->title ?? __('Untitled') }}</a>
                        <span class="text-xs text-gray-500 truncate block">{{ Str::limit($bookmark->url, 60) }}</span>
                    </div>
                    @if($bookmark->link_status === 'dead')
                        <span class="px-2 py-0.5 text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-600 rounded-md">{{ __('Broken') }}</span>
                    @endif
                    @if($bookmark->is_read_later)
                        <span class="px-2 py-0.5 text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-600 rounded-md">{{ __('Read Later') }}</span>
                    @endif
                    <span class="text-xs text-gray-400 whitespace-nowrap hidden sm:block">{{ $bookmark->created_at->diffForHumans() }}</span>
                    <button wire:click="toggleFavorite({{ $bookmark->id }})" class="p-1 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4 {{ $bookmark->is_favorite ? 'text-amber-500 fill-current' : 'text-gray-400' }}" fill="{{ $bookmark->is_favorite ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z" /></svg>
                    </button>
                    @if($filter === 'trash')
                        <button wire:click="restoreBookmark({{ $bookmark->id }})" class="p-1 text-green-500 opacity-0 group-hover:opacity-100 transition-opacity" title="{{ __('Restore') }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                        </button>
                    @endif
                    <button wire:click="trashBookmark({{ $bookmark->id }})" wire:confirm="{{ __('Move to trash?') }}" class="p-1 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                    </button>
                </div>
            @empty
                <div class="py-16 text-center">
                    <p class="text-sm text-gray-500">{{ __('No bookmarks found.') }}</p>
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
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Add Bookmark') }}</h2>
                <form wire:submit="createBookmark">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('URL') }}</label>
                            <input wire:model.live.debounce.500ms="newUrl" type="url" required placeholder="https://example.com"
                                   class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                            @error('newUrl') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @if($duplicateWarning)
                                <div class="mt-2 p-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-lg">
                                    <p class="text-xs text-amber-700 dark:text-amber-400 flex items-center gap-1">
                                        <svg class="w-4 h-4 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" /></svg>
                                        {{ $duplicateWarning }}
                                    </p>
                                </div>
                            @endif
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Title (optional)') }}</label>
                            <input wire:model="newTitle" type="text" placeholder="{{ __('Auto-detected from page') }}"
                                   class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Collection (optional)') }}</label>
                            <select wire:model="newCollectionId"
                                    class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="">{{ __('No collection') }}</option>
                                @foreach($collections as $collection)
                                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                        <button type="submit"
                                class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all"
                                wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="createBookmark">{{ __('Save Bookmark') }}</span>
                            <span wire:loading wire:target="createBookmark">{{ __('Saving...') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

    {{-- Bulk Tag Modal --}}
    @if($showBulkTagModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showBulkTagModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Add Tag to Selected') }}</h2>
                <input wire:model="bulkTagName" type="text" placeholder="{{ __('Tag name') }}"
                       class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 mb-4">
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showBulkTagModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="applyBulkTag" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl">{{ __('Apply Tag') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Bulk Move Modal --}}
    @if($showBulkMoveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showBulkMoveModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Move to Collection') }}</h2>
                <select wire:model="bulkMoveCollectionId"
                        class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 mb-4">
                    <option value="">{{ __('Select collection') }}</option>
                    @foreach($collections as $collection)
                        <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                    @endforeach
                </select>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showBulkMoveModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="applyBulkMove" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl">{{ __('Move') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
