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
                        <div x-data="tiptapEditor($wire.newContent)" x-on:editor-update.window="$wire.newContent = $event.detail.html" class="bg-gray-100 dark:bg-surface-800 rounded-xl overflow-hidden">
                            {{-- Toolbar --}}
                            <div class="flex items-center gap-0.5 px-2 py-1.5 border-b border-gray-200 dark:border-gray-700 flex-wrap">
                                <button type="button" @click="toggleBold()" :class="isActive('bold') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Bold">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6z"/><path d="M6 12h9a4 4 0 014 4 4 4 0 01-4 4H6z"/></svg>
                                </button>
                                <button type="button" @click="toggleItalic()" :class="isActive('italic') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Italic">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 4h4m-2 0l-4 16m0 0h4"/></svg>
                                </button>
                                <button type="button" @click="toggleStrike()" :class="isActive('strike') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Strikethrough">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 12h12M7 6h5a4 4 0 010 8H7"/></svg>
                                </button>
                                <button type="button" @click="toggleHighlight()" :class="isActive('highlight') ? 'bg-yellow-200 dark:bg-yellow-900' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Highlight">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9.53 16.122a3 3 0 00-5.78 1.128 2.25 2.25 0 01-2.4 2.245 4.5 4.5 0 008.4-2.245c0-.399-.078-.78-.22-1.128zm0 0a15.998 15.998 0 003.388-1.62m-5.043-.025a15.994 15.994 0 011.622-3.395m3.42 3.42a15.995 15.995 0 004.764-4.648l3.876-5.814a1.151 1.151 0 00-1.597-1.597L14.146 6.32a15.996 15.996 0 00-4.649 4.763m3.42 3.42a6.776 6.776 0 00-3.42-3.42"/></svg>
                                </button>
                                <span class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>
                                <button type="button" @click="setHeading(2)" :class="isActive('heading', {level: 2}) ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors text-xs font-bold" title="Heading">H2</button>
                                <button type="button" @click="setHeading(3)" :class="isActive('heading', {level: 3}) ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors text-xs font-bold" title="Heading">H3</button>
                                <span class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>
                                <button type="button" @click="toggleBulletList()" :class="isActive('bulletList') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Bullet List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M8 6h13M8 12h13M8 18h13M3 6h.01M3 12h.01M3 18h.01"/></svg>
                                </button>
                                <button type="button" @click="toggleOrderedList()" :class="isActive('orderedList') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Numbered List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 6h11M10 12h11M10 18h11M3 5l2 1V11M3 17h4"/></svg>
                                </button>
                                <button type="button" @click="toggleTaskList()" :class="isActive('taskList') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Task List">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </button>
                                <span class="w-px h-5 bg-gray-300 dark:bg-gray-600 mx-1"></span>
                                <button type="button" @click="toggleBlockquote()" :class="isActive('blockquote') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Quote">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10H14.017zM0 21v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151C7.563 6.068 6 8.789 6 11h4v10H0z"/></svg>
                                </button>
                                <button type="button" @click="toggleCode()" :class="isActive('codeBlock') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Code">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                                </button>
                                <button type="button" @click="setLink()" :class="isActive('link') ? 'bg-gray-300 dark:bg-surface-600' : ''" class="p-1.5 rounded-lg text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors" title="Link">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </button>
                            </div>
                            {{-- Editor Area --}}
                            <div x-ref="editor" class="min-h-[200px]"></div>
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
