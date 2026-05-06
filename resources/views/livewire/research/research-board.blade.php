<div>
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ __('Research Projects') }}</h1>
            <p class="text-sm text-gray-500 mt-1">{{ __('Organize your research with Kanban boards') }}</p>
        </div>
        <button wire:click="openProjectModal" class="flex items-center gap-2 px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
            {{ __('New Project') }}
        </button>
    </div>

    @forelse($projects as $project)
        <div class="mb-8">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $project->title }}</h2>
                <button wire:click="deleteProject({{ $project->id }})" wire:confirm="{{ __('Delete this project and all tasks?') }}" class="text-gray-400 hover:text-red-600 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 overflow-x-auto">
                @foreach(['todo' => __('To Do'), 'in_progress' => __('In Progress'), 'done' => __('Done')] as $status => $label)
                    <div class="bg-gray-50 dark:bg-surface-800/50 rounded-2xl p-4 min-h-[200px]">
                        <div class="flex items-center justify-between mb-3">
                            <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">{{ $label }}</h3>
                            <span class="px-2 py-0.5 text-[10px] font-semibold bg-gray-200 dark:bg-surface-700 text-gray-600 dark:text-gray-400 rounded-full">
                                {{ $project->tasks->where('status', $status)->count() }}
                            </span>
                        </div>

                        <div class="space-y-2">
                            @foreach($project->tasks->where('status', $status) as $task)
                                <div class="bg-white dark:bg-surface-900 rounded-xl p-3 border border-gray-200 dark:border-gray-800 group">
                                    <div class="flex items-start justify-between">
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</h4>
                                        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                            @if($status !== 'done')
                                                <button wire:click="updateTaskStatus({{ $task->id }}, '{{ $status === 'todo' ? 'in_progress' : 'done' }}')" class="p-1 text-gray-400 hover:text-green-600 rounded">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                                                </button>
                                            @endif
                                            <button wire:click="deleteTask({{ $task->id }})" class="p-1 text-gray-400 hover:text-red-600 rounded">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                            </button>
                                        </div>
                                    </div>
                                    @if($task->description)
                                        <p class="text-xs text-gray-500 mt-1">{{ Str::limit($task->description, 100) }}</p>
                                    @endif
                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-500 dark:bg-surface-800 dark:text-gray-500') }}">
                                            {{ ucfirst($task->priority ?? 'normal') }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <button wire:click="openTaskModal({{ $project->id }}, '{{ $status }}')" class="w-full mt-2 py-2 text-xs font-medium text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-700 rounded-xl transition-colors">
                            {{ __('+ Add Task') }}
                        </button>
                    </div>
                @endforeach
            </div>
        </div>
    @empty
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-8 text-center">
            <div class="w-14 h-14 bg-gray-100 dark:bg-surface-800 rounded-2xl flex items-center justify-center mx-auto mb-4">
                <svg class="w-7 h-7 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" /></svg>
            </div>
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">{{ __('Start Researching') }}</h2>
            <p class="text-sm text-gray-500 mb-4">{{ __('Create a project to organize tasks and link bookmarks to your research.') }}</p>
            <button wire:click="openProjectModal" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl shadow-lg shadow-primary-500/25 transition-all">
                {{ __('Create First Project') }}
            </button>
        </div>
    @endforelse

    {{-- Create Project Modal --}}
    @if($showProjectModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="$set('showProjectModal', false)">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('New Research Project') }}</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Project Name') }}</label>
                        <input wire:model="newProjectName" type="text" placeholder="{{ __('e.g., Machine Learning Survey') }}" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
                        <textarea wire:model="newProjectDescription" rows="3" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button wire:click="$set('showProjectModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                    <button wire:click="createProject" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">{{ __('Create') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Create Task Modal --}}
    @if($showTaskModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="$set('showTaskModal', false)">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">{{ __('Add Task') }}</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Task Title') }}</label>
                        <input wire:model="newTaskTitle" type="text" placeholder="{{ __('What needs to be done?') }}" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Description') }}</label>
                        <textarea wire:model="newTaskDescription" rows="3" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button wire:click="$set('showTaskModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                    <button wire:click="createTask" class="px-6 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl transition-colors">{{ __('Add Task') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
