<?php

namespace App\Livewire\Tags;

use App\Models\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class TagManagement extends Component
{
    public string $search = '';
    public array $selected = [];
    public bool $selectAll = false;

    public bool $showRenameModal = false;
    public bool $showMergeModal = false;
    public ?int $renamingId = null;
    public string $newName = '';
    public ?int $mergeTargetId = null;

    public function updatedSearch(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function toggleSelectAll(): void
    {
        $this->selectAll = !$this->selectAll;
        if ($this->selectAll) {
            $this->selected = $this->getTagsQuery()->pluck('id')->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function openRenameModal(int $id): void
    {
        $tag = Auth::user()->tags()->findOrFail($id);
        $this->renamingId = $id;
        $this->newName = $tag->name;
        $this->showRenameModal = true;
    }

    public function renameTag(): void
    {
        $this->validate(['newName' => 'required|string|max:100']);

        $tag = Auth::user()->tags()->findOrFail($this->renamingId);
        $tag->update([
            'name' => $this->newName,
            'slug' => Str::slug($this->newName),
        ]);

        $this->showRenameModal = false;
    }

    public function openMergeModal(): void
    {
        if (count($this->selected) < 2) {
            return;
        }
        $this->mergeTargetId = null;
        $this->showMergeModal = true;
    }

    public function mergeTags(): void
    {
        if (!$this->mergeTargetId || count($this->selected) < 2) {
            return;
        }

        $targetTag = Auth::user()->tags()->findOrFail($this->mergeTargetId);
        $sourceIds = array_filter($this->selected, fn ($id) => $id != $this->mergeTargetId);

        DB::transaction(function () use ($targetTag, $sourceIds) {
            // Move all taggable relationships from source tags to target
            $existingRelations = DB::table('taggables')
                ->where('tag_id', $targetTag->id)
                ->get(['taggable_id', 'taggable_type'])
                ->map(fn ($r) => $r->taggable_type . ':' . $r->taggable_id)
                ->toArray();

            $toMove = DB::table('taggables')
                ->whereIn('tag_id', $sourceIds)
                ->get();

            foreach ($toMove as $rel) {
                $key = $rel->taggable_type . ':' . $rel->taggable_id;
                if (!in_array($key, $existingRelations)) {
                    DB::table('taggables')->insert([
                        'tag_id' => $targetTag->id,
                        'taggable_id' => $rel->taggable_id,
                        'taggable_type' => $rel->taggable_type,
                        'created_at' => now(),
                    ]);
                }
            }

            // Delete source taggables and tags
            DB::table('taggables')->whereIn('tag_id', $sourceIds)->delete();
            Tag::whereIn('id', $sourceIds)->where('user_id', Auth::id())->delete();

            // Update usage count
            $count = DB::table('taggables')->where('tag_id', $targetTag->id)->count();
            $targetTag->update(['usage_count' => $count]);
        });

        $this->showMergeModal = false;
        $this->selected = [];
        $this->selectAll = false;
    }

    public function bulkDelete(): void
    {
        DB::table('taggables')->whereIn('tag_id', $this->selected)->delete();
        Auth::user()->tags()->whereIn('id', $this->selected)->delete();
        $this->selected = [];
        $this->selectAll = false;
    }

    public function deleteTag(int $id): void
    {
        DB::table('taggables')->where('tag_id', $id)->delete();
        Auth::user()->tags()->where('id', $id)->delete();
    }

    protected function getTagsQuery()
    {
        return Auth::user()->tags()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount(['bookmarks', 'notes', 'highlights']);
    }

    public function render()
    {
        return view('livewire.tags.tag-management', [
            'tags' => $this->getTagsQuery()
                ->orderByDesc('usage_count')
                ->get(),
        ])->layout('layouts.app', ['title' => 'Tag Management']);
    }
}
