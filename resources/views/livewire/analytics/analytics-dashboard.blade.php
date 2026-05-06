<div>
    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Analytics') }}</h1>
        <select wire:model.live="period" class="px-3 py-2 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-700 dark:text-gray-300 focus:ring-2 focus:ring-primary-500">
            <option value="7">{{ __('Last 7 days') }}</option>
            <option value="30">{{ __('Last 30 days') }}</option>
            <option value="90">{{ __('Last 90 days') }}</option>
            <option value="365">{{ __('Last year') }}</option>
        </select>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        @foreach([
            [__('Bookmarks'), $stats['total_bookmarks'], 'bg-blue-500', 'M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z'],
            [__('Notes'), $stats['total_notes'], 'bg-green-500', 'M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z'],
            [__('Highlights'), $stats['total_highlights'], 'bg-yellow-500', 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
            [__('Read'), $stats['read_bookmarks'], 'bg-emerald-500', 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            [__('Favorites'), $stats['favorites'], 'bg-amber-500', 'M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z'],
        ] as [$label, $value, $color, $icon])
            <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 {{ $color }} bg-opacity-10 rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 {{ str_replace('bg-', 'text-', $color) }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}" /></svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($value) }}</p>
                        <p class="text-xs text-gray-500">{{ $label }}</p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Streak Card --}}
        <div class="bg-gradient-to-br from-primary-600 to-purple-600 rounded-2xl p-6 text-white">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold">{{ __('Current Streak') }}</h2>
                <svg class="w-6 h-6 text-white/60" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z" /></svg>
            </div>
            <p class="text-5xl font-bold mb-1">{{ $streak }}</p>
            <p class="text-sm text-white/70">{{ $streak === 1 ? __('day') : __('days') }} {{ __('in a row') }}</p>
        </div>

        {{-- Reading Stats --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Reading Stats') }}</h2>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($readingStats['total_words']) }}</p>
                    <p class="text-xs text-gray-500">{{ __('Total words saved') }}</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($readingStats['total_reading_time']) }}m</p>
                    <p class="text-xs text-gray-500">{{ __('Reading time') }}</p>
                </div>
                <div>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ round($readingStats['avg_reading_time'], 1) }}m</p>
                    <p class="text-xs text-gray-500">{{ __('Avg per article') }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Activity Chart --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Bookmarks Over Time') }}</h2>
            <div class="h-48" x-data="{
                chart: null,
                init() {
                    const data = @js($bookmarksByDay);
                    const labels = [];
                    const values = [];
                    for (let i = {{ (int)$period }}; i >= 0; i--) {
                        const d = new Date();
                        d.setDate(d.getDate() - i);
                        const key = d.toISOString().split('T')[0];
                        labels.push(key.slice(5));
                        values.push(data[key] || 0);
                    }
                    this.chart = new Chart(this.$refs.activityChart, {
                        type: 'bar',
                        data: {
                            labels,
                            datasets: [{
                                label: 'Bookmarks',
                                data: values,
                                backgroundColor: 'rgba(99, 102, 241, 0.7)',
                                borderRadius: 4,
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: { legend: { display: false } },
                            scales: {
                                x: { display: false },
                                y: { beginAtZero: true, ticks: { stepSize: 1, color: '#9CA3AF' }, grid: { color: 'rgba(156,163,175,0.1)' } }
                            }
                        }
                    });
                },
                destroy() { this.chart?.destroy(); }
            }">
                <canvas x-ref="activityChart" class="w-full h-full"></canvas>
            </div>
        </div>

        {{-- Top Domains --}}
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Top Domains') }}</h2>
            <div class="space-y-3">
                @forelse($topDomains as $domain)
                    @php $percentage = $stats['total_bookmarks'] > 0 ? ($domain->count / $stats['total_bookmarks']) * 100 : 0; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $domain->site_name }}</span>
                            <span class="text-xs text-gray-500">{{ $domain->count }}</span>
                        </div>
                        <div class="h-1.5 bg-gray-100 dark:bg-surface-800 rounded-full overflow-hidden">
                            <div class="h-full bg-primary-500 rounded-full" style="width: {{ $percentage }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-gray-500 text-center py-4">{{ __('No data yet') }}</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- AI Categories --}}
    @if($aiCategories->count() > 0)
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-8">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('AI Categories') }}</h2>
            <div class="flex flex-wrap gap-2">
                @foreach($aiCategories as $cat)
                    <span class="px-3 py-1.5 text-sm font-medium bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 rounded-xl">
                        {{ $cat->ai_category }} <span class="text-indigo-400 dark:text-indigo-600">({{ $cat->count }})</span>
                    </span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Content Type Distribution --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Content Types') }}</h2>
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @forelse($contentTypes as $type => $count)
                <div class="p-3 bg-gray-50 dark:bg-surface-800 rounded-xl text-center">
                    <p class="text-lg font-bold text-gray-900 dark:text-white">{{ $count }}</p>
                    <p class="text-xs text-gray-500 capitalize">{{ $type }}</p>
                </div>
            @empty
                <p class="text-sm text-gray-500 col-span-4 text-center py-4">{{ __('No data yet') }}</p>
            @endforelse
        </div>
    </div>
</div>
