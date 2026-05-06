<?php

use App\Http\Controllers\Auth\EmailVerificationController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Web\DashboardController;
use App\Http\Controllers\Web\ImportExportController;
use App\Http\Controllers\Web\KnowledgeGraphController;
use App\Http\Controllers\Web\SettingsController;
use App\Livewire\Analytics\AnalyticsDashboard;
use App\Livewire\Bookmarks\BookmarkIndex;
use App\Livewire\Collections\CollectionIndex;
use App\Livewire\Highlights\HighlightIndex;
use App\Livewire\Notes\NoteIndex;
use App\Livewire\Billing\BillingPage;
use App\Livewire\Onboarding\OnboardingWizard;
use App\Livewire\Research\ResearchBoard;
use App\Livewire\Search\SearchPage;
use App\Livewire\Teams\TeamIndex;
use App\Livewire\Teams\TeamActivityFeed;
use App\Livewire\Tags\TagManagement;
use App\Livewire\ReadingList\ReadingListIndex;
use App\Livewire\Trash\TrashIndex;
use App\Livewire\Export\ExportPage;
use Illuminate\Support\Facades\Route;

// Landing Page
Route::get('/', function () {
    return view('landing.index');
})->name('home');

Route::get('/offline', function () {
    return view('offline');
})->name('offline');

Route::get('/extension', function () {
    return view('extension.index');
})->name('extension');

Route::get('/docs', function () {
    return view('docs.index');
})->name('docs');

Route::get('/language/{locale}', function (string $locale) {
    if (in_array($locale, ['en', 'de'])) {
        session(['locale' => $locale]);
        $cookie = cookie('locale', $locale, 60 * 24 * 365, '/', null, false, false);
        return redirect()->back()->withCookie($cookie);
    }
    return redirect()->back();
})->name('language.switch');

// Auth Routes (Guest only)
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    // Social Auth
    Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])->name('social.redirect');
    Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])->name('social.callback');
});

// Logout
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// Email Verification
Route::middleware('auth')->group(function () {
    Route::get('/email/verify', [EmailVerificationController::class, 'notice'])->name('verification.notice');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware('signed')
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:6,1')
        ->name('verification.send');
});

// Onboarding
Route::middleware(['auth'])->group(function () {
    Route::get('/onboarding', OnboardingWizard::class)->name('onboarding');
});

// Authenticated Routes
Route::middleware(['auth', \App\Http\Middleware\EnsureOnboardingCompleted::class])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Bookmarks
    Route::get('/bookmarks', BookmarkIndex::class)->name('bookmarks.index');

    // Collections
    Route::get('/collections', CollectionIndex::class)->name('collections.index');

    // Notes
    Route::get('/notes', NoteIndex::class)->name('notes.index');

    // Highlights
    Route::get('/highlights', HighlightIndex::class)->name('highlights.index');

    // Search (AI-powered)
    Route::get('/search', SearchPage::class)->name('search');

    // Analytics
    Route::get('/analytics', AnalyticsDashboard::class)->name('analytics');

    // Knowledge Graph
    Route::get('/knowledge-graph', [KnowledgeGraphController::class, 'index'])->name('knowledge-graph');

    // Teams
    Route::get('/teams', TeamIndex::class)->name('teams.index');
    Route::get('/teams/{teamId}/activity', TeamActivityFeed::class)->name('teams.activity');

    // Reading List
    Route::get('/reading-list', ReadingListIndex::class)->name('reading-list');

    // Trash
    Route::get('/trash', TrashIndex::class)->name('trash');

    // Tags Management
    Route::get('/tags', TagManagement::class)->name('tags.index');

    // Export
    Route::get('/export', ExportPage::class)->name('export');

    // Research
    Route::get('/research', ResearchBoard::class)->name('research.index');

    // Settings
    Route::get('/settings', [SettingsController::class, 'profile'])->name('settings.profile');
    Route::put('/settings/profile', [SettingsController::class, 'updateProfile'])->name('settings.profile.update');
    Route::put('/settings/password', [SettingsController::class, 'updatePassword'])->name('settings.password.update');
    Route::get('/settings/api-tokens', [SettingsController::class, 'apiTokens'])->name('settings.tokens');
    Route::post('/settings/api-tokens', [SettingsController::class, 'createApiToken'])->name('settings.tokens.create');
    Route::delete('/settings/api-tokens/{token}', [SettingsController::class, 'deleteApiToken'])->name('settings.tokens.delete');

    // Billing
    Route::get('/billing', BillingPage::class)->name('billing');

    // Import/Export
    Route::get('/import', [ImportExportController::class, 'showImport'])->name('import.show');
    Route::post('/import', [ImportExportController::class, 'import'])->name('import.process');
    Route::get('/export/json', [ImportExportController::class, 'exportJson'])->name('export.json');
    Route::get('/export/html', [ImportExportController::class, 'exportHtml'])->name('export.html');
});

// Public sharing routes (no auth required)
Route::get('/share/collection/{slug}', function (string $slug) {
    $collection = \App\Models\Collection::where('share_slug', $slug)
        ->where('visibility', 'public')
        ->with(['bookmarks.tags'])
        ->firstOrFail();
    return view('public.shared-collection', compact('collection'));
})->name('public.collection');

Route::get('/share/bookmark/{token}', function (string $token) {
    $bookmark = \App\Models\Bookmark::where('share_token', $token)
        ->where('is_trashed', false)
        ->with('tags')
        ->firstOrFail();
    return view('public.shared-bookmark', compact('bookmark'));
})->name('public.bookmark');
