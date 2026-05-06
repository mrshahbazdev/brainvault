@props(['class' => ''])
<div x-data="{ langOpen: false }" class="relative {{ $class }}">
    <button @click="langOpen = !langOpen" @click.away="langOpen = false" class="flex items-center gap-1.5 p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors text-sm font-medium">
        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
        <span>{{ app()->getLocale() === 'de' ? 'DE' : 'EN' }}</span>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
    </button>
    <div x-show="langOpen" x-transition class="absolute right-0 mt-1 w-36 bg-white dark:bg-surface-900 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden z-50">
        <a href="{{ route('language.switch', 'en') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm {{ app()->getLocale() === 'en' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-surface-800' }}">
            <span>🇬🇧</span> English
        </a>
        <a href="{{ route('language.switch', 'de') }}" class="flex items-center gap-2 px-4 py-2.5 text-sm {{ app()->getLocale() === 'de' ? 'bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-400 font-medium' : 'text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-surface-800' }}">
            <span>🇩🇪</span> Deutsch
        </a>
    </div>
</div>
