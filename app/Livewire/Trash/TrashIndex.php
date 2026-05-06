<?php

namespace App\Livewire\Trash;

use App\Models\Bookmark;
use App\Models\Note;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TrashIndex extends Component
{
    use WithPagination;

    public string $type = 'bookmarks';

    public function restoreBookmark(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->update([
            'is_trashed' => false,
            'trashed_at' => null,
        ]);
    }

    public function permanentDeleteBookmark(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->delete();
    }

    public function restoreNote(int $id): void
    {
        Auth::user()->notes()->findOrFail($id)->update([
            'is_trashed' => false,
            'trashed_at' => null,
        ]);
    }

    public function permanentDeleteNote(int $id): void
    {
        Auth::user()->notes()->findOrFail($id)->delete();
    }

    public function emptyTrash(): void
    {
        Auth::user()->bookmarks()->where('is_trashed', true)->delete();
        Auth::user()->notes()->where('is_trashed', true)->delete();
    }

    public function render()
    {
        $trashedBookmarks = Auth::user()->bookmarks()
            ->where('is_trashed', true)
            ->latest('trashed_at')
            ->paginate(20, ['*'], 'bookmarkPage');

        $trashedNotes = Auth::user()->notes()
            ->where('is_trashed', true)
            ->latest('trashed_at')
            ->paginate(20, ['*'], 'notePage');

        return view('livewire.trash.trash-index', [
            'trashedBookmarks' => $trashedBookmarks,
            'trashedNotes' => $trashedNotes,
        ])->layout('layouts.app', ['title' => 'Trash']);
    }
}
