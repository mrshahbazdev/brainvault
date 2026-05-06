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
use Illuminate\Support\Facades\Storage;

class CapturePageSnapshot implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 2;
    public int $timeout = 60;

    public function __construct(
        protected int $bookmarkId,
    ) {}

    public function handle(): void
    {
        $bookmark = Bookmark::find($this->bookmarkId);
        if (!$bookmark) {
            return;
        }

        try {
            $response = Http::timeout(30)
                ->withoutVerifying()
                ->withHeaders(['User-Agent' => 'BrainVault Snapshot/1.0'])
                ->get($bookmark->url);

            if (!$response->successful()) {
                return;
            }

            $html = $response->body();
            $filename = "snapshots/{$bookmark->user_id}/{$bookmark->id}_" . now()->format('Y-m-d_His') . '.html';

            Storage::disk('local')->put($filename, $html);

            $bookmark->update([
                'snapshot_path' => $filename,
                'snapshot_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::warning('Page snapshot failed', [
                'bookmark_id' => $bookmark->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
