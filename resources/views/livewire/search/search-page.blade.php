<div>
    <div class="max-w-4xl mx-auto">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Search</h1>

        {{-- Search Input --}}
        <div class="relative mb-6">
            <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input wire:model.live.debounce.300ms="query" type="text" placeholder="Search bookmarks, notes, highlights..."
                   class="w-full pl-12 pr-4 py-3.5 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-2xl text-base text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent shadow-sm">
        </div>

        {{-- Type Filters --}}
        <div class="flex items-center gap-2 mb-6">
            @foreach(['all' => 'All', 'bookmarks' => 'Bookmarks', 'notes' => 'Notes'] as $value => $label)
                <button wire:click="$set('type', '{{ $value }}')"
                        class="px-4 py-2 text-sm font-medium rounded-xl transition-all {{ $type === $value ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700' }}">
                    {{ $label }}
                </button>
            @endforeach

            @if(!empty($query))
                <button wire:click="askAI" class="ml-auto flex items-center gap-2 px-4 py-2 text-sm font-medium bg-purple-600 text-white rounded-xl hover:bg-purple-700 shadow-lg shadow-purple-500/25 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Ask AI
                </button>
            @endif
        </div>

        {{-- AI Answer --}}
        @if($aiAnswer)
            <div class="mb-6 p-5 bg-gradient-to-r from-purple-50 to-indigo-50 dark:from-purple-900/20 dark:to-indigo-900/20 border border-purple-200 dark:border-purple-800 rounded-2xl">
                <div class="flex items-center gap-2 mb-2">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    <h3 class="text-sm font-semibold text-purple-900 dark:text-purple-300">AI Answer</h3>
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-wrap">{{ $aiAnswer }}</p>
            </div>
        @endif

        {{-- Results --}}
        @if(!empty($query) && strlen($query) >= 2)
            {{-- Bookmarks --}}
            @if(($type === 'all' || $type === 'bookmarks') && !empty($results['bookmarks']) && $results['bookmarks']->count() > 0)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Bookmarks ({{ $results['bookmarks']->count() }})</h2>
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
                                            <div class="flex items-center gap-1 mt-2">
                                                @foreach($bookmark->tags->take(3) as $tag)
                                                    <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-full">{{ $tag->name }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    @if($bookmark->ai_category)
                                        <span class="px-2 py-1 text-[10px] font-semibold bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400 rounded-lg whitespace-nowrap">{{ $bookmark->ai_category }}</span>
                                    @endif
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Notes --}}
            @if(($type === 'all' || $type === 'notes') && !empty($results['notes']) && $results['notes']->count() > 0)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">Notes ({{ $results['notes']->count() }})</h2>
                    <div class="space-y-2">
                        @foreach($results['notes'] as $note)
                            <div class="p-4 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-xl {{ $note->color ? 'border-l-4' : '' }}" @if($note->color) style="border-left-color: {{ $note->color }}" @endif>
                                <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $note->title ?? 'Untitled' }}</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-3">{{ Str::limit($note->content_plain ?? strip_tags($note->content ?? ''), 200) }}</p>
                                <div class="flex items-center gap-2 mt-2">
                                    <span class="text-[10px] text-gray-400">{{ $note->updated_at->diffForHumans() }}</span>
                                    @if($note->note_type)
                                        <span class="px-2 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-500 rounded-full">{{ $note->note_type }}</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Semantic Results --}}
            @if(($type === 'all') && !empty($results['semantic']) && count($results['semantic']) > 0)
                <div class="mb-6">
                    <h2 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-3">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                            AI-Powered Results
                        </span>
                    </h2>
                    <div class="space-y-2">
                        @foreach($results['semantic'] as $result)
                            <div class="p-4 bg-purple-50/50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800/50 rounded-xl">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 text-[10px] font-semibold {{ $result['type'] === 'bookmark' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' : 'bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400' }} rounded-full">{{ ucfirst($result['type']) }}</span>
                                    <span class="text-[10px] text-gray-400">{{ round($result['similarity'] * 100) }}% match</span>
                                </div>
                                @if($result['type'] === 'bookmark')
                                    <a href="{{ $result['item']->url }}" target="_blank" class="text-sm font-medium text-gray-900 dark:text-white hover:text-primary-600">{{ $result['item']->title ?? $result['item']->url }}</a>
                                @else
                                    <h3 class="text-sm font-medium text-gray-900 dark:text-white">{{ $result['item']->title ?? 'Untitled' }}</h3>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- No Results --}}
            @if(
                (empty($results['bookmarks']) || $results['bookmarks']->count() === 0) &&
                (empty($results['notes']) || $results['notes']->count() === 0) &&
                (empty($results['semantic']) || count($results['semantic']) === 0)
            )
                <div class="text-center py-12">
                    <svg class="w-12 h-12 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    <p class="text-sm text-gray-500">No results found for "{{ $query }}"</p>
                </div>
            @endif
        @else
            <div class="text-center py-16">
                <svg class="w-16 h-16 text-gray-200 dark:text-gray-700 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Search your knowledge</h2>
                <p class="text-sm text-gray-500">Search across bookmarks, notes, and highlights</p>
            </div>
        @endif
    </div>
</div>
