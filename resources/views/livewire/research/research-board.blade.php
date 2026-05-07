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

    {{-- Bulk Actions Bar --}}
    @if(count($selectedTasks) > 0)
        <div class="bg-white dark:bg-surface-900 rounded-2xl border border-gray-200 dark:border-gray-800 p-4 mb-6">
            <div class="flex flex-wrap items-center gap-3">
                <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($selectedTasks) }} {{ __('selected') }}</span>
                <button wire:click="bulkFavorite" class="px-3 py-1.5 text-xs font-medium bg-amber-100 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400 rounded-lg hover:bg-amber-200 dark:hover:bg-amber-900/30 transition-colors">{{ __('Favorite') }}</button>
                <button wire:click="bulkArchive" class="px-3 py-1.5 text-xs font-medium bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-400 rounded-lg hover:bg-blue-200 dark:hover:bg-blue-900/30 transition-colors">{{ __('Archive') }}</button>
                <button wire:click="bulkReadLater" class="px-3 py-1.5 text-xs font-medium bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-400 rounded-lg hover:bg-green-200 dark:hover:bg-green-900/30 transition-colors">{{ __('Read Later') }}</button>
                <button wire:click="openBulkTagModal" class="px-3 py-1.5 text-xs font-medium bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-400 rounded-lg hover:bg-purple-200 dark:hover:bg-purple-900/30 transition-colors">{{ __('Tag') }}</button>
                <button wire:click="openBulkMoveModal" class="px-3 py-1.5 text-xs font-medium bg-indigo-100 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-400 rounded-lg hover:bg-indigo-200 dark:hover:bg-indigo-900/30 transition-colors">{{ __('Move to Collection') }}</button>
                <button wire:click="openBulkAddToTaskModal" class="px-3 py-1.5 text-xs font-medium bg-teal-100 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 rounded-lg hover:bg-teal-200 dark:hover:bg-teal-900/30 transition-colors">{{ __('Add to Task') }}</button>
                <button wire:click="bulkTrash" wire:confirm="{{ __('Move linked bookmarks to trash?') }}" class="px-3 py-1.5 text-xs font-medium bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-400 rounded-lg hover:bg-red-200 dark:hover:bg-red-900/30 transition-colors">{{ __('Trash') }}</button>
            </div>
        </div>
    @endif

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
                                        <div class="flex items-start gap-2">
                                            <input type="checkbox" wire:model.live="selectedTasks" value="{{ $task->id }}" class="w-4 h-4 mt-0.5 rounded border-gray-300 dark:border-gray-600 text-primary-600 focus:ring-primary-500">
                                            <h4 class="text-sm font-medium text-gray-900 dark:text-white">{{ $task->title }}</h4>
                                        </div>
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

                                    {{-- Highlights attached to this task --}}
                                    @if($task->highlights->count())
                                        <div class="mt-2 space-y-1">
                                            @foreach($task->highlights as $highlight)
                                                <div class="flex items-start gap-1.5 px-2 py-1 rounded-lg bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200/60 dark:border-yellow-800/40">
                                                    <span class="mt-0.5 w-2 h-2 rounded-full shrink-0" style="background-color: {{ $highlight->color }}"></span>
                                                    <p class="text-[11px] text-gray-700 dark:text-gray-300 leading-snug flex-1">{{ Str::limit($highlight->text, 80) }}</p>
                                                    <button wire:click="removeHighlight({{ $highlight->id }})" class="p-0.5 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Notes attached to this task --}}
                                    @if($task->notes->count())
                                        <div class="mt-2 space-y-1">
                                            @foreach($task->notes as $note)
                                                <div class="flex items-start gap-1.5 px-2 py-1 rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200/60 dark:border-blue-800/40">
                                                    <svg class="w-3 h-3 mt-0.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                    <div class="flex-1 min-w-0">
                                                        <p class="text-[11px] font-medium text-gray-700 dark:text-gray-300 truncate">{{ $note->title }}</p>
                                                        @if($note->content_plain)
                                                            <p class="text-[10px] text-gray-500 truncate">{{ Str::limit($note->content_plain, 60) }}</p>
                                                        @endif
                                                    </div>
                                                    <button wire:click="removeNote({{ $note->id }})" class="p-0.5 text-gray-400 hover:text-red-500 opacity-0 group-hover:opacity-100 transition-opacity shrink-0">
                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <div class="flex items-center gap-2 mt-2">
                                        <span class="px-1.5 py-0.5 text-[10px] font-medium rounded {{ $task->priority === 'high' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : ($task->priority === 'medium' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : 'bg-gray-100 text-gray-500 dark:bg-surface-800 dark:text-gray-500') }}">
                                            {{ ucfirst($task->priority ?? 'normal') }}
                                        </span>

                                        {{-- Add highlight/note buttons --}}
                                        <div class="flex items-center gap-1 ml-auto opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button wire:click="openHighlightModal({{ $task->id }})" class="flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-medium text-yellow-600 dark:text-yellow-400 hover:bg-yellow-100 dark:hover:bg-yellow-900/30 rounded transition-colors" title="{{ __('Add Highlight') }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                @if($task->highlights->count())
                                                    <span>{{ $task->highlights->count() }}</span>
                                                @endif
                                            </button>
                                            <button wire:click="openNoteModal({{ $task->id }})" class="flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-medium text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/30 rounded transition-colors" title="{{ __('Add Note') }}">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                                @if($task->notes->count())
                                                    <span>{{ $task->notes->count() }}</span>
                                                @endif
                                            </button>
                                        </div>
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

    {{-- Pagination --}}
    @if($projects->hasPages())
        <div class="mt-6">
            {{ $projects->links() }}
        </div>
    @endif

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

    {{-- Add Highlight to Task Modal --}}
    @if($showHighlightModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="$set('showHighlightModal', false)">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        {{ __('Add Highlight') }}
                    </span>
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Highlighted Text') }}</label>
                        <textarea wire:model="newHighlightText" rows="3" placeholder="{{ __('Paste or type the highlighted text...') }}" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Color') }}</label>
                        <div class="flex items-center gap-2">
                            @foreach(['#FBBF24', '#F87171', '#34D399', '#60A5FA', '#A78BFA', '#FB923C'] as $color)
                                <button wire:click="$set('newHighlightColor', '{{ $color }}')" class="w-7 h-7 rounded-full border-2 transition-all {{ $newHighlightColor === $color ? 'border-gray-900 dark:border-white scale-110' : 'border-transparent hover:scale-105' }}" style="background-color: {{ $color }}"></button>
                            @endforeach
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Note') }} <span class="text-gray-400 font-normal">({{ __('optional') }})</span></label>
                        <textarea wire:model="newHighlightNote" rows="2" placeholder="{{ __('Add a note about this highlight...') }}" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button wire:click="$set('showHighlightModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                    <button wire:click="createHighlight" class="px-6 py-2.5 bg-yellow-500 hover:bg-yellow-600 text-white text-sm font-semibold rounded-xl transition-colors">{{ __('Add Highlight') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Add Note to Task Modal --}}
    @if($showNoteModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" wire:click.self="$set('showNoteModal', false)">
            <div class="bg-white dark:bg-surface-900 rounded-2xl shadow-xl w-full max-w-md p-6 mx-4">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    <span class="inline-flex items-center gap-2">
                        <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                        {{ __('Add Note') }}
                    </span>
                </h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Title') }}</label>
                        <input wire:model="newNoteTitle" type="text" placeholder="{{ __('Note title...') }}" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Content') }}</label>
                        <textarea wire:model="newNoteContent" rows="4" placeholder="{{ __('Write your note...') }}" class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 resize-none"></textarea>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-3 mt-6">
                    <button wire:click="$set('showNoteModal', false)" class="px-4 py-2.5 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl transition-colors">{{ __('Cancel') }}</button>
                    <button wire:click="createNote" class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors">{{ __('Add Note') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Bulk Tag Modal --}}
    @if($showBulkTagModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showBulkTagModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Add Tag to Selected') }}</h2>
                <input wire:model="bulkTagName" type="text" placeholder="{{ __('Tag name') }}"
                       class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-primary-500 mb-4">
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showBulkTagModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="applyBulkTag" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl">{{ __('Apply Tag') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Bulk Move to Collection Modal --}}
    @if($showBulkMoveModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showBulkMoveModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Move to Collection') }}</h2>
                <select wire:model="bulkMoveCollectionId"
                        class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500 mb-4">
                    <option value="">{{ __('Select collection') }}</option>
                    @foreach($collections as $collection)
                        <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                    @endforeach
                </select>
                <div class="flex justify-end gap-3">
                    <button wire:click="$set('showBulkMoveModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="applyBulkMove" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl">{{ __('Move') }}</button>
                </div>
            </div>
        </div>
    @endif

    {{-- Bulk Add to Task Modal --}}
    @if($showBulkAddToTaskModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" wire:click="$set('showBulkAddToTaskModal', false)"></div>
            <div class="relative bg-white dark:bg-surface-900 rounded-2xl shadow-2xl border border-gray-200 dark:border-gray-800 w-full max-w-sm p-6">
                <h2 class="text-lg font-bold text-gray-900 dark:text-white mb-4">{{ __('Add to Task') }}</h2>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Research Project') }}</label>
                        <select wire:model="bulkTaskProjectId"
                                class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="">{{ __('Select project') }}</option>
                            @foreach($researchProjects as $rProject)
                                <option value="{{ $rProject->id }}">{{ $rProject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">{{ __('Task Status') }}</label>
                        <select wire:model="bulkTaskStatus"
                                class="w-full px-4 py-2.5 bg-gray-100 dark:bg-surface-800 border-0 rounded-xl text-sm text-gray-900 dark:text-white focus:ring-2 focus:ring-primary-500">
                            <option value="todo">{{ __('To Do') }}</option>
                            <option value="in_progress">{{ __('In Progress') }}</option>
                            <option value="done">{{ __('Done') }}</option>
                        </select>
                    </div>
                </div>
                <div class="flex justify-end gap-3 mt-6">
                    <button wire:click="$set('showBulkAddToTaskModal', false)" class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-surface-800 rounded-xl">{{ __('Cancel') }}</button>
                    <button wire:click="applyBulkAddToTask" class="px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-xl">{{ __('Add to Task') }}</button>
                </div>
            </div>
        </div>
    @endif
</div>
