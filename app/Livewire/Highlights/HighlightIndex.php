<?php

namespace App\Livewire\Highlights;

use Illuminate\Support\Facades\Auth;
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

    public function render()
    {
        $highlights = Auth::user()->highlights()
            ->when($this->search, fn ($q) => $q->where('text', 'like', "%{$this->search}%")
                ->orWhere('page_url', 'like', "%{$this->search}%")
                ->orWhere('note', 'like', "%{$this->search}%"))
            ->when($this->colorFilter, fn ($q) => $q->where('color', $this->colorFilter))
            ->with(['bookmark', 'tags'])
            ->latest()
            ->paginate(24);

        return view('livewire.highlights.highlight-index', [
            'highlights' => $highlights,
        ])->layout('layouts.app', ['title' => 'Highlights']);
    }
}
