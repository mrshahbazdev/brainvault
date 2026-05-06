@extends('layouts.app')

@section('title', 'Settings')

@section('content')
<div class="max-w-3xl mx-auto">
    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Settings') }}</h1>

    {{-- Navigation Tabs --}}
    <div class="flex items-center gap-1 bg-gray-100 dark:bg-surface-800 rounded-xl p-1 mb-8 w-fit">
        <a href="{{ route('settings.profile') }}" class="px-4 py-2 text-sm font-medium rounded-lg bg-white dark:bg-surface-700 text-gray-900 dark:text-white shadow-sm">{{ __('Profile') }}</a>
        <a href="{{ route('settings.tokens') }}" class="px-4 py-2 text-sm font-medium rounded-lg text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">{{ __('API Tokens') }}</a>
    </div>

    {{-- Success Message --}}
    @if(session('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <p class="text-sm text-green-700 dark:text-green-400">{{ session('success') }}</p>
        </div>
    @endif

    {{-- Profile Form --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Profile Information') }}</h2>
        <form action="{{ route('settings.profile.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Name') }}</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                           class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    @error('name') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Email') }}</label>
                    <input type="email" value="{{ $user->email }}" disabled
                           class="w-full px-4 py-2.5 bg-gray-50 dark:bg-surface-800/50 border-0 rounded-xl text-sm text-gray-500 cursor-not-allowed">
                    <p class="mt-1 text-xs text-gray-400">{{ __('Email cannot be changed.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Bio') }}</label>
                    <textarea name="bio" rows="3" placeholder="{{ __('Tell us about yourself...') }}"
                              class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500">{{ old('bio', $user->bio) }}</textarea>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Timezone') }}</label>
                        <select name="timezone" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            @foreach(['UTC', 'America/New_York', 'America/Chicago', 'America/Denver', 'America/Los_Angeles', 'Europe/London', 'Europe/Paris', 'Asia/Karachi', 'Asia/Kolkata', 'Asia/Tokyo', 'Australia/Sydney'] as $tz)
                                <option value="{{ $tz }}" {{ $user->timezone === $tz ? 'selected' : '' }}>{{ $tz }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Language') }}</label>
                        <select name="language" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="en" {{ $user->language === 'en' ? 'selected' : '' }}>English</option>
                            <option value="ur" {{ $user->language === 'ur' ? 'selected' : '' }}>Urdu</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                    {{ __('Save Changes') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Password Form --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 mb-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Change Password') }}</h2>
        <form action="{{ route('settings.password.update') }}" method="POST">
            @csrf
            @method('PUT')
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Current Password') }}</label>
                    <input type="password" name="current_password" required
                           class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    @error('current_password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('New Password') }}</label>
                    <input type="password" name="password" required
                           class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    @error('password') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Confirm New Password') }}</label>
                    <input type="password" name="password_confirmation" required
                           class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                    {{ __('Update Password') }}
                </button>
            </div>
        </form>
    </div>

    {{-- Connected Accounts --}}
    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Connected Accounts') }}</h2>
        <div class="space-y-3">
            @php
                $socialAccounts = $user->socialAccounts ?? collect();
                $google = $socialAccounts->where('provider', 'google')->first();
                $github = $socialAccounts->where('provider', 'github')->first();
            @endphp
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 01-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">Google</p>
                        <p class="text-xs text-gray-500">{{ $google ? __('Connected') : __('Not connected') }}</p>
                    </div>
                </div>
                @if(!$google)
                    <a href="{{ route('social.redirect', 'google') }}" class="px-3 py-1.5 text-xs font-medium bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors">{{ __('Connect') }}</a>
                @endif
            </div>
            <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                <div class="flex items-center gap-3">
                    <svg class="w-5 h-5 text-gray-900 dark:text-white" fill="currentColor" viewBox="0 0 24 24"><path d="M12 0C5.374 0 0 5.373 0 12c0 5.302 3.438 9.8 8.207 11.387.599.111.793-.261.793-.577v-2.234c-3.338.726-4.033-1.416-4.033-1.416-.546-1.387-1.333-1.756-1.333-1.756-1.089-.745.083-.729.083-.729 1.205.084 1.839 1.237 1.839 1.237 1.07 1.834 2.807 1.304 3.492.997.107-.775.418-1.305.762-1.604-2.665-.305-5.467-1.334-5.467-5.931 0-1.311.469-2.381 1.236-3.221-.124-.303-.535-1.524.117-3.176 0 0 1.008-.322 3.301 1.23A11.509 11.509 0 0112 5.803c1.02.005 2.047.138 3.006.404 2.291-1.552 3.297-1.23 3.297-1.23.653 1.653.242 2.874.118 3.176.77.84 1.235 1.911 1.235 3.221 0 4.609-2.807 5.624-5.479 5.921.43.372.823 1.102.823 2.222v3.293c0 .319.192.694.801.576C20.566 21.797 24 17.3 24 12c0-6.627-5.373-12-12-12z"/></svg>
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">GitHub</p>
                        <p class="text-xs text-gray-500">{{ $github ? __('Connected') : __('Not connected') }}</p>
                    </div>
                </div>
                @if(!$github)
                    <a href="{{ route('social.redirect', 'github') }}" class="px-3 py-1.5 text-xs font-medium bg-gray-800 dark:bg-gray-700 text-white rounded-lg hover:bg-gray-900 dark:hover:bg-gray-600 transition-colors">{{ __('Connect') }}</a>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
