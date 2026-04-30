<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\BookmarkImportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImportExportController extends Controller
{
    public function __construct(
        protected BookmarkImportService $importService,
    ) {}

    public function showImport()
    {
        return view('import.index');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'max:10240'],
            'source' => ['required', 'in:chrome,pocket,raindrop'],
        ]);

        $file = $request->file('file');
        $content = file_get_contents($file->getPathname());
        $user = Auth::user();

        try {
            $result = match ($request->source) {
                'chrome' => $this->importService->importChromeHtml($user, $content),
                'pocket' => $this->importService->importPocketJson($user, json_decode($content, true)),
                'raindrop' => $this->importService->importRaindropJson($user, json_decode($content, true)),
            };

            return back()->with('success', "Imported {$result['imported']} bookmarks. Skipped {$result['skipped']} duplicates.");
        } catch (\Exception $e) {
            return back()->with('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function exportJson()
    {
        $data = $this->importService->exportJson(Auth::user());

        return response()->json($data)
            ->header('Content-Disposition', 'attachment; filename="brainvault-bookmarks.json"');
    }

    public function exportHtml()
    {
        $html = $this->importService->exportHtml(Auth::user());

        return response($html)
            ->header('Content-Type', 'text/html')
            ->header('Content-Disposition', 'attachment; filename="brainvault-bookmarks.html"');
    }
}
