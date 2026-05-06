@extends('layouts.app')

@section('title', 'API Tokens')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Settings') }}</h1>

    {{-- Navigation Tabs --}}
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1 mb-8 w-fit">
        <a href="{{ route('settings.profile') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">{{ __('Profile') }}</a>
        <a href="{{ route('settings.tokens') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm">{{ __('API Tokens') }}</a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- New Token Created --}}
    @if(session('token'))
        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl">
            <p class="text-sm font-medium text-amber-800 dark:text-amber-300 mb-2">{{ __("Your new API token (copy now - it won't be shown again):") }}</p>
            <div class="flex items-center gap-2">
                <code class="flex-1 px-3 py-2 bg-white dark:bg-surface-800 rounded-lg text-xs font-mono text-gray-900 dark:text-gray-100 break-all">{{ session('token') }}</code>
                <button onclick="navigator.clipboard.writeText('{{ session('token') }}')" class="px-3 py-2 bg-primary-600 text-white text-xs font-medium rounded-lg hover:bg-primary-700 transition-colors">{{ __('Copy') }}</button>
            </div>
        </div>
    @endif

    {{-- Create Token --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('Create API Token') }}</h2>
        <p class="text-sm text-gray-500 mb-4">{{ __('API tokens allow the Chrome extension and third-party apps to access BrainVault on your behalf.') }}</p>
        <form action="{{ route('settings.tokens.create') }}" method="POST" class="flex flex-col sm:flex-row sm:items-end gap-3">
            @csrf
            <div class="flex-1">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Token Name') }}</label>
                <input type="text" name="name" required placeholder="e.g., Chrome Extension"
                       class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">
            </div>
            <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                {{ __('Create Token') }}
            </button>
        </form>
    </div>

    {{-- Existing Tokens --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Active Tokens') }}</h2>
        @if($tokens->count() > 0)
            <div class="space-y-3">
                @foreach($tokens as $token)
                    <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $token->name }}</p>
                            <p class="text-xs text-gray-500">{{ __('Created') }} {{ $token->created_at->diffForHumans() }} &middot; {{ __('Last used') }} {{ $token->last_used_at?->diffForHumans() ?? __('Never') }}</p>
                        </div>
                        <form action="{{ route('settings.tokens.delete', $token->id) }}" method="POST" onsubmit="return confirm('Revoke this token?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-3 py-1.5 text-xs font-medium text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                {{ __('Revoke') }}
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-sm text-gray-500 text-center py-4">{{ __('No API tokens yet. Create one to connect the Chrome extension.') }}</p>
        @endif
    </div>
</div>
@endsection
