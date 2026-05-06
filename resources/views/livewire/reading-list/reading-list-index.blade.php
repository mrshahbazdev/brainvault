<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Reading List') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $bookmarks->total() }} {{ __('items to read') }}</p>
        </div>
        <select wire:model.live="sort" class="px-3 py-2 bg-white dark:bg-surface-800 border border-gray-200 dark:border-gray-700 rounded-xl text-sm text-gray-600 dark:text-gray-400 focus:ring-2 focus:ring-primary-500">
            <option value="newest">{{ __('Newest First') }}</option>
            <option value="oldest">{{ __('Oldest First') }}</option>
            <option value="reading_time">{{ __('Shortest First') }}</option>
        </select>
    </div>

    {{-- Reading List Items --}}
    <div class="space-y-3">
        @forelse($bookmarks as $bookmark)
            <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 hover:shadow-md transition-shadow" wire:key="rl-{{ $bookmark->id }}">
                <div class="flex items-start gap-4">
                    @if($bookmark->favicon_url)
                        <img src="{{ $bookmark->favicon_url }}" alt="" class="w-8 h-8 rounded mt-0.5 flex-shrink-0" onerror="this.style.display='none'">
                    @endif
                    <div class="flex-1 min-w-0">
                        <a href="{{ $bookmark->url }}" target="_blank" class="text-sm font-semibold text-gray-900 dark:text-white hover:text-primary-600 dark:hover:text-primary-400 block truncate">
                            {{ $bookmark->title ?? $bookmark->url }}
                        </a>
                        <span class="text-xs text-gray-500 block mt-0.5">{{ $bookmark->site_name ?? parse_url($bookmark->url, PHP_URL_HOST) }}</span>
                        @if($bookmark->description)
                            <p class="text-xs text-gray-500 mt-2 line-clamp-2">{{ $bookmark->description }}</p>
                        @endif
                        @if($bookmark->ai_summary)
                            <p class="text-xs text-primary-600 dark:text-primary-400 mt-2 line-clamp-2">{{ $bookmark->ai_summary }}</p>
                        @endif
                        <div class="flex items-center gap-3 mt-3">
                            @if($bookmark->reading_time)
                                <span class="text-xs text-gray-400 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ $bookmark->reading_time }} {{ __('min read') }}
                                </span>
                            @endif
                            <span class="text-xs text-gray-400">{{ __('Added') }} {{ $bookmark->created_at->diffForHumans() }}</span>
                            @if($bookmark->read_later_reminder_at)
                                <span class="text-xs text-amber-500 flex items-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                                    {{ __('Reminder:') }} {{ $bookmark->read_later_reminder_at->format('M d') }}
                                </span>
                            @endif
                            @if($bookmark->tags->count() > 0)
                                @foreach($bookmark->tags->take(3) as $tag)
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-md">{{ $tag->name }}</span>
                                @endforeach
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-shrink-0">
                        <button wire:click="markAsRead({{ $bookmark->id }})" class="px-3 py-1.5 text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 transition-colors">
                            {{ __('Mark Read') }}
                        </button>
                        <button wire:click="removeFromList({{ $bookmark->id }})" class="p-1.5 text-gray-400 hover:text-red-500 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 py-16 text-center">
                <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('Your reading list is empty') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Add bookmarks to your reading list from the bookmarks page.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">
        {{ $bookmarks->links() }}
    </div>
</div>
