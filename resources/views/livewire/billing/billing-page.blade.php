<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Billing & Plans') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Manage your subscription and billing') }}</p>
        </div>
        @if($subscription)
            <button wire:click="manageBilling"
                    class="px-4 py-2 bg-gray-100 dark:bg-surface-800 hover:bg-gray-200 dark:hover:bg-surface-700 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-xl transition-colors">
                {{ __('Manage Billing') }}
            </button>
        @endif
    </div>

    @if(request('success'))
        <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
            <p class="text-sm text-green-700 dark:text-green-400 font-medium">{{ __('Subscription activated successfully!') }}</p>
        </div>
    @endif

    {{-- Plans Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl border-2 {{ $currentPlan === $plan['id'] ? 'border-primary-500 shadow-lg shadow-primary-500/10' : 'border-gray-200 dark:border-gray-800' }} overflow-hidden transition-all hover:shadow-lg">
                @if($plan['id'] === 'pro')
                    <div class="absolute top-0 right-0 bg-primary-600 text-white text-[10px] font-bold px-3 py-1 rounded-bl-xl">{{ __('POPULAR') }}</div>
                @endif

                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ $plan['name'] }}</h3>
                    <div class="mt-3 flex items-baseline gap-1">
                        <span class="text-4xl font-extrabold text-gray-900 dark:text-white">${{ $plan['price'] }}</span>
                        @if($plan['price'] > 0)
                            <span class="text-sm text-gray-500">/{{ $plan['period'] }}</span>
                        @endif
                    </div>

                    <ul class="mt-6 space-y-3">
                        @foreach($plan['features'] as $feature)
                            <li class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                {{ $feature }}
                            </li>
                        @endforeach
                    </ul>

                    <div class="mt-6">
                        @if($currentPlan === $plan['id'])
                            <div class="w-full py-2.5 text-center text-sm font-semibold text-primary-600 dark:text-primary-400 bg-primary-50 dark:bg-primary-900/20 rounded-xl">
                                {{ __('Current Plan') }}
                            </div>
                        @elseif($plan['price'] === 0)
                            <div class="w-full py-2.5 text-center text-sm font-medium text-gray-500 bg-gray-100 dark:bg-surface-800 rounded-xl">
                                {{ __('Free Forever') }}
                            </div>
                        @elseif(isset($plan['stripe_price_id']) && $plan['stripe_price_id'])
                            <button wire:click="subscribe('{{ $plan['stripe_price_id'] }}')"
                                    class="w-full py-2.5 text-center text-sm font-semibold text-white bg-primary-600 hover:bg-primary-700 rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                                {{ __('Upgrade to') }} {{ $plan['name'] }}
                            </button>
                        @else
                            <div class="w-full py-2.5 text-center text-sm font-medium text-gray-400 bg-gray-100 dark:bg-surface-800 rounded-xl">
                                {{ __('Coming Soon') }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Current Subscription Details --}}
    @if($subscription)
        <div class="mt-8 bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Subscription Details') }}</h2>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div>
                    <p class="text-xs text-gray-500 mb-1">{{ __('Status') }}</p>
                    <span class="px-2 py-1 text-xs font-medium rounded-lg {{ $subscription->active() ? 'bg-green-100 text-green-700 dark:bg-green-900/20 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/20 dark:text-red-400' }}">
                        {{ $subscription->active() ? __('Active') : __('Inactive') }}
                    </span>
                </div>
                <div>
                    <p class="text-xs text-gray-500 mb-1">{{ __('Plan') }}</p>
                    <p class="text-sm font-medium text-gray-900 dark:text-white capitalize">{{ $currentPlan }}</p>
                </div>
                @if($subscription->onTrial())
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ __('Trial Ends') }}</p>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $subscription->trial_ends_at->format('M d, Y') }}</p>
                    </div>
                @endif
                @if($subscription->ends_at)
                    <div>
                        <p class="text-xs text-gray-500 mb-1">{{ __('Cancels On') }}</p>
                        <p class="text-sm font-medium text-red-600">{{ $subscription->ends_at->format('M d, Y') }}</p>
                    </div>
                @endif
            </div>
        </div>
    @endif
</div>
