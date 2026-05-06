<div class="relative" x-data="{ open: @entangle('showDropdown') }">
    {{-- Bell Button --}}
    <button @click="open = !open" class="relative p-2 rounded-xl hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors">
        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
        @if($unreadCount > 0)
            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
        @endif
    </button>

    {{-- Dropdown --}}
    <div x-show="open" @click.outside="open = false" x-transition
         class="absolute right-0 top-full mt-2 w-80 bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-2xl z-50 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 dark:border-gray-800">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white">{{ __('Notifications') }}</h3>
            @if($unreadCount > 0)
                <button wire:click="markAllRead" class="text-xs text-primary-600 hover:text-primary-700 font-medium">{{ __('Mark all read') }}</button>
            @endif
        </div>

        <div class="max-h-80 overflow-y-auto">
            @forelse($notifications as $notification)
                <div wire:click="markAsRead('{{ $notification->id }}')"
                     class="px-4 py-3 border-b border-gray-100 dark:border-gray-800 hover:bg-gray-50 dark:hover:bg-surface-800 cursor-pointer transition-colors {{ $notification->read_at ? 'opacity-60' : '' }}">
                    <div class="flex items-start gap-3">
                        @if(!$notification->read_at)
                            <span class="w-2 h-2 mt-1.5 bg-primary-500 rounded-full flex-shrink-0"></span>
                        @else
                            <span class="w-2 h-2 mt-1.5 flex-shrink-0"></span>
                        @endif
                        <div class="min-w-0">
                            <p class="text-sm text-gray-900 dark:text-white">{{ $notification->data['message'] ?? __('Notification') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $notification->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-4 py-8 text-center">
                    <svg class="mx-auto w-10 h-10 text-gray-300 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    <p class="mt-2 text-sm text-gray-500">{{ __('No notifications yet') }}</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
