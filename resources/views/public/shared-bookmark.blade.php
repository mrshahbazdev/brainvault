<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $bookmark->title ?? 'Shared Bookmark' }} - BrainVault</title>
    <meta name="description" content="{{ $bookmark->description ?? $bookmark->ai_summary ?? '' }}">
    <meta property="og:title" content="{{ $bookmark->title ?? $bookmark->url }}">
    <meta property="og:description" content="{{ $bookmark->description ?? $bookmark->ai_summary ?? '' }}">
    @if($bookmark->og_image_url)
        <meta property="og:image" content="{{ $bookmark->og_image_url }}">
    @endif
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-surface-50 dark:bg-surface-950 flex items-center justify-center p-4">
    <div class="max-w-lg w-full">
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden shadow-lg">
            @if($bookmark->og_image_url)
                <img src="{{ $bookmark->og_image_url }}" alt="" class="w-full aspect-video object-cover">
            @endif
            <div class="p-6">
                <div class="flex items-center gap-2 mb-3">
                    @if($bookmark->favicon_url)
                        <img src="{{ $bookmark->favicon_url }}" alt="" class="w-5 h-5 rounded">
                    @endif
                    <span class="text-sm text-gray-500">{{ $bookmark->site_name ?? parse_url($bookmark->url, PHP_URL_HOST) }}</span>
                </div>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white mb-2">{{ $bookmark->title ?? 'Untitled' }}</h1>
                @if($bookmark->description)
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $bookmark->description }}</p>
                @endif
                @if($bookmark->ai_summary)
                    <div class="p-3 bg-primary-50 dark:bg-primary-900/20 rounded-xl mb-4">
                        <p class="text-xs font-medium text-primary-700 dark:text-primary-400 mb-1">AI Summary</p>
                        <p class="text-sm text-gray-700 dark:text-gray-300">{{ $bookmark->ai_summary }}</p>
                    </div>
                @endif
                @if($bookmark->tags->count() > 0)
                    <div class="flex flex-wrap gap-1 mb-4">
                        @foreach($bookmark->tags as $tag)
                            <span class="px-2 py-0.5 text-xs font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-md">{{ $tag->name }}</span>
                        @endforeach
                    </div>
                @endif
                <a href="{{ $bookmark->url }}" target="_blank" class="block w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all text-center">
                    Visit Link
                </a>
            </div>
        </div>
        <p class="text-center text-xs text-gray-400 mt-4">Shared via <a href="/" class="text-primary-500 hover:underline">BrainVault</a></p>
    </div>
</body>
</html>
