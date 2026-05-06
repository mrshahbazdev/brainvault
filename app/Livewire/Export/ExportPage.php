<?php

namespace App\Livewire\Export;

use App\Services\ExportService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportPage extends Component
{
    public string $exportType = 'markdown';

    public function exportBookmarksMarkdown(): StreamedResponse
    {
        $service = app(ExportService::class);
        $content = $service->exportBookmarksMarkdown(Auth::id());

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'brainvault-bookmarks-' . now()->format('Y-m-d') . '.md');
    }

    public function exportBookmarksJson(): StreamedResponse
    {
        $service = app(ExportService::class);
        $data = $service->exportBookmarksJson(Auth::id());

        return response()->streamDownload(function () use ($data) {
            echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        }, 'brainvault-bookmarks-' . now()->format('Y-m-d') . '.json');
    }

    public function exportNotesMarkdown(): StreamedResponse
    {
        $service = app(ExportService::class);
        $content = $service->exportNotesMarkdown(Auth::id());

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'brainvault-notes-' . now()->format('Y-m-d') . '.md');
    }

    public function exportNotionCsv(): StreamedResponse
    {
        $service = app(ExportService::class);
        $content = $service->exportNotionCsv(Auth::id());

        return response()->streamDownload(function () use ($content) {
            echo $content;
        }, 'brainvault-notion-' . now()->format('Y-m-d') . '.csv');
    }

    public function render()
    {
        $stats = [
            'bookmarks' => Auth::user()->bookmarks()->where('is_trashed', false)->count(),
            'notes' => Auth::user()->notes()->where('is_trashed', false)->count(),
            'tags' => Auth::user()->tags()->count(),
            'collections' => Auth::user()->collections()->count(),
        ];

        return view('livewire.export.export-page', [
            'stats' => $stats,
        ])->layout('layouts.app', ['title' => 'Export']);
    }
}
