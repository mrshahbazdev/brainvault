<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WeeklyDigest extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $stats,
        public array $topBookmarks,
        public array $aiInsights,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Your BrainVault Weekly Digest',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.weekly-digest',
        );
    }
}
