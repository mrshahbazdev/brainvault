<?php

namespace App\Livewire\Notes;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class NoteIndex extends Component
{
    use WithPagination;

    #[Url]
    public string $search = '';

    #[Url]
    public string $filter = 'all';

    public bool $showCreateModal = false;
    public string $newTitle = '';
    public string $newContent = '';
    public string $newColor = '';
    public string $noteType = 'note';

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function openCreateModal(): void
    {
        $this->reset(['newTitle', 'newContent', 'newColor', 'noteType']);
        $this->showCreateModal = true;
    }

    public function createNote(): void
    {
        $this->validate([
            'newTitle' => ['nullable', 'string', 'max:500'],
            'newContent' => ['nullable', 'string'],
        ]);

        Auth::user()->notes()->create([
            'title' => $this->newTitle ?: 'Untitled Note',
            'content' => $this->newContent,
            'content_plain' => strip_tags($this->newContent),
            'note_type' => $this->noteType,
            'color' => $this->newColor ?: null,
        ]);

        $this->showCreateModal = false;
        $this->reset(['newTitle', 'newContent', 'newColor', 'noteType']);
    }

    public function togglePin(int $id): void
    {
        $note = Auth::user()->notes()->findOrFail($id);
        $note->update(['is_pinned' => !$note->is_pinned]);
    }

    public function trashNote(int $id): void
    {
        Auth::user()->notes()->findOrFail($id)->update([
            'is_trashed' => true,
            'trashed_at' => now(),
        ]);
    }

    public function restoreNote(int $id): void
    {
        Auth::user()->notes()->findOrFail($id)->update([
            'is_trashed' => false,
            'trashed_at' => null,
        ]);
    }

    public function permanentDelete(int $id): void
    {
        Auth::user()->notes()->findOrFail($id)->delete();
    }

    public function render()
    {
        $notes = Auth::user()->notes()
            ->when($this->search, fn ($q) => $q->where('title', 'ilike', "%{$this->search}%")->orWhere('content_plain', 'ilike', "%{$this->search}%"))
            ->when($this->filter === 'all', fn ($q) => $q->where('is_trashed', false))
            ->when($this->filter === 'pinned', fn ($q) => $q->where('is_pinned', true)->where('is_trashed', false))
            ->when($this->filter === 'archived', fn ($q) => $q->where('is_archived', true)->where('is_trashed', false))
            ->when($this->filter === 'trash', fn ($q) => $q->where('is_trashed', true))
            ->with('tags')
            ->latest()
            ->paginate(24);

        return view('livewire.notes.note-index', [
            'notes' => $notes,
        ]);
    }
}
