<?php

namespace App\Livewire\Research;

use App\Models\ResearchProject;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ResearchBoard extends Component
{
    use WithPagination;
    public bool $showProjectModal = false;
    public bool $showTaskModal = false;
    public bool $showHighlightModal = false;
    public bool $showNoteModal = false;
    public string $newProjectName = '';
    public string $newProjectDescription = '';
    public string $newTaskTitle = '';
    public string $newTaskDescription = '';
    public ?int $selectedProjectId = null;
    public string $newTaskStatus = 'todo';

    public ?int $selectedTaskId = null;
    public string $newHighlightText = '';
    public string $newHighlightColor = '#FBBF24';
    public string $newHighlightNote = '';
    public string $newNoteTitle = '';
    public string $newNoteContent = '';

    public function openProjectModal(): void
    {
        $this->showProjectModal = true;
        $this->newProjectName = '';
        $this->newProjectDescription = '';
    }

    public function createProject(): void
    {
        $this->validate([
            'newProjectName' => 'required|string|max:255',
        ]);

        ResearchProject::create([
            'user_id' => Auth::id(),
            'name' => $this->newProjectName,
            'description' => $this->newProjectDescription ?: null,
            'status' => 'active',
        ]);

        $this->showProjectModal = false;
    }

    public function openTaskModal(int $projectId, string $status = 'todo'): void
    {
        $this->selectedProjectId = $projectId;
        $this->newTaskStatus = $status;
        $this->showTaskModal = true;
        $this->newTaskTitle = '';
        $this->newTaskDescription = '';
    }

    public function createTask(): void
    {
        $this->validate([
            'newTaskTitle' => 'required|string|max:255',
        ]);

        Task::create([
            'user_id' => Auth::id(),
            'research_project_id' => $this->selectedProjectId,
            'title' => $this->newTaskTitle,
            'description' => $this->newTaskDescription ?: null,
            'status' => $this->newTaskStatus,
            'priority' => 'medium',
        ]);

        $this->showTaskModal = false;
    }

    public function updateTaskStatus(int $taskId, string $status): void
    {
        Task::where('id', $taskId)
            ->where('user_id', Auth::id())
            ->update([
                'status' => $status,
                'completed_at' => $status === 'done' ? now() : null,
            ]);
    }

    public function deleteTask(int $taskId): void
    {
        Task::where('id', $taskId)->where('user_id', Auth::id())->delete();
    }

    public function deleteProject(int $projectId): void
    {
        ResearchProject::where('id', $projectId)->where('user_id', Auth::id())->delete();
    }

    public function openHighlightModal(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
        $this->showHighlightModal = true;
        $this->reset(['newHighlightText', 'newHighlightNote', 'newHighlightColor']);
        $this->newHighlightColor = '#FBBF24';
    }

    public function createHighlight(): void
    {
        $this->validate([
            'newHighlightText' => 'required|string',
            'newHighlightColor' => 'nullable|string|max:7',
            'newHighlightNote' => 'nullable|string',
        ]);

        if (!Auth::user()->tasks()->where('id', $this->selectedTaskId)->exists()) {
            return;
        }

        Auth::user()->highlights()->create([
            'task_id' => $this->selectedTaskId,
            'text' => $this->newHighlightText,
            'color' => $this->newHighlightColor,
            'note' => $this->newHighlightNote ?: null,
            'page_url' => 'brainvault://task/' . $this->selectedTaskId,
            'start_xpath' => '',
            'start_offset' => 0,
            'end_xpath' => '',
            'end_offset' => 0,
        ]);

        $this->showHighlightModal = false;
    }

    public function removeHighlight(int $highlightId): void
    {
        Auth::user()->highlights()->where('id', $highlightId)->delete();
    }

    public function openNoteModal(int $taskId): void
    {
        $this->selectedTaskId = $taskId;
        $this->showNoteModal = true;
        $this->reset(['newNoteTitle', 'newNoteContent']);
    }

    public function createNote(): void
    {
        $this->validate([
            'newNoteTitle' => 'nullable|string|max:500',
            'newNoteContent' => 'nullable|string',
        ]);

        if (!Auth::user()->tasks()->where('id', $this->selectedTaskId)->exists()) {
            return;
        }

        Auth::user()->notes()->create([
            'task_id' => $this->selectedTaskId,
            'title' => $this->newNoteTitle ?: 'Untitled Note',
            'content' => $this->newNoteContent,
            'content_plain' => strip_tags($this->newNoteContent),
            'note_type' => 'note',
        ]);

        $this->showNoteModal = false;
    }

    public function removeNote(int $noteId): void
    {
        Auth::user()->notes()->where('id', $noteId)->update([
            'is_trashed' => true,
            'trashed_at' => now(),
        ]);
    }

    public function render()
    {
        $projects = Auth::user()->researchProjects()
            ->with(['tasks' => fn ($q) => $q->with(['highlights', 'notes' => fn ($n) => $n->where('is_trashed', false)])->orderBy('priority')])
            ->orderByDesc('created_at')
            ->paginate(6);

        return view('livewire.research.research-board', [
            'projects' => $projects,
        ])->layout('layouts.app', ['title' => 'Research']);
    }
}
