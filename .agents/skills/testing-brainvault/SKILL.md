# BrainVault Local Testing

## Stack
- Laravel 13.7.0, Livewire v3, Alpine.js, Tailwind CSS v4
- PHP 8.3, MySQL (production) / SQLite (local testing)
- Shared hosting deployment at https://brainvault.allocore.de

## Local Setup

1. Install PHP extensions:
   ```bash
   sudo apt-get install -y php8.3-curl php8.3-mbstring php8.3-xml php8.3-bcmath php8.3-intl php8.3-sqlite3 php8.3-mysql php8.3-zip php8.3-dom php8.3-gd
   ```

2. Install dependencies:
   ```bash
   composer install --no-interaction
   npm install
   ```

3. Configure `.env` for local SQLite:
   ```
   DB_CONNECTION=sqlite
   DB_DATABASE=/home/ubuntu/repos/brainvault/database/database.sqlite
   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=database
   BROADCAST_CONNECTION=log
   ```

4. Set up database:
   ```bash
   touch database/database.sqlite
   php artisan key:generate
   php artisan migrate --force
   ```

5. Create test user:
   ```bash
   php artisan tinker --execute="\App\Models\User::create(['name'=>'Test User','email'=>'test@example.com','password'=>bcrypt('password123'),'onboarding_completed'=>true]);"
   ```

6. Build frontend and start server:
   ```bash
   npm run build
   php artisan serve --host=0.0.0.0 --port=8000
   ```

## Test Credentials
- Email: `test@example.com`
- Password: `password123`
- User must have `onboarding_completed=true` to bypass onboarding wizard redirect

## Common Issues

### Route Name Collisions
- `routes/api.php` uses `apiResource()` which generates route names like `bookmarks.index`
- `routes/web.php` also defines `bookmarks.index` for the web pages
- If API routes don't have a `name('api.')` prefix, `route('bookmarks.index')` might resolve to `/api/bookmarks` instead of `/bookmarks`
- **Fix:** Ensure `routes/api.php` wraps routes in `->name('api.')->group(...)` 

### Cache Issues After Code Changes
Always clear caches after changing routes, views, or config:
```bash
php artisan route:clear && php artisan view:clear && php artisan cache:clear && php artisan config:clear
```
Also restart the dev server — the PHP built-in server may serve stale compiled views.

### Livewire Layout
- Livewire v3 defaults to `components.layouts.app` layout
- The app has TWO layout files:
  - `resources/views/layouts/app.blade.php` (traditional Blade layout, used with `@extends`)
  - `resources/views/components/layouts/app.blade.php` (Blade component layout, used with `<x-layouts.app>`)
- Livewire components MUST have explicit `->layout('layouts.app')` in their `render()` method
- Without it, Livewire uses its own default component layout path which might not match

### Migration Errors on MySQL
- The embeddings migration uses PostgreSQL syntax (`CREATE EXTENSION IF NOT EXISTS vector`)
- For MySQL/SQLite, this migration should be guarded or use a JSON column instead

### Service Worker
- `public/sw.js` must NOT cache authenticated routes or intercept Livewire POST requests
- After deploying SW changes, users need to hard-refresh or unregister the old SW

## Testing Workflow
1. Start server: `php artisan serve --host=0.0.0.0 --port=8000`
2. Open browser to `http://localhost:8000/login`
3. Log in with test credentials
4. Navigate via sidebar links to test pages
5. Check browser DOM (computer tool output) to verify `href` attributes
6. Verify page content: heading, action buttons, sidebar presence

## Key Files
- Routes: `routes/web.php`, `routes/api.php`
- Layouts: `resources/views/layouts/app.blade.php`, `resources/views/components/layouts/app.blade.php`
- Livewire components: `app/Livewire/Bookmarks/BookmarkIndex.php`, `app/Livewire/Notes/NoteIndex.php`, `app/Livewire/Collections/CollectionIndex.php`
- Service worker: `public/sw.js`
- CSS: `resources/css/app.css` (Tailwind v4 with `@custom-variant dark`)

## Devin Secrets Needed
None required for local testing. The app runs fully locally with SQLite.
