<?php

namespace App\Livewire\Notifications;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationDropdown extends Component
{
    public bool $showDropdown = false;

    public function toggleDropdown(): void
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead(string $id): void
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
    }

    public function markAllRead(): void
    {
        Auth::user()->unreadNotifications->markAsRead();
    }

    public function render()
    {
        $notifications = Auth::user()->notifications()->latest()->take(20)->get();
        $unreadCount = Auth::user()->unreadNotifications()->count();

        return view('livewire.notifications.notification-dropdown', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }
}
