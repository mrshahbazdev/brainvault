<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Collections') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Organize your bookmarks into folders') }}</p>
        </div>
        <button wire:click="openCreateModal"
                class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ __('New Collection') }}
        </button>
    </div>

    {{-- Collections Grid --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
        @forelse($collections as $collection)
            <div class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5 hover:shadow-lg hover:-translate-y-0.5 transition-all duration-200">
                <div class="flex items-start justify-between mb-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center" style="background-color: {{ $collection->color ?? '#6366F1' }}20;">
                        <svg class="w-5 h-5" style="color: {{ $collection->color ?? '#6366F1' }};" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                    </div>
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="p-1 rounded text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 opacity-0 group-hover:opacity-100 transition-opacity">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" /></svg>
                        </button>
                        <div x-show="open" @click.away="open = false" class="absolute right-0 mt-1 w-36 bg-white dark:bg-surface-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 py-1 z-10">
                            <button wire:click="editCollection({{ $collection->id }})" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">{{ __('Edit') }}</button>
                            <button wire:click="openCreateModal({{ $collection->id }})" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700">{{ __('Add Sub-folder') }}</button>
                            <button wire:click="deleteCollection({{ $collection->id }})" wire:confirm="{{ __('Delete collection?') }}" @click="open = false" class="w-full text-left px-3 py-1.5 text-xs text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20">{{ __('Delete') }}</button>
                        </div>
                    </div>
                </div>
                <a href="{{ route('bookmarks.index', ['collection' => $collection->id]) }}" class="block">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-1 group-hover:text-primary-600 dark:group-hover:text-primary-400">{{ $collection->name }}</h3>
                    @if($collection->description)
                        <p class="text-xs text-gray-500 line-clamp-2 mb-3">{{ $collection->description }}</p>
                    @endif
                    <div class="flex items-center gap-3 text-xs text-gray-400">
                        <span>{{ $collection->bookmarks_count }} {{ __('bookmarks') }}</span>
                        @if($collection->children->count() > 0)
                            <span>{{ $collection->children->count() }} {{ __('sub-folders') }}</span>
                        @endif
                    </div>
                </a>

                {{-- Sub-collections --}}
                @if($collection->children->count() > 0)
                    <div class="mt-3 pt-3 border-t border-gray-100 dark:border-gray-800 space-y-1">
                        @foreach($collection->children->take(3) as $child)
                            <a href="{{ route('bookmarks.index', ['collection' => $child->id]) }}" class="flex items-center gap-2 px-2 py-1 rounded-lg hover:bg-gray-50 dark:hover:bg-surface-800">
                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $child->color ?? $collection->color ?? '#6366F1' }};"></div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 truncate">{{ $child->name }}</span>
                                <span class="text-xs text-gray-400 ml-auto">{{ $child->bookmarks_count }}</span>
                            </a>
                        @endforeach
                        @if($collection->children->count() > 3)
                            <span class="text-xs text-gray-400 pl-6">+{{ $collection->children->count() - 3 }} {{ __('more') }}</span>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <svg class="mx-auto w-16 h-16 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">{{ __('No collections yet') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('Create your first collection to organize bookmarks.') }}</p>
            </div>
        @endforelse
    </div>

    {{-- Pagination --}}
    @if($collections->hasPages())
        <div class="mt-6">
            {{ $collections->links() }}
        </div>
    @endif

    {{-- Create/Edit Modal --}}
    @if($showCreateModal || $showEditModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-md p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ $showEditModal ? __('Edit') : __('New') }} {{ __('Collection') }}</h2>
                <form wire:submit="{{ $showEditModal ? 'updateCollection' : 'createCollection' }}">
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }}</label>
                            <input wire:model="name" type="text" required placeholder="{{ __('My Collection') }}"
                                   class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
                            <textarea wire:model="description" rows="2" placeholder="{{ __('What\'s this collection about?') }}"
                                      class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Color') }}</label>
                            <div class="flex items-center gap-2">
                                @foreach($colors as $c)
                                    <button type="button" wire:click="$set('color', '{{ $c }}')"
                                            class="w-7 h-7 rounded-full transition-transform {{ $color === $c ? 'ring-2 ring-offset-2 ring-gray-400 dark:ring-offset-surface-900 scale-110' : 'hover:scale-110' }}"
                                            style="background-color: {{ $c }};"></button>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 mt-6">
                        <button type="button" wire:click="$set('{{ $showCreateModal ? 'showCreateModal' : 'showEditModal' }}', false)"
                                class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                        <button type="submit"
                                class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                            {{ $showEditModal ? __('Update') : __('Create') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>
