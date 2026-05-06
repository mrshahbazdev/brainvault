<div>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Search') }}</h1>

        {{-- Search Input --}}
        <div class="relative mb-4">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input wire:model.live.debounce.300ms="query" type="text" placeholder="{{ __('Search bookmarks, notes, highlights...') }}"
                   class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-2xl text-base text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm">
        </div>

        {{-- Advanced Filters Toggle --}}
        <div class="mb-6">
            <button wire:click="toggleAdvancedFilters" class="flex items-center gap-2 text-sm text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition-colors">
                <svg class="w-4 h-4 transition-transform {{ $showAdvancedFilters ? 'rotate-90' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" /></svg>
                {{ __('Advanced Filters') }}
            </button>

            @if($showAdvancedFilters)
                <div class="mt-3 p-4 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-2xl">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Date From') }}</label>
                            <input wire:model.live="dateFrom" type="date" class="w-full px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Date To') }}</label>
                            <input wire:model.live="dateTo" type="date" class="w-full px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Domain') }}</label>
                            <input wire:model.live.debounce.300ms="domain" type="text" placeholder="github.com" class="w-full px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Tag') }}</label>
                            <input wire:model.live.debounce.300ms="tagFilter" type="text" placeholder="{{ __('Filter by tag') }}" class="w-full px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Read Status') }}</label>
                            <select wire:model.live="readStatus" class="w-full px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="">{{ __('All') }}</option>
                                <option value="read">{{ __('Read') }}</option>
                                <option value="unread">{{ __('Unread') }}</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">{{ __('Category') }}</label>
                            <select wire:model.live="category" class="w-full px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                                <option value="">{{ __('All Categories') }}</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}">{{ $cat }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 mt-4">
                        <button wire:click="search" class="px-4 py-2 text-sm font-medium bg-primary-600 text-white rounded-xl hover:bg-primary-700 transition-colors">{{ __('Apply Filters') }}</button>
                        <button wire:click="clearFilters" class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">{{ __('Clear') }}</button>
                    </div>
                </div>
            @endif
        </div>

        {{-- Type Filters --}}
        <div class="flex items-center gap-2 mb-6">
            @foreach(['all' => __('All'), 'bookmarks' => __('Bookmarks'), 'notes' => __('Notes')] as $value => $label)
                <button wire:click="$set('type', '{{ $value }}')"
                        class="px-4 py-2 text-sm font-medium rounded-xl transition-all {{ $type === $value ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700' }}">
                    {{ $label }}
                </button>
            @endforeach

            @if(!empty($query))
                <button wire:click="askAI" class="ml-auto flex items-center gap-2 px-4 py-2 text-sm font-medium bg-purple-600 text-white rounded-xl hover:bg-purple-700 shadow-lg shadow-purple-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    {{ __('Ask AI') }}
                </button>
            @endif
        </div>

        {{-- AI Answer --}}
        @if($aiAnswer)
            <div class="mb-6 p-5 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-200 dark:border-purple-800 rounded-2xl">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <h3 class="text-sm font-semibold text-purple-900 dark:text-purple-300">{{ __('AI Answer') }}</h3>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $aiAnswer }}</p>
            </div>
        @endif

        {{-- Related Content Suggestions --}}
        @if(!empty($relatedSuggestions))
            <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800 rounded-2xl">
                <h3 class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-2">{{ __('Related Categories — You might also like:') }}</h3>
                <div class="flex flex-wrap gap-2">
                    @foreach($relatedSuggestions as $suggestion)
                        <button wire:click="$set('category', '{{ $suggestion }}')" wire:click.self="search"
                                class="px-3 py-1 text-xs font-medium bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 transition-colors">
                            {{ $suggestion }}
                        </button>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Results --}}
        @if(!empty($query) && strlen($query) >= 2 || $showAdvancedFilters)
            {{-- Bookmarks --}}
            @if(($type === 'all' || $type === 'bookmarks') && !empty($results['bookmarks']) && count($results['bookmarks']) > 0)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('Bookmarks') }} ({{ count($results['bookmarks']) }})</h2>
                    <div class="space-y-2">
                        @foreach($results['bookmarks'] as $bookmark)
                            <a href="{{ $bookmark->url }}" target="_blank" class="block p-4 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-xl hover:border-primary-300 dark:hover:border-primary-700 transition-colors group">
                                <div class="flex items-start gap-3">
                                    @if($bookmark->favicon_url)
                                        <img src="{{ $bookmark->favicon_url }}" alt="" class="w-5 h-5 rounded mt-0.5">
                                    @endif
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-sm font-medium text-gray-900 dark:text-white truncate group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $bookmark->title ?? $bookmark->url }}</h3>
                                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ $bookmark->site_name }} &middot; {{ $bookmark->url }}</p>
                                        @if($bookmark->ai_summary)
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $bookmark->ai_summary }}</p>
                                        @elseif($bookmark->description)
                                            <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-2">{{ $bookmark->description }}</p>
                                        @endif
                                        @if($bookmark->tags->count() > 0)
                                            <div class="flex flex-wrap gap-1 mt-2">
                                                @foreach($bookmark->tags->take(5) as $tag)
                                                    <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-md">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ $bookmark->created_at->diffForHumans() }}</span>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if(($type === 'all' || $type === 'notes') && !empty($results['notes']) && count($results['notes']) > 0)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">{{ __('Notes') }} ({{ count($results['notes']) }})</h2>
                    <div class="space-y-2">
                        @foreach($results['notes'] as $note)
                            <div class="p-4 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-xl">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $note->title ?? 'Untitled Note' }}</h3>
                                <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ Str::limit($note->content_plain ?? '', 200) }}</p>
                                @if($note->tags->count() > 0)
                                    <div class="flex flex-wrap gap-1 mt-2">
                                        @foreach($note->tags->take(3) as $tag)
                                            <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-md">{{ $tag->name }}</span>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(empty($results) || (empty($results['bookmarks']) && empty($results['notes'])))
                <div class="py-16 text-center">
                    <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('No results found') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Try adjusting your search or filters.') }}</p>
                </div>
            @endif
        @endif
    </div>
</div>
