<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.darkMode.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chrome Extension - BrainVault</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="description" content="Download the BrainVault Chrome extension to save bookmarks, highlight text, and take notes on any webpage.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-surface-950 font-sans antialiased overflow-x-hidden">
    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 glass" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 animated-gradient rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">BrainVault</span>
                </a>
                <div class="hidden md:flex items-center gap-8">
                    <a href="{{ route('home') }}#features" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Features') }}</a>
                    <a href="{{ route('home') }}#pricing" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Pricing') }}</a>
                    <a href="{{ route('extension') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ __('Extension') }}</a>
                    <a href="{{ route('docs') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Docs') }}</a>
                </div>
                <div class="hidden md:flex items-center gap-3">
                    <x-language-switcher />
                    <button @click="$store.darkMode.toggle()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors">
                        <svg x-show="!$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 btn-gradient text-sm font-semibold rounded-xl transition-all">{{ __('Dashboard') }}</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Sign In') }}</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 btn-gradient text-sm font-semibold rounded-xl transition-all">{{ __('Get Started Free') }}</a>
                    @endauth
                </div>

                {{-- Mobile menu button --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>

            {{-- Mobile Nav --}}
            <div x-show="mobileMenu" x-transition class="md:hidden py-4 border-t border-gray-200 dark:border-gray-800">
                <div class="space-y-2">
                    <a href="{{ route('home') }}#features" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">{{ __('Features') }}</a>
                    <a href="{{ route('home') }}#pricing" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">{{ __('Pricing') }}</a>
                    <a href="{{ route('extension') }}" class="block px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">{{ __('Extension') }}</a>
                    <a href="{{ route('docs') }}" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">{{ __('Docs') }}</a>
                    <div class="flex items-center gap-3 px-3 py-2">
                        <x-language-switcher />
                        <button @click="$store.darkMode.toggle()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors">
                            <svg x-show="!$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                            <svg x-show="$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                        </button>
                    </div>
                    @guest
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300">{{ __('Sign In') }}</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 btn-gradient text-sm font-semibold rounded-xl">{{ __('Get Started Free') }}</a>
                    </div>
                    @else
                    <div class="pt-2">
                        <a href="{{ route('dashboard') }}" class="block text-center px-4 py-2 btn-gradient text-sm font-semibold rounded-xl">{{ __('Dashboard') }}</a>
                    </div>
                    @endguest
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero --}}
    <section class="relative pt-32 pb-16 lg:pt-40 lg:pb-24 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-amber-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-full text-sm text-amber-700 dark:text-amber-400 font-medium mb-6">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                {{ __('Free Download') }}
            </div>

            <h1 class="text-4xl lg:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                BrainVault <span class="text-gradient">{{ __('Chrome Extension') }}</span>
            </h1>
            <p class="mt-6 text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                {{ __('Save bookmarks, highlight text, and take notes directly on any webpage. Your highlights sync automatically to your BrainVault dashboard.') }}
            </p>

            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/downloads/brainvault-extension.zip" download class="w-full sm:w-auto px-8 py-4 btn-gradient font-semibold rounded-2xl transition-all text-lg inline-flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    {{ __('Download Extension') }}
                </a>
                <a href="#installation" class="w-full sm:w-auto px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-2xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors text-lg inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    {{ __('Installation Guide') }}
                </a>
            </div>
        </div>
    </section>

    {{-- Installation Steps --}}
    <section id="installation" class="py-16 lg:py-24 bg-surface-50 dark:bg-surface-900/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ __('Installation Guide') }}</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('Set up the extension in 4 simple steps.') }}</p>
            </div>

            <div class="space-y-8">
                {{-- Step 1 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="flex items-start gap-5">
                        <div class="w-10 h-10 rounded-xl animated-gradient flex items-center justify-center text-white font-bold text-lg flex-shrink-0">1</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Download the Extension') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('Click the download button above to get the extension zip file. Save it to your computer and extract (unzip) it.') }}</p>
                            <div class="bg-surface-50 dark:bg-surface-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Tip:') }}</span> {{ __("After extracting, you'll see a dist folder inside. This is the folder you'll need in the next step.") }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 2 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="flex items-start gap-5">
                        <div class="w-10 h-10 rounded-xl animated-gradient flex items-center justify-center text-white font-bold text-lg flex-shrink-0">2</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Open Chrome Extensions Page') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('Open Google Chrome and go to the extensions page.') }}</p>
                            <div class="bg-surface-50 dark:bg-surface-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 space-y-2">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Type this in your Chrome address bar:') }}</p>
                                <code class="block bg-gray-900 text-green-400 px-4 py-3 rounded-lg text-sm font-mono">chrome://extensions</code>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mt-2">{{ __('Then enable') }} <span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Developer Mode') }}</span> {{ __('by toggling the switch in the top-right corner.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 3 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="flex items-start gap-5">
                        <div class="w-10 h-10 rounded-xl animated-gradient flex items-center justify-center text-white font-bold text-lg flex-shrink-0">3</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Load the Extension') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __('Click') }} <span class="font-semibold text-gray-700 dark:text-gray-300">"{{ __('Load unpacked') }}"</span> {{ __('button that appears after enabling Developer Mode.') }}</p>
                            <div class="bg-surface-50 dark:bg-surface-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700">
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ __('Navigate to the extracted zip folder and select the dist folder. Click "Select Folder" to install the extension.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Step 4 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="flex items-start gap-5">
                        <div class="w-10 h-10 rounded-xl animated-gradient flex items-center justify-center text-white font-bold text-lg flex-shrink-0">4</div>
                        <div class="flex-1">
                            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">{{ __('Connect to Your Account') }}</h3>
                            <p class="text-gray-600 dark:text-gray-400 mb-4">{{ __("Click the BrainVault extension icon in Chrome's toolbar and enter your API token to connect.") }}</p>
                            <div class="bg-surface-50 dark:bg-surface-800 rounded-xl p-4 border border-gray-100 dark:border-gray-700 space-y-3">
                                <p class="text-sm text-gray-600 dark:text-gray-400"><span class="font-semibold text-gray-700 dark:text-gray-300">{{ __('Get your API token:') }}</span></p>
                                <ol class="text-sm text-gray-600 dark:text-gray-400 space-y-1 list-decimal list-inside">
                                    <li>{{ __('Log in to your BrainVault account') }}</li>
                                    <li>{{ __('Go to') }} <a href="{{ route('settings.tokens') }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('Settings') }} &rarr; {{ __('API Tokens') }}</a></li>
                                    <li>{{ __('Create a new token and copy it') }}</li>
                                    <li>{{ __('Paste the token in the extension popup') }}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- How to Use --}}
    <section class="py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ __('How to Use') }}</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('Everything you can do with the BrainVault extension.') }}</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8">
                {{-- Highlighting --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Highlighting Text') }}</h3>
                    <ol class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-decimal list-inside leading-relaxed">
                        <li>{{ __('Select any text on a webpage') }}</li>
                        <li>{{ __('A color picker tooltip will appear') }}</li>
                        <li>{{ __('Click a color to save the highlight') }}</li>
                        <li>{{ __("A success notification confirms it's saved") }}</li>
                    </ol>
                    <div class="mt-4 flex gap-2">
                        <span class="w-6 h-6 rounded-full bg-yellow-300 border-2 border-yellow-400"></span>
                        <span class="w-6 h-6 rounded-full bg-green-300 border-2 border-green-400"></span>
                        <span class="w-6 h-6 rounded-full bg-blue-300 border-2 border-blue-400"></span>
                        <span class="w-6 h-6 rounded-full bg-pink-300 border-2 border-pink-400"></span>
                        <span class="w-6 h-6 rounded-full bg-purple-300 border-2 border-purple-400"></span>
                    </div>
                    <p class="mt-2 text-xs text-gray-500 dark:text-gray-500">{{ __('Available highlight colors') }}</p>
                </div>

                {{-- Bookmarks --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Saving Bookmarks') }}</h3>
                    <ol class="text-gray-600 dark:text-gray-400 text-sm space-y-2 list-decimal list-inside leading-relaxed">
                        <li>{{ __('Click the BrainVault icon in Chrome toolbar') }}</li>
                        <li>{{ __('Click "Save Bookmark" to save the current page') }}</li>
                        <li>{{ __('Add tags or notes if you want') }}</li>
                        <li>{{ __('The page is saved to your BrainVault dashboard') }}</li>
                    </ol>
                </div>

                {{-- Persistent Highlights --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('Persistent Highlights') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                        {{ __('Your highlights are saved to the cloud and automatically restored when you revisit a page. They persist across browser sessions and devices.') }}
                    </p>
                </div>

                {{-- Dashboard --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-3">{{ __('View on Dashboard') }}</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                        {{ __('All your highlights and bookmarks appear on your') }} <a href="{{ route('login') }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('BrainVault dashboard') }}</a>. {{ __('Search, filter by color, and organize them into collections.') }}
                    </p>
                </div>
            </div>
        </div>
    </section>

    {{-- Troubleshooting --}}
    <section class="py-16 lg:py-24 bg-surface-50 dark:bg-surface-900/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ __('Troubleshooting') }}</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('Common issues and how to fix them.') }}</p>
            </div>

            <div class="space-y-4">
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Highlight not saving / no toast notification') }}</h3>
                        <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p>{{ __("This usually means you're not logged in to the extension. Click the BrainVault icon and check if your API token is set.") }}</p>
                        <p>{{ __("If you recently reinstalled the extension, you'll need to re-enter your API token as Chrome assigns a new extension ID.") }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Highlights not showing after page refresh') }}</h3>
                        <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p>{{ __("Make sure you're logged in. Highlights are restored from the server, so an active connection is required.") }}</p>
                        <p>{{ __('On dynamic pages (SPAs), highlights may take a moment to appear as the extension waits for content to load.') }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('Extension not appearing in Chrome toolbar') }}</h3>
                        <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p>{{ __("Click the puzzle icon in Chrome's toolbar to see all extensions. Find BrainVault and click the pin icon to keep it visible.") }}</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6" x-data="{ open: false }">
                    <button @click="open = !open" class="flex items-center justify-between w-full text-left">
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ __('How to update the extension') }}</h3>
                        <svg :class="{ 'rotate-180': open }" class="w-5 h-5 text-gray-400 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                    </button>
                    <div x-show="open" x-transition class="mt-4 text-sm text-gray-600 dark:text-gray-400 space-y-2">
                        <p>{{ __('Download the latest version from this page. Extract it and replace the old dist folder with the new one.') }}</p>
                        <p>{{ __('Then go to chrome://extensions and click the refresh icon on the BrainVault extension card.') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ __('Ready to start?') }}</h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('Download the extension and start capturing knowledge from the web.') }}</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="/downloads/brainvault-extension.zip" download class="w-full sm:w-auto px-8 py-4 btn-gradient font-semibold rounded-2xl transition-all text-lg inline-flex items-center justify-center gap-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    {{ __('Download Extension') }}
                </a>
                @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-2xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors text-lg">
                        {{ __('Create Free Account') }}
                    </a>
                @endguest
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 dark:border-gray-800 py-12 bg-white dark:bg-surface-950">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-8 h-8 animated-gradient rounded-xl flex items-center justify-center">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">BrainVault</span>
                </a>
                <p class="text-sm text-gray-500">&copy; {{ date('Y') }} BrainVault. {{ __('All rights reserved.') }}</p>
            </div>
        </div>
    </footer>
</body>
</html>
