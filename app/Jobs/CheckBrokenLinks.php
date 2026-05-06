<?php

namespace App\Jobs;

use App\Models\Bookmark;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CheckBrokenLinks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 1;
    public int $timeout = 120;

    public function __construct(
        protected int $userId,
    ) {}

    public function handle(): void
    {
        $bookmarks = Bookmark::where('user_id', $this->userId)
            ->where('is_trashed', false)
            ->where(function ($q) {
                $q->whereNull('link_checked_at')
                    ->orWhere('link_checked_at', '<', now()->subDays(7));
            })
            ->limit(50)
            ->get();

        foreach ($bookmarks as $bookmark) {
            try {
                $response = Http::timeout(10)
                    ->withoutVerifying()
                    ->withHeaders(['User-Agent' => 'BrainVault Link Checker/1.0'])
                    ->get($bookmark->url);

                $status = match (true) {
                    $response->successful() => 'alive',
                    $response->redirect() => 'redirect',
                    $response->status() === 404 || $response->status() === 410 => 'dead',
                    default => 'unknown',
                };
            } catch (\Exception $e) {
                $status = 'dead';
                Log::debug('Link check failed', [
                    'bookmark_id' => $bookmark->id,
                    'url' => $bookmark->url,
                    'error' => $e->getMessage(),
                ]);
            }

            $bookmark->update([
                'link_status' => $status,
                'link_checked_at' => now(),
            ]);
        }
    }
}
