@extends('layouts.app')

@section('title', __('Import & Export'))

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Import & Export') }}</h1>

    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
            <p class="text-sm text-red-700 dark:text-red-400">{{ session('error') }}</p>
        </div>
    @endif

    {{-- Import --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Import Bookmarks') }}</h2>
        <form action="{{ route('import.process') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">{{ __('Source') }}</label>
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                        @foreach(['chrome' => ['Chrome / Firefox', 'HTML bookmark file'], 'pocket' => ['Pocket', 'JSON export'], 'raindrop' => ['Raindrop.io', 'JSON export']] as $source => [$name, $desc])
                            <label class="relative cursor-pointer">
                                <input type="radio" name="source" value="{{ $source }}" class="peer sr-only" {{ $source === 'chrome' ? 'checked' : '' }}>
                                <div class="p-4 bg-gray-50 dark:bg-surface-800 rounded-xl border-2 border-transparent peer-checked:border-primary-500 peer-checked:bg-primary-50 dark:peer-checked:bg-primary-900/20 transition-all">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $name }}</p>
                                    <p class="text-xs text-gray-500 mt-0.5">{{ $desc }}</p>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Upload File') }}</label>
                    <div class="flex items-center justify-center w-full">
                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-xl cursor-pointer bg-gray-50 dark:bg-surface-800 dark:border-gray-700 hover:bg-gray-100 dark:hover:bg-surface-700 transition-colors">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                                <p class="text-sm text-gray-500">{{ __('Click or drag to upload') }}</p>
                                <p class="text-xs text-gray-400">{{ __('HTML, JSON (max 10MB)') }}</p>
                            </div>
                            <input type="file" name="file" class="hidden" accept=".html,.json,.htm" required>
                        </label>
                    </div>
                    @error('file') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                    {{ __('Import Bookmarks') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Export --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Export Bookmarks') }}</h2>
        <p class="text-sm text-gray-500 mb-4">{{ __('Download all your bookmarks in your preferred format.') }}</p>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('export.json') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-surface-800 hover:bg-gray-200 dark:hover:bg-surface-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                {{ __('Export JSON') }}
            </a>
            <a href="{{ route('export.html') }}" class="flex items-center gap-2 px-4 py-2.5 bg-gray-100 dark:bg-surface-800 hover:bg-gray-200 dark:hover:bg-surface-700 text-gray-700 dark:text-gray-300 text-sm font-medium rounded-xl transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                {{ __('Export HTML') }}
            </a>
        </div>
    </div>
</div>
@endsection
