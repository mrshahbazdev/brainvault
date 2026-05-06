<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Highlights') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('All your web highlights in one place') }}</p>
        </div>
    </div>

    {{-- Search & Color Filter --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search highlights...') }}"
                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <div class="flex flex-wrap items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1">
            <button wire:click="$set('colorFilter', '')"
                    class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $colorFilter === '' ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                {{ __('All') }}
            </button>
            @foreach(['#FBBF24' => __('Yellow'), '#22C55E' => __('Green'), '#3B82F6' => __('Blue'), '#EC4899' => __('Pink'), '#8B5CF6' => __('Purple')] as $hex => $name)
                <button wire:click="$set('colorFilter', '{{ $hex }}')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors flex items-center gap-1.5 {{ $colorFilter === $hex ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $hex }};"></span>
                    {{ $name }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Highlights List --}}
    <div class="space-y-4">
        @forelse($highlights as $highlight)
            <div class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-lg transition-all duration-200"
                 style="border-left: 4px solid {{ $highlight->color ?? '#FBBF24' }};"
                 wire:key="highlight-{{ $highlight->id }}">
                <div class="p-5">
                    {{-- Highlighted Text --}}
                    <div class="flex items-start justify-between gap-4">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white leading-relaxed">
                                <span class="px-1 rounded" style="background-color: {{ $highlight->color ?? '#FBBF24' }}33;">&ldquo;{{ $highlight->text }}&rdquo;</span>
                            </p>
                        </div>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                            <button wire:click="deleteHighlight({{ $highlight->id }})" wire:confirm="{{ __('Delete this highlight?') }}"
                                    class="p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>

                    {{-- Note --}}
                    @if($highlight->note)
                        <p class="mt-2 text-xs text-gray-500 dark:text-gray-400 italic">{{ $highlight->note }}</p>
                    @endif

                    {{-- Meta Row --}}
                    <div class="mt-3 flex flex-wrap items-center justify-between gap-2">
                        <div class="flex items-center gap-3 min-w-0">
                            {{-- Source Page --}}
                            <a href="{{ $highlight->page_url }}" target="_blank" rel="noopener"
                               class="flex items-center gap-1.5 text-xs text-gray-400 hover:text-primary-600 transition-colors truncate max-w-xs">
                                <img src="https://www.google.com/s2/favicons?domain={{ parse_url($highlight->page_url, PHP_URL_HOST) }}&sz=32"
                                     alt="" class="w-3.5 h-3.5 rounded-sm" loading="lazy">
                                <span class="truncate">{{ parse_url($highlight->page_url, PHP_URL_HOST) }}</span>
                            </a>

                            {{-- Linked Bookmark --}}
                            @if($highlight->bookmark)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-primary-100 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 rounded">
                                    {{ __('Linked') }}
                                </span>
                            @endif
                        </div>
                        <span class="text-[10px] text-gray-400 shrink-0">{{ $highlight->created_at->diffForHumans() }}</span>
                    </div>

                    {{-- Tags --}}
                    @if($highlight->tags->count() > 0)
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($highlight->tags->take(5) as $tag)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-500 rounded">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                <div class="text-center py-12">
                    <div class="w-16 h-16 bg-yellow-100 dark:bg-yellow-900/20 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                    </div>
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('No highlights yet') }}</h2>
                    <p class="text-sm text-gray-500 max-w-md mx-auto mb-6">{{ __('Use the BrainVault Chrome extension to highlight text on any webpage. Your highlights will appear here automatically.') }}</p>
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($highlights->hasPages())
        <div class="mt-6">
            {{ $highlights->links() }}
        </div>
    @endif
</div>
