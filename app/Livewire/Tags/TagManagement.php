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

    // Highlight modal
    public bool $showHighlightModal = false;
    public ?int $highlightTagId = null;
    public string $newHighlightText = '';
    public string $newHighlightColor = '#FBBF24';
    public string $newHighlightNote = '';

    // Note modal
    public bool $showNoteModal = false;
    public ?int $noteTagId = null;
    public string $newNoteTitle = '';
    public string $newNoteContent = '';

    // Expanded tag to show details
    public ?int $expandedTagId = null;

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

    public function toggleExpand(int $id): void
    {
        $this->expandedTagId = $this->expandedTagId === $id ? null : $id;
    }

    public function openHighlightModal(int $tagId): void
    {
        $this->highlightTagId = $tagId;
        $this->reset(['newHighlightText', 'newHighlightColor', 'newHighlightNote']);
        $this->newHighlightColor = '#FBBF24';
        $this->showHighlightModal = true;
    }

    public function createHighlight(): void
    {
        $this->validate([
            'newHighlightText' => 'required|string',
            'newHighlightColor' => 'nullable|string|max:7',
            'newHighlightNote' => 'nullable|string',
        ]);

        $tag = Auth::user()->tags()->findOrFail($this->highlightTagId);

        $highlight = Auth::user()->highlights()->create([
            'text' => $this->newHighlightText,
            'color' => $this->newHighlightColor,
            'note' => $this->newHighlightNote ?: null,
            'page_url' => 'brainvault://tag/' . $tag->id,
            'start_xpath' => '',
            'start_offset' => 0,
            'end_xpath' => '',
            'end_offset' => 0,
        ]);

        $tag->highlights()->attach($highlight->id);

        $this->showHighlightModal = false;
        $this->dispatch('notify', message: __('Highlight added to tag.'));
    }

    public function removeHighlight(int $tagId, int $highlightId): void
    {
        $tag = Auth::user()->tags()->findOrFail($tagId);
        $tag->highlights()->detach($highlightId);
        $this->dispatch('notify', message: __('Highlight removed from tag.'));
    }

    public function openNoteModal(int $tagId): void
    {
        $this->noteTagId = $tagId;
        $this->reset(['newNoteTitle', 'newNoteContent']);
        $this->showNoteModal = true;
    }

    public function createNote(): void
    {
        $this->validate([
            'newNoteTitle' => 'nullable|string|max:500',
            'newNoteContent' => 'nullable|string',
        ]);

        $tag = Auth::user()->tags()->findOrFail($this->noteTagId);

        $note = Auth::user()->notes()->create([
            'title' => $this->newNoteTitle ?: 'Untitled Note',
            'content' => $this->newNoteContent,
            'content_plain' => strip_tags($this->newNoteContent),
            'note_type' => 'note',
        ]);

        $tag->notes()->attach($note->id);

        $this->showNoteModal = false;
        $this->dispatch('notify', message: __('Note added to tag.'));
    }

    public function removeNote(int $tagId, int $noteId): void
    {
        $tag = Auth::user()->tags()->findOrFail($tagId);
        $tag->notes()->detach($noteId);
        $this->dispatch('notify', message: __('Note removed from tag.'));
    }

    protected function getTagsQuery()
    {
        return Auth::user()->tags()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount(['bookmarks', 'notes', 'highlights']);
    }

    public function render()
    {
        $query = $this->getTagsQuery();

        if ($this->expandedTagId) {
            $query->with([
                'highlights' => fn ($q) => $q->latest()->limit(20),
                'notes' => fn ($q) => $q->latest()->limit(20),
            ]);
        }

        return view('livewire.tags.tag-management', [
            'tags' => $query->orderByDesc('usage_count')->get(),
        ])->layout('layouts.app', ['title' => 'Tag Management']);
    }
}
