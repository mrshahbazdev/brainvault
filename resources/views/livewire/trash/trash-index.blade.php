<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Trash') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Items are permanently deleted after 30 days') }}</p>
        </div>
        <button wire:click="emptyTrash" wire:confirm="{{ __('Permanently delete all trashed items? This cannot be undone.') }}"
                class="flex items-center gap-2 px-4 py-2 text-sm font-medium text-red-600 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
            {{ __('Empty Trash') }}
        </button>
    </div>

    {{-- Tabs --}}
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1 w-fit mb-6">
        <button wire:click="$set('type', 'bookmarks')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $type === 'bookmarks' ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500' }}">
            {{ __('Bookmarks') }} ({{ $trashedBookmarks->total() }})
        </button>
        <button wire:click="$set('type', 'notes')" class="px-4 py-2 text-sm font-medium rounded-lg transition-colors {{ $type === 'notes' ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500' }}">
            {{ __('Notes') }} ({{ $trashedNotes->total() }})
        </button>
    </div>

    @if($type === 'bookmarks')
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($trashedBookmarks as $bookmark)
                <div class="flex items-center gap-4 px-4 py-3 hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors group" wire:key="trash-b-{{ $bookmark->id }}">
                    @if($bookmark->favicon_url)
                        <img src="{{ $bookmark->favicon_url }}" alt="" class="w-5 h-5 rounded flex-shrink-0" onerror="this.style.display='none'">
                    @endif
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $bookmark->title ?? __('Untitled') }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($bookmark->url, 60) }}</p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ __('Deleted') }} {{ $bookmark->trashed_at?->diffForHumans() ?? $bookmark->updated_at->diffForHumans() }}</span>
                    <button wire:click="restoreBookmark({{ $bookmark->id }})" class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 transition-colors">
                        {{ __('Restore') }}
                    </button>
                    <button wire:click="permanentDeleteBookmark({{ $bookmark->id }})" wire:confirm="{{ __('Permanently delete?') }}" class="p-1 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @empty
                <div class="py-16 text-center">
                    <p class="text-sm text-gray-500">{{ __('No bookmarks in trash.') }}</p>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $trashedBookmarks->links() }}</div>
    @else
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden divide-y divide-gray-100 dark:divide-gray-800">
            @forelse($trashedNotes as $note)
                <div class="flex items-center gap-4 px-4 py-3 hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors group" wire:key="trash-n-{{ $note->id }}">
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $note->title ?? __('Untitled Note') }}</p>
                        <p class="text-xs text-gray-500 truncate">{{ Str::limit($note->content_plain ?? '', 80) }}</p>
                    </div>
                    <span class="text-xs text-gray-400 whitespace-nowrap">{{ __('Deleted') }} {{ $note->trashed_at?->diffForHumans() ?? $note->updated_at->diffForHumans() }}</span>
                    <button wire:click="restoreNote({{ $note->id }})" class="px-3 py-1.5 text-xs font-medium text-green-600 bg-green-50 dark:bg-green-900/20 rounded-lg hover:bg-green-100 transition-colors">
                        {{ __('Restore') }}
                    </button>
                    <button wire:click="permanentDeleteNote({{ $note->id }})" wire:confirm="{{ __('Permanently delete?') }}" class="p-1 text-red-400 hover:text-red-600 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
            @empty
                <div class="py-16 text-center">
                    <p class="text-sm text-gray-500">{{ __('No notes in trash.') }}</p>
                </div>
            @endforelse
        </div>
        <div class="mt-6">{{ $trashedNotes->links() }}</div>
    @endif
</div>
