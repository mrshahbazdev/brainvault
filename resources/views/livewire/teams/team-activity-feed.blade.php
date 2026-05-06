<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Activity Feed') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ $team->name }}</p>
        </div>
    </div>

    <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 overflow-hidden">
        @forelse($activities as $activity)
            <div class="flex items-start gap-3 px-4 py-3 border-b border-gray-100 dark:border-gray-800 last:border-b-0" wire:key="activity-{{ $activity->id }}">
                <div class="w-8 h-8 rounded-full bg-primary-100 dark:bg-primary-900/20 flex items-center justify-center flex-shrink-0 mt-0.5">
                    <span class="text-xs font-bold text-primary-700 dark:text-primary-400">{{ strtoupper(substr($activity->user->name ?? '?', 0, 1)) }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm text-gray-900 dark:text-white">
                        <span class="font-medium">{{ $activity->user->name ?? __('Unknown') }}</span>
                        <span class="text-gray-500">
                            @switch($activity->action)
                                @case('bookmark_added') {{ __('added a bookmark') }} @break
                                @case('bookmark_deleted') {{ __('deleted a bookmark') }} @break
                                @case('collection_created') {{ __('created a collection') }} @break
                                @case('collection_shared') {{ __('shared a collection') }} @break
                                @case('note_added') {{ __('added a note') }} @break
                                @case('member_joined') {{ __('joined the team') }} @break
                                @case('member_invited') {{ __('invited a member') }} @break
                                @default {{ $activity->action }}
                            @endswitch
                        </span>
                    </p>
                    @if($activity->properties)
                        <p class="text-xs text-gray-500 mt-0.5">
                            @if(isset($activity->properties['title']))
                                "{{ Str::limit($activity->properties['title'], 60) }}"
                            @endif
                        </p>
                    @endif
                    <span class="text-xs text-gray-400 mt-1 block">{{ $activity->created_at->diffForHumans() }}</span>
                </div>
            </div>
        @empty
            <div class="py-16 text-center">
                <p class="text-sm text-gray-500">{{ __('No activity yet.') }}</p>
            </div>
        @endforelse
    </div>

    <div class="mt-6">{{ $activities->links() }}</div>
</div>
