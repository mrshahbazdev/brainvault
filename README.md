# BrainVault

**Your Second Brain for the Web** - An advanced bookmark and notes management platform with AI-powered organization.

## Features

- **One-Click Bookmarks** - Save any webpage with auto-metadata extraction
- **Web Highlighting** - Highlight text on any website with Chrome extension
- **Smart Notes** - Rich text editor linked to bookmarks and highlights
- **AI Summaries** - Auto-generate summaries, keywords, and categories with GPT-4
- **Collections & Tags** - Organize with nested folders and polymorphic tags
- **Powerful Search** - Full-text search powered by Meilisearch
- **Team Collaboration** - Shared collections and knowledge bases
- **Knowledge Graph** - Visual topic connections with D3.js
- **Chrome Extension** - Manifest V3 extension with popup, sidebar, and highlighting

## Tech Stack

| Component | Technology |
|-----------|-----------|
| Backend | Laravel 11, PHP 8.3 |
| Frontend | Livewire 3, Alpine.js, Tailwind CSS |
| Database | PostgreSQL 16 + pgvector |
| Search | Meilisearch |
| Cache/Queue | Redis 7 |
| Storage | S3/MinIO |
| Auth | Laravel Sanctum + Socialite (Google, GitHub) |
| Extension | Manifest V3, Preact, Vite |
| AI | OpenAI GPT-4o |

## Quick Start

### Prerequisites
- Docker & Docker Compose
- Node.js 20+
- PHP 8.3+
- Composer

### Setup with Docker

```bash
# Clone the repo
git clone https://github.com/mrshahbazdev/brainvault.git
cd brainvault

# Copy env file
cp .env.example .env

# Start services
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate

# Generate app key
docker compose exec app php artisan key:generate

# Visit http://localhost:8000
```

### Local Development

```bash
# Install dependencies
composer install
npm install

# Configure database (.env)
# Start PostgreSQL, Redis, Meilisearch locally

# Run migrations
php artisan migrate

# Build assets
npm run build

# Start dev server
php artisan serve
```

## API Documentation

All API endpoints require authentication via Sanctum token.

### Bookmarks
- `GET /api/bookmarks` - List bookmarks (paginated)
- `POST /api/bookmarks` - Create bookmark
- `GET /api/bookmarks/{id}` - Get bookmark details
- `PUT /api/bookmarks/{id}` - Update bookmark
- `DELETE /api/bookmarks/{id}` - Delete bookmark

### Collections
- `GET /api/collections` - List collections
- `POST /api/collections` - Create collection
- `GET /api/collections/{id}` - Get collection with bookmarks
- `PUT /api/collections/{id}` - Update collection
- `DELETE /api/collections/{id}` - Delete collection

### Notes
- `GET /api/notes` - List notes
- `POST /api/notes` - Create note
- `GET /api/notes/{id}` - Get note
- `PUT /api/notes/{id}` - Update note
- `DELETE /api/notes/{id}` - Soft delete (trash)

### Highlights
- `GET /api/highlights` - List highlights
- `POST /api/highlights` - Create highlight
- `GET /api/highlights/{id}` - Get highlight
- `PUT /api/highlights/{id}` - Update highlight
- `DELETE /api/highlights/{id}` - Delete highlight

### Tags
- `GET /api/tags` - List tags
- `POST /api/tags` - Create tag
- `PUT /api/tags/{id}` - Update tag
- `DELETE /api/tags/{id}` - Delete tag

## Project Structure

```
brainvault/
├── app/
│   ├── Http/Controllers/
│   │   ├── Api/          # REST API controllers
│   │   ├── Auth/         # Authentication controllers
│   │   └── Web/          # Dashboard controllers
│   ├── Models/           # Eloquent models
│   └── Policies/         # Authorization policies
├── database/
│   └── migrations/       # Database migrations
├── resources/
│   ├── css/              # Tailwind CSS
│   ├── js/               # Alpine.js
│   └── views/
│       ├── auth/         # Auth pages (login, register, etc.)
│       ├── dashboard/    # Dashboard views
│       ├── landing/      # Landing page
│       └── layouts/      # Blade layouts
├── routes/
│   ├── web.php           # Web routes
│   └── api.php           # API routes
├── docker/               # Docker configuration
└── docker-compose.yml    # Docker Compose services
```

## License

MIT
