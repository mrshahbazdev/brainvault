<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.darkMode.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Documentation') }} - BrainVault</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="description" content="BrainVault documentation — learn how to use bookmarks, highlights, notes, collections, the Chrome extension, and more.">
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
                    <a href="{{ route('extension') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">{{ __('Extension') }}</a>
                    <a href="{{ route('docs') }}" class="text-sm font-medium text-primary-600 dark:text-primary-400">{{ __('Docs') }}</a>
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
                    <a href="{{ route('extension') }}" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">{{ __('Extension') }}</a>
                    <a href="{{ route('docs') }}" class="block px-3 py-2 text-sm font-medium text-primary-600 dark:text-primary-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">{{ __('Docs') }}</a>
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
    <section class="relative pt-32 pb-12 lg:pt-40 lg:pb-16 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-secondary-500/10 rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h1 class="text-4xl lg:text-6xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                {{ __('Documentation') }}
            </h1>
            <p class="mt-6 text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                {{ __('Learn how to use BrainVault to save, organize, and manage your web knowledge.') }}
            </p>
        </div>
    </section>

    {{-- Quick Links --}}
    <section class="py-12 lg:py-16">
        <div class="max-w-5xl mx-auto px-6 lg:px-8">
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                <a href="#getting-started" class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-primary-100 dark:bg-primary-900/30 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Getting Started') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Create your account and set up your workspace.') }}</p>
                </a>
                <a href="#bookmarks" class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Bookmarks') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Save and organize web pages with tags and collections.') }}</p>
                </a>
                <a href="#highlights" class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Highlights') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Highlight text on any webpage and sync to your dashboard.') }}</p>
                </a>
                <a href="#notes" class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Notes') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Create and organize notes linked to your bookmarks.') }}</p>
                </a>
                <a href="#collections" class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Collections') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Group related bookmarks and notes into collections.') }}</p>
                </a>
                <a href="#chrome-extension" class="group bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 hover:border-primary-300 dark:hover:border-primary-700 transition-colors">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center mb-4">
                        <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </div>
                    <h3 class="text-base font-bold text-gray-900 dark:text-white group-hover:text-primary-600 dark:group-hover:text-primary-400 transition-colors">{{ __('Chrome Extension') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Install and use the browser extension.') }}</p>
                </a>
            </div>
        </div>
    </section>

    {{-- Documentation Sections --}}
    <section class="py-12 lg:py-16 bg-surface-50 dark:bg-surface-900/50">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 space-y-16">

            {{-- Getting Started --}}
            <div id="getting-started">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Getting Started') }}</h2>
                <div class="space-y-4">
                    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('1. Create Your Account') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Visit the registration page and sign up with your email, or use Google/GitHub to create your account instantly. After registration, verify your email address.') }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('2. Install the Chrome Extension') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Download the Chrome extension from the') }} <a href="{{ route('extension') }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('Extension page') }}</a>. {{ __('Follow the installation guide to load it into Chrome and connect it with your API token.') }}
                        </p>
                    </div>
                    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">{{ __('3. Generate an API Token') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Go to Settings → API Tokens in your dashboard. Create a new token, copy it, and paste it into the Chrome extension popup to connect your account.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Bookmarks --}}
            <div id="bookmarks">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Bookmarks') }}</h2>
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Saving Bookmarks') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Click the BrainVault extension icon in your Chrome toolbar while on any webpage. Click "Save Bookmark" to save the current page. You can add tags and notes before saving.') }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Managing Bookmarks') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('View all your bookmarks on the dashboard. Use the search bar to find specific bookmarks, filter by tags, or sort by date. Click on any bookmark to open the original page.') }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Tags') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Add tags to bookmarks to categorize and find them later. Tags are searchable and help you organize content by topic, project, or any category that makes sense to you.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Highlights --}}
            <div id="highlights">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Highlights') }}</h2>
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Creating Highlights') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Select any text on a webpage. A color picker tooltip will appear — choose a color (yellow, green, blue, pink, or purple) to save the highlight. A success notification confirms it was saved.') }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Persistent Highlights') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Your highlights are automatically restored when you revisit a page. They are saved to the cloud and persist across browser sessions and devices.') }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Viewing Highlights') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Visit the Highlights page in your dashboard to see all your saved highlights. Filter by color, search by text or source URL, and click any highlight to visit the original page.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Notes --}}
            <div id="notes">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Notes') }}</h2>
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Creating Notes') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Create notes from the Notes page in your dashboard. Notes can be standalone or linked to specific bookmarks for context.') }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Organizing Notes') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Search and filter your notes to find what you need. Notes can be added to collections along with bookmarks for project-based organization.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Collections --}}
            <div id="collections">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Collections') }}</h2>
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('What are Collections?') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Collections are folders for grouping related bookmarks, notes, and highlights together. Use them to organize research projects, reading lists, or any topic.') }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Creating Collections') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('Go to the Collections page and create a new collection with a name and optional description. Then add bookmarks and notes to it from their respective pages.') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Chrome Extension --}}
            <div id="chrome-extension">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('Chrome Extension') }}</h2>
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                        {{ __('The BrainVault Chrome extension lets you save bookmarks, highlight text, and take notes directly on any webpage.') }}
                    </p>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Installation') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('For detailed installation instructions, visit the') }} <a href="{{ route('extension') }}" class="text-primary-600 dark:text-primary-400 hover:underline">{{ __('Extension page') }}</a>.
                        </p>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('API Token') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('The extension requires an API token to connect to your account. Generate one from Settings → API Tokens in your dashboard and paste it into the extension popup.') }}
                        </p>
                    </div>
                    <div class="pt-2">
                        <a href="{{ route('extension') }}" class="inline-flex items-center gap-2 px-6 py-3 btn-gradient font-semibold rounded-xl transition-all text-sm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                            {{ __('Download Extension') }}
                        </a>
                    </div>
                </div>
            </div>

            {{-- API --}}
            <div id="api">
                <h2 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mb-6">{{ __('API') }}</h2>
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6 space-y-4">
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                        {{ __('BrainVault provides a REST API for programmatic access to your bookmarks, highlights, and notes.') }}
                    </p>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Authentication') }}</h3>
                        <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                            {{ __('All API requests require a Bearer token. Include your API token in the Authorization header:') }}
                        </p>
                        <code class="block mt-2 bg-gray-900 text-green-400 px-4 py-3 rounded-lg text-sm font-mono">Authorization: Bearer your-api-token</code>
                    </div>
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">{{ __('Endpoints') }}</h3>
                        <div class="space-y-2 mt-2">
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                                <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded">GET</span>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/bookmarks</code>
                                <span class="text-xs text-gray-500 ml-auto">{{ __('List bookmarks') }}</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded">POST</span>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/bookmarks</code>
                                <span class="text-xs text-gray-500 ml-auto">{{ __('Create bookmark') }}</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                                <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded">GET</span>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/highlights</code>
                                <span class="text-xs text-gray-500 ml-auto">{{ __('List highlights') }}</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded">POST</span>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/highlights</code>
                                <span class="text-xs text-gray-500 ml-auto">{{ __('Create highlight') }}</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                                <span class="px-2 py-0.5 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400 text-xs font-bold rounded">GET</span>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/notes</code>
                                <span class="text-xs text-gray-500 ml-auto">{{ __('List notes') }}</span>
                            </div>
                            <div class="flex items-center gap-3 p-3 bg-gray-50 dark:bg-surface-800 rounded-xl">
                                <span class="px-2 py-0.5 bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold rounded">POST</span>
                                <code class="text-sm font-mono text-gray-700 dark:text-gray-300">/api/notes</code>
                                <span class="text-xs text-gray-500 ml-auto">{{ __('Create note') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </section>

    {{-- CTA --}}
    <section class="py-16 lg:py-24">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">{{ __('Ready to start?') }}</h2>
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">{{ __('Create your account and start building your second brain.') }}</p>
            <div class="mt-8 flex flex-col sm:flex-row items-center justify-center gap-4">
                @guest
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 btn-gradient font-semibold rounded-2xl transition-all text-lg">{{ __('Get Started Free') }}</a>
                @endguest
                <a href="{{ route('extension') }}" class="w-full sm:w-auto px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-2xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors text-lg inline-flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    {{ __('Download Extension') }}
                </a>
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
