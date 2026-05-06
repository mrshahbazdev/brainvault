<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.darkMode.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'BrainVault' }} - BrainVault</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-surface-50 dark:bg-surface-950 font-sans antialiased">
    <div class="min-h-screen flex">
        {{-- Left Brand Panel (hidden on mobile) --}}
        <div class="hidden lg:flex lg:w-1/2 animated-gradient relative overflow-hidden">
            <div class="absolute inset-0 bg-black/10"></div>
            <div class="relative z-10 flex flex-col justify-between p-12 text-white w-full">
                {{-- Logo --}}
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold tracking-tight">BrainVault</span>
                    </a>
                </div>

                {{-- Center Content --}}
                <div class="space-y-8">
                    <div>
                        <h1 class="text-4xl font-bold leading-tight">{{ __('Your Second Brain for the Web') }}</h1>
                        <p class="mt-4 text-lg text-white/80 max-w-md">{{ __('Save, highlight, annotate, and organize everything you read online. Powered by AI.') }}</p>
                    </div>

                    {{-- Feature highlights --}}
                    <div class="space-y-4">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                            </div>
                            <span class="text-white/90">{{ __('One-click bookmarks with auto-metadata') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                            </div>
                            <span class="text-white/90">{{ __('AI-powered summaries & smart search') }}</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                            </div>
                            <span class="text-white/90">{{ __('Highlight & annotate any website') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Bottom --}}
                <div class="text-sm text-white/50">
                    &copy; {{ date('Y') }} BrainVault. {{ __('All rights reserved.') }}
                </div>
            </div>

            {{-- Decorative circles --}}
            <div class="absolute -top-20 -right-20 w-80 h-80 bg-white/5 rounded-full animate-float"></div>
            <div class="absolute -bottom-32 -left-16 w-96 h-96 bg-white/5 rounded-full animate-float-delayed"></div>
        </div>

        {{-- Right Auth Form Panel --}}
        <div class="flex-1 flex items-center justify-center p-6 sm:p-12">
            <div class="w-full max-w-md">
                {{-- Mobile Logo --}}
                <div class="lg:hidden mb-8 text-center">
                    <a href="{{ route('home') }}" class="inline-flex items-center gap-3">
                        <div class="w-10 h-10 bg-primary-500 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                            </svg>
                        </div>
                        <span class="text-xl font-bold text-gray-900 dark:text-white">BrainVault</span>
                    </a>
                </div>

                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
