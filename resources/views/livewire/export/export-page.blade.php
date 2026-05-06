<div>
    {{-- Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Export Data') }}</h1>
        <p class="text-sm text-gray-500 mt-1">{{ __('Export your bookmarks and notes to various formats') }}</p>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
        @foreach([
            ['label' => __('Bookmarks'), 'value' => $stats['bookmarks'], 'color' => 'primary'],
            ['label' => __('Notes'), 'value' => $stats['notes'], 'color' => 'green'],
            ['label' => __('Tags'), 'value' => $stats['tags'], 'color' => 'purple'],
            ['label' => __('Collections'), 'value' => $stats['collections'], 'color' => 'amber'],
        ] as $stat)
            <div class="bg-white dark:bg-surface-900 rounded-xl border border-gray-200 dark:border-gray-800 p-4">
                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $stat['value'] }}</p>
                <p class="text-xs text-gray-500 mt-1">{{ $stat['label'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Export Options --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Markdown Export --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gray-100 dark:bg-surface-800 flex items-center justify-center">
                    <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Markdown') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Standard markdown format') }}</p>
                </div>
            </div>
            <div class="space-y-2">
                <button wire:click="exportBookmarksMarkdown" class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-surface-800 rounded-xl hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors text-left">
                    {{ __('Export Bookmarks (.md)') }}
                </button>
                <button wire:click="exportNotesMarkdown" class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-surface-800 rounded-xl hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors text-left">
                    {{ __('Export Notes (.md)') }}
                </button>
            </div>
        </div>

        {{-- JSON Export --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('JSON') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Structured data format') }}</p>
                </div>
            </div>
            <button wire:click="exportBookmarksJson" class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-surface-800 rounded-xl hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors text-left">
                {{ __('Export Bookmarks (.json)') }}
            </button>
        </div>

        {{-- Notion Export --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-gray-900 dark:bg-white flex items-center justify-center">
                    <span class="text-white dark:text-gray-900 text-sm font-bold">N</span>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Notion') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Import-ready CSV for Notion') }}</p>
                </div>
            </div>
            <button wire:click="exportNotionCsv" class="w-full px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-surface-800 rounded-xl hover:bg-gray-200 dark:hover:bg-surface-700 transition-colors text-left">
                {{ __('Export for Notion (.csv)') }}
            </button>
        </div>

        {{-- Obsidian Export --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" /></svg>
                </div>
                <div>
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Obsidian') }}</h3>
                    <p class="text-xs text-gray-500">{{ __('Individual .md files with YAML frontmatter') }}</p>
                </div>
            </div>
            <p class="text-xs text-gray-500 bg-gray-50 dark:bg-surface-800 rounded-xl p-3">
                {{ __('Use the Markdown export and import into Obsidian. Each bookmark includes YAML frontmatter with tags, URL, and category.') }}
            </p>
        </div>
    </div>
</div>
