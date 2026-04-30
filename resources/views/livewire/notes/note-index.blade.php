<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Notes</h1>
            <p class="text-sm text-gray-500 mt-1">Capture your thoughts and ideas</p>
        </div>
        <button wire:click="openCreateModal"
                class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            New Note
        </button>
    </div>

    {{-- Search & Filters --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 mb-6">
        <div class="relative flex-1 w-full">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Search notes..."
                   class="w-full pl-10 pr-4 py-2 bg-white dark:bg-surface-900 border border-gray-200 dark:border-gray-800 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 focus:border-transparent">
        </div>
        <div class="flex items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1">
            @foreach(['all' => 'All', 'pinned' => 'Pinned', 'archived' => 'Archived', 'trash' => 'Trash'] as $key => $label)
                <button wire:click="$set('filter', '{{ $key }}')"
                        class="px-3 py-1.5 text-xs font-medium rounded-lg transition-colors {{ $filter === $key ? 'bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 hover:text-gray-700 dark:hover:text-gray-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- Notes Grid (Masonry-like) --}}
    <div class="columns-1 sm:columns-2 lg:columns-3 xl:columns-4 gap-4 space-y-4">
        @forelse($notes as $note)
            <div class="break-inside-avoid group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-lg transition-all duration-200"
                 @if($note->color) style="border-left: 3px solid {{ $note->color }};" @endif
                 wire:key="note-{{ $note->id }}">
                <div class="p-4">
                    {{-- Title --}}
                    <div class="flex items-start justify-between gap-2 mb-2">
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-1">{{ $note->title ?: 'Untitled' }}</h3>
                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button wire:click="togglePin({{ $note->id }})" class="p-1 rounded hover:bg-gray-100 dark:hover:bg-surface-800">
                                <svg class="w-3.5 h-3.5 {{ $note->is_pinned ? 'text-primary-600' : 'text-gray-400' }}" fill="{{ $note->is_pinned ? 'currentColor' : 'none' }}" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            </button>
                            @if($filter === 'trash')
                                <button wire:click="restoreNote({{ $note->id }})" class="p-1 rounded text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                                </button>
                                <button wire:click="permanentDelete({{ $note->id }})" wire:confirm="Permanently delete this note?" class="p-1 rounded text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            @else
                                <button wire:click="trashNote({{ $note->id }})" class="p-1 rounded text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20">
                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- Content Preview --}}
                    @if($note->content_plain)
                        <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-6 mb-3">{{ Str::limit($note->content_plain, 300) }}</p>
                    @endif

                    {{-- Meta --}}
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            @if($note->note_type !== 'note')
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded capitalize">{{ $note->note_type }}</span>
                            @endif
                            @if($note->bookmark_id)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-primary-100 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 rounded">Linked</span>
                            @endif
                        </div>
                        <span class="text-[10px] text-gray-400">{{ $note->updated_at->diffForHumans() }}</span>
                    </div>

                    {{-- Tags --}}
                    @if($note->tags->count() > 0)
                        <div class="flex flex-wrap gap-1 mt-2">
                            @foreach($note->tags->take(3) as $tag)
                                <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-500 rounded">{{ $tag->name }}</span>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">
                    {{ $filter === 'trash' ? 'Trash is empty' : 'No notes yet' }}
                </h3>
                <p class="mt-1 text-sm text-gray-500">
                    {{ $filter === 'trash' ? 'Deleted notes will appear here.' : 'Start capturing your thoughts and ideas.' }}
                </p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    <div class="mt-6">
        {{ $notes->links() }}
    </div>

    {{-- Create Note Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showCreateModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-lg p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">New Note</h2>
                <form wire:submit="createNote">
                    <div class="space-y-4">
                        <div>
                            <input wire:model="newTitle" type="text" placeholder="Note title..."
                                   class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm font-medium text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <select wire:model="noteType" class="w-full px-4 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-600 dark:text-gray-400 focus:ring-2 focus:ring-primary-500">
                                <option value="note">Note</option>
                                <option value="quick">Quick Note</option>
                                <option value="checklist">Checklist</option>
                            </select>
                        </div>
                        <div>
                            <textarea wire:model="newContent" rows="8" placeholder="Start writing..."
                                      class="w-full px-4 py-3 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('showCreateModal', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">Cancel</button>
                        <button type="submit"
                                class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                            Create Note
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
