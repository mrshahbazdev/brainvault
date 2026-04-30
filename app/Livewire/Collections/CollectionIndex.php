<?php

namespace App\Livewire\Collections;

use App\Models\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;

class CollectionIndex extends Component
{
    public bool $showCreateModal = false;
    public bool $showEditModal = false;

    public string $name = '';
    public string $description = '';
    public string $color = '#6366F1';
    public string $icon = 'folder';
    public ?int $parentId = null;
    public ?int $editingId = null;

    protected array $colors = [
        '#6366F1', '#8B5CF6', '#EC4899', '#EF4444', '#F97316',
        '#EAB308', '#22C55E', '#14B8A6', '#06B6D4', '#3B82F6',
    ];

    public function openCreateModal(?int $parentId = null): void
    {
        $this->reset(['name', 'description', 'editingId']);
        $this->color = '#6366F1';
        $this->icon = 'folder';
        $this->parentId = $parentId;
        $this->showCreateModal = true;
    }

    public function createCollection(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        Auth::user()->collections()->create([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
            'parent_id' => $this->parentId,
        ]);

        $this->showCreateModal = false;
        $this->reset(['name', 'description', 'parentId']);
    }

    public function editCollection(int $id): void
    {
        $collection = Auth::user()->collections()->findOrFail($id);
        $this->editingId = $id;
        $this->name = $collection->name;
        $this->description = $collection->description ?? '';
        $this->color = $collection->color ?? '#6366F1';
        $this->icon = $collection->icon ?? 'folder';
        $this->showEditModal = true;
    }

    public function updateCollection(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'color' => ['required', 'string', 'max:7'],
        ]);

        $collection = Auth::user()->collections()->findOrFail($this->editingId);
        $collection->update([
            'name' => $this->name,
            'slug' => Str::slug($this->name),
            'description' => $this->description,
            'color' => $this->color,
            'icon' => $this->icon,
        ]);

        $this->showEditModal = false;
    }

    public function deleteCollection(int $id): void
    {
        Auth::user()->collections()->findOrFail($id)->delete();
    }

    public function render()
    {
        return view('livewire.collections.collection-index', [
            'collections' => Auth::user()->collections()
                ->whereNull('parent_id')
                ->withCount('bookmarks')
                ->with(['children' => fn ($q) => $q->withCount('bookmarks')])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get(),
            'colors' => $this->colors,
        ]);
    }
}
