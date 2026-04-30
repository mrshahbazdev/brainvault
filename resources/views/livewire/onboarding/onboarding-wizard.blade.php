<div class="min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-2xl">
        {{-- Progress Bar --}}
        <div class="mb-8">
            <div class="flex items-center justify-between mb-2">
                <span class="text-sm text-gray-500 dark:text-gray-400">Step {{ $step }} of {{ $totalSteps }}</span>
                <button wire:click="skip" class="text-sm text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">Skip setup</button>
            </div>
            <div class="h-2 bg-gray-200 dark:bg-surface-800 rounded-full overflow-hidden">
                <div class="h-full bg-primary-600 rounded-full transition-all duration-500" style="width: {{ ($step / $totalSteps) * 100 }}%"></div>
            </div>
        </div>

        <div class="bg-white dark:bg-surface-900 rounded-3xl border border-gray-200 dark:border-gray-800 shadow-xl p-8">
            {{-- Step 1: Profile --}}
            @if($step === 1)
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-primary-100 dark:bg-primary-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" /></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Welcome to BrainVault!</h2>
                    <p class="text-gray-500 mt-2">Let's set up your profile</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Your Name</label>
                        <input wire:model="name" type="text" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-surface-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 focus:border-transparent" placeholder="Enter your name">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Timezone</label>
                        <select wire:model="timezone" class="w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-surface-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="UTC">UTC</option>
                            <option value="America/New_York">Eastern Time (US)</option>
                            <option value="America/Chicago">Central Time (US)</option>
                            <option value="America/Denver">Mountain Time (US)</option>
                            <option value="America/Los_Angeles">Pacific Time (US)</option>
                            <option value="Europe/London">London</option>
                            <option value="Europe/Berlin">Berlin</option>
                            <option value="Asia/Karachi">Pakistan (PKT)</option>
                            <option value="Asia/Kolkata">India (IST)</option>
                            <option value="Asia/Tokyo">Tokyo</option>
                            <option value="Australia/Sydney">Sydney</option>
                        </select>
                    </div>
                </div>
            @endif

            {{-- Step 2: Theme --}}
            @if($step === 2)
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-purple-100 dark:bg-purple-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01" /></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Choose Your Theme</h2>
                    <p class="text-gray-500 mt-2">Pick your preferred appearance</p>
                </div>

                <div class="grid grid-cols-3 gap-4">
                    @foreach(['light' => 'Light', 'dark' => 'Dark', 'system' => 'System'] as $value => $label)
                        <button wire:click="$set('theme', '{{ $value }}')"
                                class="p-4 rounded-2xl border-2 transition-all {{ $theme === $value ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                            <div class="w-12 h-12 mx-auto mb-2 rounded-xl {{ $value === 'light' ? 'bg-white border border-gray-200' : ($value === 'dark' ? 'bg-gray-900 border border-gray-700' : 'bg-gradient-to-br from-white to-gray-900') }}"></div>
                            <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $label }}</p>
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Step 3: Interests --}}
            @if($step === 3)
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" /></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Your Interests</h2>
                    <p class="text-gray-500 mt-2">Help us personalize your experience</p>
                </div>

                <div class="flex flex-wrap gap-3 justify-center">
                    @foreach($availableInterests as $key => $label)
                        <button wire:click="toggleInterest('{{ $key }}')"
                                class="px-4 py-2 rounded-xl text-sm font-medium transition-all {{ in_array($key, $interests) ? 'bg-primary-600 text-white shadow-lg shadow-primary-500/25' : 'bg-gray-100 dark:bg-surface-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-surface-700' }}">
                            {{ $label }}
                        </button>
                    @endforeach
                </div>
            @endif

            {{-- Step 4: Chrome Extension --}}
            @if($step === 4)
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-blue-100 dark:bg-blue-900/30 rounded-2xl flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">Install Chrome Extension</h2>
                    <p class="text-gray-500 mt-2">Save bookmarks from any webpage with one click</p>
                </div>

                <div class="bg-gray-50 dark:bg-surface-800 rounded-2xl p-6 text-center">
                    <div class="space-y-4">
                        <div class="flex items-center gap-3 text-left">
                            <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 font-bold text-sm">1</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Click the button below to install</p>
                        </div>
                        <div class="flex items-center gap-3 text-left">
                            <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 font-bold text-sm">2</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Pin the extension to your toolbar</p>
                        </div>
                        <div class="flex items-center gap-3 text-left">
                            <div class="w-8 h-8 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center flex-shrink-0">
                                <span class="text-primary-600 font-bold text-sm">3</span>
                            </div>
                            <p class="text-sm text-gray-700 dark:text-gray-300">Start saving bookmarks from any page!</p>
                        </div>
                    </div>

                    <button class="mt-6 px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-lg transition-all">
                        Install Chrome Extension
                    </button>
                    <p class="text-xs text-gray-400 mt-2">You can always install it later from Settings</p>
                </div>
            @endif

            {{-- Navigation Buttons --}}
            <div class="flex items-center justify-between mt-8">
                @if($step > 1)
                    <button wire:click="previousStep" class="px-5 py-2.5 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                        Back
                    </button>
                @else
                    <div></div>
                @endif

                @if($step < $totalSteps)
                    <button wire:click="nextStep" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                        Continue
                    </button>
                @else
                    <button wire:click="complete" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                        Get Started
                    </button>
                @endif
            </div>
        </div>
    </div>
</div>
