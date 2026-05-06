<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $collection->name }} - BrainVault</title>
    <meta name="description" content="{{ $collection->description ?? 'A shared collection from BrainVault' }}">
    <meta property="og:title" content="{{ $collection->name }}">
    <meta property="og:description" content="{{ $collection->description ?? 'A shared collection from BrainVault' }}">
    @vite(['resources/css/app.css'])
</head>
<body class="min-h-screen bg-surface-50 dark:bg-surface-950 p-4 sm:p-8">
    <div class="max-w-4xl mx-auto">
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">{{ $collection->name }}</h1>
            @if($collection->description)
                <p class="text-gray-500">{{ $collection->description }}</p>
            @endif
            <p class="text-sm text-gray-400 mt-2">{{ $collection->bookmarks->count() }} bookmarks</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($collection->bookmarks as $bookmark)
                <a href="{{ $bookmark->url }}" target="_blank" class="block bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden hover:shadow-lg transition-shadow group">
                    @if($bookmark->og_image_url)
                        <div class="aspect-video bg-gray-100 dark:bg-surface-800">
                            <img src="{{ $bookmark->og_image_url }}" alt="" class="w-full h-full object-cover">
                        </div>
                    @endif
                    <div class="p-4">
                        <div class="flex items-center gap-2 mb-2">
                            @if($bookmark->favicon_url)
                                <img src="{{ $bookmark->favicon_url }}" alt="" class="w-4 h-4 rounded">
                            @endif
                            <span class="text-xs text-gray-500">{{ $bookmark->site_name ?? parse_url($bookmark->url, PHP_URL_HOST) }}</span>
                        </div>
                        <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-primary-600">{{ $bookmark->title ?? 'Untitled' }}</h3>
                        @if($bookmark->description)
                            <p class="text-xs text-gray-500 mt-1 line-clamp-2">{{ $bookmark->description }}</p>
                        @endif
                        @if($bookmark->tags->count() > 0)
                            <div class="flex flex-wrap gap-1 mt-2">
                                @foreach($bookmark->tags->take(3) as $tag)
                                    <span class="px-1.5 py-0.5 text-[10px] font-medium bg-gray-100 dark:bg-surface-800 text-gray-600 dark:text-gray-400 rounded-md">{{ $tag->name }}</span>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </a>
            @endforeach
        </div>

        <p class="text-center text-xs text-gray-400 mt-8">Shared via <a href="/" class="text-primary-500 hover:underline">BrainVault</a></p>
    </div>
</body>
</html>
