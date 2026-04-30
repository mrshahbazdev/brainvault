<div>
    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Teams</h1>
            <p class="text-sm text-gray-500 mt-1">Collaborate with others on shared knowledge</p>
        </div>
        <button wire:click="openCreateModal" class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            Create Team
        </button>
    </div>

    {{-- Teams Grid --}}
    @if($ownedTeams->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
            @foreach($ownedTeams as $team)
                <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-5">
                    <div class="flex items-start justify-between mb-3">
                        <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-xl flex items-center justify-center">
                            <svg class="w-5 h-5 text-primary-600 dark:text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
                        </div>
                        <div class="flex items-center gap-1">
                            <button wire:click="openInviteModal({{ $team->id }})" class="p-1.5 text-gray-400 hover:text-primary-600 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" /></svg>
                            </button>
                            <button wire:click="deleteTeam({{ $team->id }})" wire:confirm="Delete this team?" class="p-1.5 text-gray-400 hover:text-red-600 rounded-lg hover:bg-gray-100 dark:hover:bg-surface-800 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white">{{ $team->name }}</h3>
                    @if($team->description)
                        <p class="text-sm text-gray-500 mt-1 line-clamp-2">{{ $team->description }}</p>
                    @endif
                    <div class="flex items-center gap-2 mt-3">
                        <div class="flex -space-x-1.5">
                            @foreach($team->members->take(4) as $member)
                                <div class="w-6 h-6 rounded-full bg-primary-500 border-2 border-white dark:border-surface-900 flex items-center justify-center text-white text-[10px] font-semibold">
                                    {{ substr($member->name, 0, 1) }}
                                </div>
                            @endforeach
                        </div>
                        <span class="text-xs text-gray-500">{{ $team->members->count() }} {{ Str::plural('member', $team->members->count()) }}</span>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8 text-center">
            <div class="w-14 h-14 bg-gray-100 dark:bg-surface-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" /></svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">Create your first team</h2>
            <p class="text-sm text-gray-500 mb-4">Invite colleagues to collaborate on shared bookmarks and notes.</p>
            <button wire:click="openCreateModal" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                Create Team
            </button>
        </div>
    @endif

    {{-- Create Team Modal --}}
    @if($showCreateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="$set('showCreateModal', false)">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Create Team</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Team Name</label>
                        <input wire:model="newName" type="text" placeholder="Engineering Team" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Description</label>
                        <textarea wire:model="newDescription" rows="3" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button wire:click="$set('showCreateModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">Cancel</button>
                    <button wire:click="createTeam" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">Create</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Invite Modal --}}
    @if($showInviteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="$set('showInviteModal', false)">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Invite Member</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Email</label>
                        <input wire:model="inviteEmail" type="email" placeholder="colleague@example.com" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Role</label>
                        <select wire:model="inviteRole" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="member">Member - can view</option>
                            <option value="editor">Editor - can add & edit</option>
                            <option value="admin">Admin - full access</option>
                        </select>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button wire:click="$set('showInviteModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">Cancel</button>
                    <button wire:click="sendInvite" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">Send Invite</button>
                </div>
            </div>
        </div>
    @endif
</div>
