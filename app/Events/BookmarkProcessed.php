<?php

namespace App\Events;

use App\Models\Bookmark;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class BookmarkProcessed implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Bookmark $bookmark,
        public string $status = 'completed',
    ) {}

    public function broadcastOn(): array
    {
        return [
            new PrivateChannel("user.{$this->bookmark->user_id}"),
        ];
    }

    public function broadcastAs(): string
    {
        return 'bookmark.processed';
    }

    public function broadcastWith(): array
    {
        return [
            'bookmark_id' => $this->bookmark->id,
            'title' => $this->bookmark->title,
            'status' => $this->status,
            'ai_summary' => $this->bookmark->ai_summary,
            'ai_category' => $this->bookmark->ai_category,
        ];
    }
}
