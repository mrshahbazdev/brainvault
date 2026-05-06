<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data x-init="$store.darkMode.init()">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>BrainVault - Your Second Brain for the Web</title>
    <link rel="icon" type="image/png" href="/favicon.png">
    <meta name="description" content="Save, highlight, annotate, and organize everything you read online. AI-powered bookmark and notes management platform.">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white dark:bg-surface-950 font-sans antialiased overflow-x-hidden">
    {{-- Navigation --}}
    <nav class="fixed top-0 left-0 right-0 z-50 glass" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                {{-- Logo --}}
                <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                    <div class="w-9 h-9 animated-gradient rounded-xl flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                        </svg>
                    </div>
                    <span class="text-lg font-bold text-gray-900 dark:text-white">BrainVault</span>
                </a>

                {{-- Desktop Nav --}}
                <div class="hidden md:flex items-center gap-8">
                    <a href="#features" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Features</a>
                    <a href="#how-it-works" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">How it Works</a>
                    <a href="#pricing" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Pricing</a>
                    <a href="{{ route('extension') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Extension</a>
                </div>

                {{-- Auth Buttons --}}
                <div class="hidden md:flex items-center gap-3">
                    <button @click="$store.darkMode.toggle()" class="p-2 rounded-lg text-gray-500 hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors">
                        <svg x-show="!$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" /></svg>
                        <svg x-show="$store.darkMode.on" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </button>
                    @auth
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 btn-gradient text-sm font-semibold rounded-xl transition-all">Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">Sign In</a>
                        <a href="{{ route('register') }}" class="px-4 py-2 btn-gradient text-sm font-semibold rounded-xl transition-all">Get Started Free</a>
                    @endauth
                </div>

                {{-- Mobile menu --}}
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-2 rounded-lg text-gray-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" /></svg>
                </button>
            </div>

            {{-- Mobile Nav --}}
            <div x-show="mobileMenu" x-transition class="md:hidden py-4 border-t border-gray-200 dark:border-gray-800">
                <div class="space-y-2">
                    <a href="#features" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">Features</a>
                    <a href="#how-it-works" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">How it Works</a>
                    <a href="#pricing" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">Pricing</a>
                    <a href="{{ route('extension') }}" class="block px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800">Extension</a>
                    <div class="flex gap-3 pt-2">
                        <a href="{{ route('login') }}" class="flex-1 text-center px-4 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300">Sign In</a>
                        <a href="{{ route('register') }}" class="flex-1 text-center px-4 py-2 btn-gradient text-sm font-semibold rounded-xl">Get Started</a>
                    </div>
                </div>
            </div>
        </div>
    </nav>

    {{-- Hero Section --}}
    <section class="relative pt-32 pb-20 lg:pt-40 lg:pb-32 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="absolute -top-40 -right-40 w-[600px] h-[600px] bg-primary-500/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-40 -left-40 w-[600px] h-[600px] bg-secondary-500/10 rounded-full blur-3xl"></div>
        </div>

        <div class="relative max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-4xl mx-auto">
                {{-- Badge --}}
                <div class="inline-flex items-center gap-2 px-3 py-1 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-full text-sm text-primary-700 dark:text-primary-400 font-medium mb-6">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                    Powered by AI
                </div>

                <h1 class="text-5xl lg:text-7xl font-extrabold tracking-tight text-gray-900 dark:text-white">
                    Your <span class="text-gradient">Second Brain</span><br>for the Web
                </h1>
                <p class="mt-6 text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto leading-relaxed">
                    Save bookmarks, highlight text, take notes, and let AI organize your knowledge. One platform for everything you read, learn, and discover online.
                </p>

                {{-- CTA Buttons --}}
                <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                    <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 btn-gradient font-semibold rounded-2xl transition-all text-lg">
                        Get Started Free
                    </a>
                    <a href="#features" class="w-full sm:w-auto px-8 py-4 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-2xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors text-lg flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Watch Demo
                    </a>
                </div>

                <p class="mt-4 text-sm text-gray-500">Free forever. No credit card required.</p>
            </div>

            {{-- Hero Image / Dashboard Preview --}}
            <div class="mt-16 relative mx-auto max-w-5xl">
                <div class="absolute -inset-4 bg-gradient-to-r from-primary-500/20 via-secondary-500/20 to-primary-500/20 rounded-3xl blur-2xl"></div>
                <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
                    <div class="flex items-center gap-2 px-4 py-3 border-b border-gray-100 dark:border-gray-800 bg-gray-50 dark:bg-surface-800">
                        <div class="flex gap-1.5">
                            <div class="w-3 h-3 rounded-full bg-red-400"></div>
                            <div class="w-3 h-3 rounded-full bg-amber-400"></div>
                            <div class="w-3 h-3 rounded-full bg-green-400"></div>
                        </div>
                        <div class="flex-1 flex justify-center">
                            <div class="px-4 py-1 bg-white dark:bg-surface-900 rounded-lg text-xs text-gray-400 border border-gray-200 dark:border-gray-700">brainvault.app/dashboard</div>
                        </div>
                    </div>
                    <div class="aspect-[16/9] bg-gradient-to-br from-surface-50 via-primary-50/30 to-secondary-50/30 dark:from-surface-900 dark:via-primary-950/20 dark:to-secondary-950/20 flex items-center justify-center">
                        <div class="text-center px-8">
                            <div class="grid grid-cols-4 gap-4 mb-6">
                                <div class="bg-white dark:bg-surface-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 dark:bg-blue-900/30 mx-auto mb-2"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto mb-1"></div>
                                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-16 mx-auto"></div>
                                </div>
                                <div class="bg-white dark:bg-surface-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 mx-auto mb-2"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto mb-1"></div>
                                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-16 mx-auto"></div>
                                </div>
                                <div class="bg-white dark:bg-surface-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 mx-auto mb-2"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto mb-1"></div>
                                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-16 mx-auto"></div>
                                </div>
                                <div class="bg-white dark:bg-surface-800 rounded-xl p-4 shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="w-8 h-8 rounded-lg bg-purple-100 dark:bg-purple-900/30 mx-auto mb-2"></div>
                                    <div class="h-3 bg-gray-200 dark:bg-gray-700 rounded w-12 mx-auto mb-1"></div>
                                    <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-16 mx-auto"></div>
                                </div>
                            </div>
                            <div class="space-y-3 max-w-lg mx-auto">
                                @for($i = 0; $i < 4; $i++)
                                <div class="flex items-center gap-3 bg-white dark:bg-surface-800 rounded-xl p-3 shadow-sm border border-gray-100 dark:border-gray-700">
                                    <div class="w-10 h-8 rounded-lg bg-gray-100 dark:bg-gray-700 flex-shrink-0"></div>
                                    <div class="flex-1">
                                        <div class="h-2.5 bg-gray-200 dark:bg-gray-700 rounded w-3/4 mb-1"></div>
                                        <div class="h-2 bg-gray-100 dark:bg-gray-800 rounded w-1/2"></div>
                                    </div>
                                </div>
                                @endfor
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Features Section --}}
    <section id="features" class="py-20 lg:py-32 bg-surface-50 dark:bg-surface-900/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Everything you need to<br><span class="text-gradient">capture knowledge</span></h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">From quick saves to deep research, BrainVault handles it all.</p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                {{-- Feature 1 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">One-Click Bookmarks</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Save any page with one click. Auto-captures title, description, screenshots, and metadata.</p>
                </div>

                {{-- Feature 2 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Web Highlighting</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Highlight text directly on any website. Annotations sync to your dashboard automatically.</p>
                </div>

                {{-- Feature 3 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Smart Notes</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Rich text editor for notes with auto-linking to bookmarks, highlights, and topics.</p>
                </div>

                {{-- Feature 4 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">AI Summaries</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Auto-generate summaries, extract keywords, and categorize content with GPT-4.</p>
                </div>

                {{-- Feature 5 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-pink-100 dark:bg-pink-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-pink-600 dark:text-pink-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Powerful Search</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Full-text search across all your bookmarks, notes, highlights, and AI summaries.</p>
                </div>

                {{-- Feature 6 --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl p-8 border border-gray-200 dark:border-gray-800 hover:shadow-xl hover:-translate-y-1 transition-all duration-300">
                    <div class="w-12 h-12 rounded-2xl bg-cyan-100 dark:bg-cyan-900/30 flex items-center justify-center mb-5">
                        <svg class="w-6 h-6 text-cyan-600 dark:text-cyan-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-2">Team Collaboration</h3>
                    <p class="text-gray-600 dark:text-gray-400 text-sm leading-relaxed">Share collections, bookmark together, and build a team knowledge base.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- How it Works --}}
    <section id="how-it-works" class="py-20 lg:py-32">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">How it works</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Three simple steps to organize your online knowledge.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8">
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl animated-gradient flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6">1</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Install Extension</h3>
                    <p class="text-gray-600 dark:text-gray-400">Add the BrainVault Chrome extension to your browser in one click.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl animated-gradient flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6">2</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Save & Highlight</h3>
                    <p class="text-gray-600 dark:text-gray-400">Bookmark pages, highlight text, and add notes while you browse.</p>
                </div>
                <div class="text-center">
                    <div class="w-16 h-16 rounded-2xl animated-gradient flex items-center justify-center text-white text-2xl font-bold mx-auto mb-6">3</div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-3">Let AI Organize</h3>
                    <p class="text-gray-600 dark:text-gray-400">AI auto-categorizes, summarizes, and connects your knowledge.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- Pricing Section --}}
    <section id="pricing" class="py-20 lg:py-32 bg-surface-50 dark:bg-surface-900/50">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="text-center max-w-2xl mx-auto mb-16">
                <h2 class="text-3xl lg:text-4xl font-bold text-gray-900 dark:text-white">Simple, transparent pricing</h2>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Start free, upgrade when you need more power.</p>
            </div>

            <div class="grid md:grid-cols-3 gap-8 max-w-5xl mx-auto">
                {{-- Free Plan --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Free</h3>
                    <p class="mt-2 text-sm text-gray-500">For getting started</p>
                    <div class="mt-6">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">$0</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="mt-8 space-y-3">
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> 100 bookmarks</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> 50 notes</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Chrome extension</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Basic search</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full text-center px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors">Get Started</a>
                </div>

                {{-- Pro Plan --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border-2 border-primary-500 p-8 relative shadow-xl">
                    <div class="absolute -top-3 left-1/2 -translate-x-1/2 px-3 py-1 bg-primary-500 text-white text-xs font-bold rounded-full">Popular</div>
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Pro</h3>
                    <p class="mt-2 text-sm text-gray-500">For power users</p>
                    <div class="mt-6">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">$8</span>
                        <span class="text-gray-500">/month</span>
                    </div>
                    <ul class="mt-8 space-y-3">
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Unlimited bookmarks</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Unlimited notes</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> AI summaries & keywords</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Knowledge graph</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Priority support</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full text-center px-4 py-3 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">Get Pro</a>
                </div>

                {{-- Team Plan --}}
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">Team</h3>
                    <p class="mt-2 text-sm text-gray-500">For teams and orgs</p>
                    <div class="mt-6">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">$15</span>
                        <span class="text-gray-500">/user/month</span>
                    </div>
                    <ul class="mt-8 space-y-3">
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Everything in Pro</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Shared collections</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Team knowledge base</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> Admin dashboard</li>
                        <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400"><svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" /></svg> SSO & audit log</li>
                    </ul>
                    <a href="{{ route('register') }}" class="mt-8 block w-full text-center px-4 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 font-semibold rounded-xl hover:bg-gray-50 dark:hover:bg-surface-800 transition-colors">Start Trial</a>
                </div>
            </div>
        </div>
    </section>

    {{-- CTA Section --}}
    <section class="py-20 lg:py-32">
        <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
            <h2 class="text-3xl lg:text-5xl font-bold text-gray-900 dark:text-white">Ready to build your<br><span class="text-gradient">second brain</span>?</h2>
            <p class="mt-6 text-lg text-gray-600 dark:text-gray-400 max-w-xl mx-auto">Join thousands of knowledge workers who use BrainVault to save, organize, and rediscover information.</p>
            <div class="mt-10 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('register') }}" class="w-full sm:w-auto px-8 py-4 btn-gradient font-semibold rounded-2xl transition-all text-lg">
                    Get Started Free
                </a>
            </div>
        </div>
    </section>

    {{-- Footer --}}
    <footer class="border-t border-gray-200 dark:border-gray-800 py-12 bg-white dark:bg-surface-950">
        <div class="max-w-7xl mx-auto px-6 lg:px-8">
            <div class="grid md:grid-cols-4 gap-8">
                <div>
                    <a href="{{ route('home') }}" class="flex items-center gap-2.5 mb-4">
                        <div class="w-8 h-8 animated-gradient rounded-xl flex items-center justify-center">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                        </div>
                        <span class="text-lg font-bold text-gray-900 dark:text-white">BrainVault</span>
                    </a>
                    <p class="text-sm text-gray-500">Your second brain for the web. Save, organize, and rediscover.</p>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Product</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#features" class="hover:text-gray-700 dark:hover:text-gray-300">Features</a></li>
                        <li><a href="#pricing" class="hover:text-gray-700 dark:hover:text-gray-300">Pricing</a></li>
                        <li><a href="{{ route('extension') }}" class="hover:text-gray-700 dark:hover:text-gray-300">Chrome Extension</a></li>
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">API</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Resources</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Documentation</a></li>
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Blog</a></li>
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Changelog</a></li>
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Support</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="font-semibold text-gray-900 dark:text-white mb-4">Legal</h4>
                    <ul class="space-y-2 text-sm text-gray-500">
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Privacy Policy</a></li>
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Terms of Service</a></li>
                        <li><a href="#" class="hover:text-gray-700 dark:hover:text-gray-300">Cookie Policy</a></li>
                    </ul>
                </div>
            </div>
            <div class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-800 text-center text-sm text-gray-500">
                &copy; {{ date('Y') }} BrainVault. All rights reserved.
            </div>
        </div>
    </footer>
</body>
</html>
