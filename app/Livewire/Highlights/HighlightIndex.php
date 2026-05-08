<?php

namespace App\Livewire\Highlights;

use App\Models\Task;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class HighlightIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $colorFilter = '';

    public bool $showMoveToTaskModal = false;
    public ?int $moveToTaskHighlightId = null;
    public ?int $taskProjectId = null;
    public string $taskStatus = 'todo';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedColorFilter(): void
    {
        $this->resetPage();
    }

    public function deleteHighlight(int $id): void
    {
        Auth::user()->highlights()->findOrFail($id)->delete();
    }

    public function openMoveToTaskModal(int $id): void
    {
        $this->moveToTaskHighlightId = $id;
        $this->taskProjectId = null;
        $this->taskStatus = 'todo';
        $this->showMoveToTaskModal = true;
    }

    public function applyMoveToTask(): void
    {
        if (!$this->taskProjectId || !$this->moveToTaskHighlightId) {
            return;
        }

        $project = Auth::user()->researchProjects()->find($this->taskProjectId);
        if (!$project) {
            return;
        }

        $highlight = Auth::user()->highlights()->findOrFail($this->moveToTaskHighlightId);

        $task = Task::create([
            'user_id' => Auth::id(),
            'research_project_id' => $project->id,
            'title' => Str::limit($highlight->text, 100) ?: 'Highlight',
            'description' => $highlight->text . ($highlight->note ? "\n\nNote: " . $highlight->note : ''),
            'status' => $this->taskStatus,
            'priority' => 'medium',
        ]);

        $highlight->update(['task_id' => $task->id]);

        $this->showMoveToTaskModal = false;
        $this->reset(['moveToTaskHighlightId', 'taskProjectId', 'taskStatus']);
        $this->dispatch('notify', message: 'Highlight moved to task.');
    }

    public function render()
    {
        $highlights = Auth::user()->highlights()
            ->when($this->search, fn ($q) => $q->where('text', 'like', "%{$this->search}%")
                ->orWhere('page_url', 'like', "%{$this->search}%")
                ->orWhere('note', 'like', "%{$this->search}%"))
            ->when($this->colorFilter, fn ($q) => $q->where('color', $this->colorFilter))
            ->with(['bookmark', 'tags', 'task'])
            ->latest()
            ->paginate(24);

        return view('livewire.highlights.highlight-index', [
            'highlights' => $highlights,
            'researchProjects' => Auth::user()->researchProjects()->orderBy('name')->get(),
        ])->layout('layouts.app', ['title' => 'Highlights']);
    }
}
