<?php

namespace App\Livewire\ReadingList;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReadingListIndex extends Component
{
    use WithPagination;

    public string $sort = 'newest';

    public function markAsRead(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->update([
            'is_read' => true,
            'is_read_later' => false,
            'read_at' => now(),
        ]);
    }

    public function removeFromList(int $id): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->update([
            'is_read_later' => false,
        ]);
    }

    public function setReminder(int $id, string $date): void
    {
        Auth::user()->bookmarks()->findOrFail($id)->update([
            'read_later_reminder_at' => $date,
        ]);
    }

    public function render()
    {
        $bookmarks = Auth::user()->bookmarks()
            ->where('is_read_later', true)
            ->where('is_trashed', false)
            ->when($this->sort === 'newest', fn ($q) => $q->latest())
            ->when($this->sort === 'oldest', fn ($q) => $q->oldest())
            ->when($this->sort === 'reading_time', fn ($q) => $q->orderBy('reading_time'))
            ->with(['tags'])
            ->paginate(20);

        return view('livewire.reading-list.reading-list-index', [
            'bookmarks' => $bookmarks,
        ])->layout('layouts.app', ['title' => 'Reading List']);
    }
}
