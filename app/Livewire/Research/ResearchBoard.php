<?php

namespace App\Livewire\Research;

use App\Models\ResearchProject;
use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class ResearchBoard extends Component
{
    public bool $showProjectModal = false;
    public bool $showTaskModal = false;
    public string $newProjectName = '';
    public string $newProjectDescription = '';
    public string $newTaskTitle = '';
    public string $newTaskDescription = '';
    public ?int $selectedProjectId = null;
    public string $newTaskStatus = 'todo';

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
            'title' => $this->newProjectName,
            'slug' => Str::slug($this->newProjectName) . '-' . Str::random(4),
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

    public function render()
    {
        $projects = Auth::user()->researchProjects()
            ->with(['tasks' => fn ($q) => $q->orderBy('priority')])
            ->orderByDesc('created_at')
            ->get();

        return view('livewire.research.research-board', [
            'projects' => $projects,
        ])->layout('layouts.app', ['title' => 'Research']);
    }
}
