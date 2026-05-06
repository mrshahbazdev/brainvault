<div>
    {{-- Header --}}
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Tag Management') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Rename, merge, and organize your tags') }}</p>
        </div>
    </div>

    {{-- Search & Bulk Actions --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
            <div class="relative flex-1 w-full">
                <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="{{ __('Search tags...') }}"
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
            </div>
            @if(count($selected) > 0)
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500">{{ count($selected) }} {{ __('selected') }}</span>
                    @if(count($selected) >= 2)
                        <button wire:click="openMergeModal" class="px-3 py-1.5 text-xs font-medium bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-200 transition-colors">{{ __('Merge') }}</button>
                    @endif
                    <button wire:click="bulkDelete" wire:confirm="{{ __('Delete selected tags and all their associations?') }}"
                            class="px-3 py-1.5 text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 transition-colors">{{ __('Delete') }}</button>
                </div>
            @endif
        </div>
    </div>

    {{-- Tags Table --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        <table class="w-full">
            <thead>
                <tr class="border-b border-gray-200 dark:border-gray-800">
                    <th class="px-4 py-3 text-left">
                        <input type="checkbox" wire:click="toggleSelectAll" @checked($selectAll) class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Tag') }}</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">{{ __('Color') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('Bookmarks') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('Notes') }}</th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-gray-500 uppercase">{{ __('Highlights') }}</th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                @forelse($tags as $tag)
                    <tr class="hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors" wire:key="tag-{{ $tag->id }}">
                        <td class="px-4 py-3">
                            <input type="checkbox" wire:model.live="selected" value="{{ $tag->id }}" class="w-4 h-4 rounded border-gray-300 text-primary-600 focus:ring-primary-500">
                        </td>
                        <td class="px-4 py-3">
                            <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $tag->name }}</span>
                            <span class="text-xs text-gray-400 ml-2">{{ $tag->slug }}</span>
                        </td>
                        <td class="px-4 py-3">
                            @if($tag->color)
                                <div class="w-5 h-5 rounded-full" style="background-color: {{ $tag->color }};"></div>
                            @else
                                <span class="text-xs text-gray-400">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">{{ $tag->bookmarks_count }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">{{ $tag->notes_count }}</td>
                        <td class="px-4 py-3 text-center text-sm text-gray-600 dark:text-gray-400">{{ $tag->highlights_count }}</td>
                        <td class="px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-1">
                                <button wire:click="openRenameModal({{ $tag->id }})" class="px-2 py-1 text-xs text-gray-600 dark:text-gray-400 hover:text-primary-600 dark:hover:text-primary-400">
                                    {{ __('Rename') }}
                                </button>
                                <button wire:click="deleteTag({{ $tag->id }})" wire:confirm="{{ __('Delete this tag?') }}" class="px-2 py-1 text-xs text-red-500 hover:text-red-700">
                                    {{ __('Delete') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">{{ __('No tags found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Rename Modal --}}
    @if($showRenameModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showRenameModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Rename Tag') }}</h2>
                <input wire:model="newName" type="text" placeholder="{{ __('New tag name') }}"
                       class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 mb-4">
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showRenameModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="renameTag" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl">{{ __('Rename') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Merge Modal --}}
    @if($showMergeModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showMergeModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-2">{{ __('Merge Tags') }}</h2>
                <p class="text-sm text-gray-500 mb-4">{{ __('Select the tag to keep. All other selected tags will be merged into it.') }}</p>
                <select wire:model="mergeTargetId"
                        class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 mb-4">
                    <option value="">{{ __('Select target tag') }}</option>
                    @foreach($tags->whereIn('id', $selected) as $tag)
                        <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                    @endforeach
                </select>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showMergeModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="mergeTags" class="px-4 py-2.5 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl">{{ __('Merge') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
