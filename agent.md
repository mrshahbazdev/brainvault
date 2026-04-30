# BrainVault - Advanced Bookmark & Notes Platform

## Advanced Development Roadmap

> **Mission**: Build a smart, AI-powered web platform + browser extension where users can save bookmarks, add notes, highlight web content, and manage knowledge seamlessly — a true "Second Brain" for the modern web.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Tech Stack](#2-tech-stack)
3. [System Architecture](#3-system-architecture)
4. [Database Schema](#4-database-schema)
5. [Authentication & Authorization](#5-authentication--authorization)
6. [Laravel Backend - API Design](#6-laravel-backend---api-design)
7. [Chrome Extension Architecture](#7-chrome-extension-architecture)
8. [Frontend UI/UX Design](#8-frontend-uiux-design)
9. [Landing Page Specification](#9-landing-page-specification)
10. [AI Integration & Smart Features](#10-ai-integration--smart-features)
11. [Knowledge Graph & Search](#11-knowledge-graph--search)
12. [Team & Collaboration Features](#12-team--collaboration-features)
13. [Phased Development Roadmap](#13-phased-development-roadmap)
14. [Directory Structure](#14-directory-structure)
15. [DevOps & Deployment](#15-devops--deployment)
16. [Security Considerations](#16-security-considerations)
17. [Performance & Scaling](#17-performance--scaling)
18. [Future Vision](#18-future-vision)

---

## 1. Project Overview

**BrainVault** is an all-in-one knowledge management platform that combines:

- **Web Dashboard** (Laravel + Blade/Livewire) — Beautiful, responsive UI for managing bookmarks, notes, highlights, and research
- **Chrome Extension** — One-click bookmark saving, text highlighting, screenshot capture, and note-taking directly on any webpage
- **AI Engine** — Smart summaries, keyword extraction, topic clustering, related content suggestions, and a personal knowledge graph
- **Collaboration Hub** — Share collections, co-annotate, and build team knowledge bases

### Core Value Propositions

| Feature | Description |
|---------|-------------|
| **Instant Capture** | Save any URL, selected text, or screenshot with one click |
| **AI Summaries** | Auto-generate summaries & extract keywords from saved content |
| **Highlight Sync** | Highlight text on any website, synced to your dashboard |
| **Knowledge Graph** | Visual connections between your bookmarks, notes & topics |
| **Smart Search** | Full-text search across notes, highlights, screenshots & AI summaries |
| **Research Folders** | Auto-organize bookmarks into topic-based research folders |
| **Team Collaboration** | Share collections, annotate together, build collective knowledge |
| **Second Brain AI** | Personalized insights, next-action suggestions, learning progress tracking |

---

## 2. Tech Stack

### Backend (Laravel)

| Component | Technology | Why |
|-----------|-----------|-----|
| **Framework** | Laravel 11.x | Robust PHP framework, excellent ecosystem, built-in auth |
| **PHP Version** | PHP 8.3+ | JIT compilation, fibers, typed properties, enums |
| **Database** | PostgreSQL 16 | Full-text search (tsvector), JSONB columns, excellent scaling |
| **Cache/Queue** | Redis 7 | Session storage, cache, queue driver, real-time pub/sub |
| **Queue Worker** | Laravel Horizon | Dashboard for monitoring Redis queues |
| **Search Engine** | Meilisearch | Typo-tolerant, fast full-text search with faceting |
| **File Storage** | S3 / MinIO | Screenshots, PDFs, file attachments |
| **Real-time** | Laravel Reverb (WebSockets) | Live collaboration, real-time sync |
| **API Auth** | Laravel Sanctum | SPA + API token authentication |
| **Admin Panel** | Filament 3 | Admin dashboard for platform management |

### Frontend (Web Dashboard)

| Component | Technology | Why |
|-----------|-----------|-----|
| **UI Framework** | Livewire 3 + Alpine.js | Reactive components without heavy JS framework |
| **CSS Framework** | Tailwind CSS 3.4 | Utility-first, highly customizable |
| **Component Library** | Custom + Flowbite | Pre-built Tailwind components |
| **Charts/Graphs** | Chart.js + D3.js | Analytics dashboard & knowledge graph visualization |
| **Rich Text Editor** | Tiptap (ProseMirror) | Note editing with markdown support |
| **Icons** | Heroicons + Lucide | Consistent icon system |
| **Animations** | Framer Motion (landing) + Alpine transitions | Smooth micro-interactions |
| **Dark Mode** | Tailwind dark variant | System preference + manual toggle |

### Chrome Extension

| Component | Technology | Why |
|-----------|-----------|-----|
| **Manifest** | Manifest V3 | Latest Chrome extension standard |
| **UI Framework** | Preact + HTM | Lightweight (3KB), React-compatible for popup/sidebar |
| **Styling** | Tailwind CSS (compiled) | Consistent design with web app |
| **Highlighting** | Custom DOM manipulation | Text selection & persistent highlights via CSS injection |
| **Storage** | chrome.storage.sync + IndexedDB | Offline capability + cross-device sync |
| **Communication** | REST API + WebSocket | Real-time sync with backend |
| **Screenshots** | chrome.tabs.captureVisibleTab | Full page & selected area screenshots |

### AI & ML

| Component | Technology | Why |
|-----------|-----------|-----|
| **LLM Provider** | OpenAI GPT-4o / Claude API | Summaries, keyword extraction, smart suggestions |
| **Embeddings** | OpenAI text-embedding-3-small | Semantic search & content similarity |
| **Vector Store** | pgvector (PostgreSQL extension) | Store embeddings alongside relational data |
| **NLP Pipeline** | Python microservice (FastAPI) | Topic modeling, entity extraction, clustering |
| **OCR** | Tesseract OCR (via PHP wrapper) | Extract text from screenshots & images |

### DevOps & Infrastructure

| Component | Technology | Why |
|-----------|-----------|-----|
| **Containerization** | Docker + Docker Compose | Consistent dev/prod environments |
| **CI/CD** | GitHub Actions | Automated testing, linting, deployment |
| **Hosting** | DigitalOcean / AWS | Scalable cloud hosting |
| **CDN** | Cloudflare | Static assets, DDoS protection |
| **Monitoring** | Laravel Telescope + Sentry | Error tracking & performance monitoring |
| **SSL** | Let's Encrypt (Certbot) | Free HTTPS certificates |

---

## 3. System Architecture

```
┌─────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER                              │
│                                                                  │
│  ┌──────────────┐  ┌──────────────────┐  ┌──────────────────┐  │
│  │   Chrome      │  │  Web Dashboard   │  │  Mobile PWA      │  │
│  │   Extension   │  │  (Livewire +     │  │  (Future)        │  │
│  │   (Preact)    │  │   Alpine.js)     │  │                  │  │
│  └──────┬───────┘  └────────┬─────────┘  └────────┬─────────┘  │
│         │                   │                      │             │
└─────────┼───────────────────┼──────────────────────┼─────────────┘
          │                   │                      │
          ▼                   ▼                      ▼
┌─────────────────────────────────────────────────────────────────┐
│                      API GATEWAY / ROUTES                        │
│                                                                  │
│  ┌──────────────────────────────────────────────────────────┐   │
│  │  Laravel Router                                           │   │
│  │  ├── /api/v1/*          (Sanctum Token Auth)             │   │
│  │  ├── /web/*             (Session Auth + Livewire)        │   │
│  │  ├── /extension/*       (Extension API + Token Auth)     │   │
│  │  └── /webhook/*         (Third-party integrations)       │   │
│  └──────────────────────────────────────────────────────────┘   │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                    APPLICATION LAYER                              │
│                                                                  │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌─────────────┐  │
│  │ Bookmark   │ │   Notes    │ │ Highlight  │ │ Collection  │  │
│  │ Service    │ │  Service   │ │  Service   │ │  Service    │  │
│  └────────────┘ └────────────┘ └────────────┘ └─────────────┘  │
│                                                                  │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐ ┌─────────────┐  │
│  │   AI       │ │  Search    │ │   Team     │ │  Analytics  │  │
│  │  Service   │ │  Service   │ │  Service   │ │  Service    │  │
│  └────────────┘ └────────────┘ └────────────┘ └─────────────┘  │
│                                                                  │
│  ┌────────────┐ ┌────────────┐ ┌────────────┐                  │
│  │  Scraper   │ │  Export    │ │  Webhook   │                  │
│  │  Service   │ │  Service   │ │  Service   │                  │
│  └────────────┘ └────────────┘ └────────────┘                  │
└─────────────────────────┬───────────────────────────────────────┘
                          │
                          ▼
┌─────────────────────────────────────────────────────────────────┐
│                     DATA & INFRASTRUCTURE                        │
│                                                                  │
│  ┌──────────┐ ┌──────────┐ ┌───────────┐ ┌──────────────────┐  │
│  │PostgreSQL│ │  Redis   │ │Meilisearch│ │  S3 / MinIO      │  │
│  │+ pgvector│ │ (Cache/  │ │(Full-text │ │  (File Storage)  │  │
│  │(Database)│ │  Queue)  │ │  Search)  │ │                  │  │
│  └──────────┘ └──────────┘ └───────────┘ └──────────────────┘  │
│                                                                  │
│  ┌────────────────────┐  ┌───────────────────────────────────┐  │
│  │ Python AI Service  │  │  Laravel Reverb (WebSockets)     │  │
│  │ (FastAPI)          │  │  Real-time sync & collaboration  │  │
│  └────────────────────┘  └───────────────────────────────────┘  │
└─────────────────────────────────────────────────────────────────┘
```

### Request Flow

```
User Action → Chrome Extension / Web UI
    → HTTP Request (with Sanctum Token)
    → Laravel Middleware (Auth, Rate Limit, CORS)
    → Controller → Form Request Validation
    → Service Class → Repository/Model
    → Database Query / Cache Check
    → Queue Job (if async: AI processing, scraping, notifications)
    → Response (JSON for API / Livewire for Web)
```

---

## 4. Database Schema

### Core Tables

```sql
-- =====================================================
-- USERS & AUTHENTICATION
-- =====================================================

CREATE TABLE users (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    email           VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password        VARCHAR(255) NOT NULL,
    avatar          VARCHAR(500) NULL,
    bio             TEXT NULL,
    timezone        VARCHAR(50) DEFAULT 'UTC',
    language        VARCHAR(10) DEFAULT 'en',
    theme           VARCHAR(20) DEFAULT 'system', -- light/dark/system
    onboarding_completed BOOLEAN DEFAULT FALSE,
    storage_used_bytes  BIGINT DEFAULT 0,
    plan            VARCHAR(50) DEFAULT 'free', -- free/pro/team/enterprise
    settings        JSONB DEFAULT '{}',
    last_active_at  TIMESTAMP NULL,
    remember_token  VARCHAR(100) NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE social_accounts (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    provider        VARCHAR(50) NOT NULL, -- google/github/twitter
    provider_id     VARCHAR(255) NOT NULL,
    provider_token  TEXT NULL,
    provider_refresh_token TEXT NULL,
    provider_avatar VARCHAR(500) NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(provider, provider_id)
);

-- =====================================================
-- BOOKMARKS
-- =====================================================

CREATE TABLE bookmarks (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    url             TEXT NOT NULL,
    title           VARCHAR(500) NULL,
    description     TEXT NULL,
    excerpt         TEXT NULL,                    -- Auto-extracted content snippet
    favicon_url     VARCHAR(500) NULL,
    og_image_url    VARCHAR(500) NULL,           -- OpenGraph image
    screenshot_path VARCHAR(500) NULL,           -- Stored screenshot
    site_name       VARCHAR(255) NULL,           -- og:site_name
    content_type    VARCHAR(50) DEFAULT 'webpage', -- webpage/article/video/pdf/tweet
    reading_time    INTEGER NULL,                -- Estimated minutes
    word_count      INTEGER NULL,
    is_archived     BOOLEAN DEFAULT FALSE,
    is_favorite     BOOLEAN DEFAULT FALSE,
    is_read         BOOLEAN DEFAULT FALSE,
    read_at         TIMESTAMP NULL,
    read_progress   DECIMAL(5,2) DEFAULT 0,      -- 0-100%
    metadata        JSONB DEFAULT '{}',          -- Flexible metadata storage
    ai_summary      TEXT NULL,                   -- AI-generated summary
    ai_keywords     JSONB DEFAULT '[]',          -- AI-extracted keywords
    ai_category     VARCHAR(100) NULL,           -- AI-suggested category
    ai_embedding    vector(1536) NULL,           -- pgvector embedding
    scraped_at      TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),

    -- Full-text search vector
    search_vector   tsvector GENERATED ALWAYS AS (
        setweight(to_tsvector('english', COALESCE(title, '')), 'A') ||
        setweight(to_tsvector('english', COALESCE(description, '')), 'B') ||
        setweight(to_tsvector('english', COALESCE(excerpt, '')), 'C') ||
        setweight(to_tsvector('english', COALESCE(ai_summary, '')), 'D')
    ) STORED
);

CREATE INDEX idx_bookmarks_user ON bookmarks(user_id);
CREATE INDEX idx_bookmarks_search ON bookmarks USING GIN(search_vector);
CREATE INDEX idx_bookmarks_embedding ON bookmarks USING ivfflat(ai_embedding vector_cosine_ops);
CREATE INDEX idx_bookmarks_url ON bookmarks(user_id, url);
CREATE INDEX idx_bookmarks_created ON bookmarks(user_id, created_at DESC);
CREATE INDEX idx_bookmarks_content_type ON bookmarks(user_id, content_type);

-- =====================================================
-- COLLECTIONS (Folders/Categories)
-- =====================================================

CREATE TABLE collections (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    parent_id       BIGINT REFERENCES collections(id) ON DELETE SET NULL,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL,
    description     TEXT NULL,
    color           VARCHAR(7) DEFAULT '#6366F1', -- Hex color
    icon            VARCHAR(50) DEFAULT 'folder',
    cover_image     VARCHAR(500) NULL,
    is_default      BOOLEAN DEFAULT FALSE,
    is_smart        BOOLEAN DEFAULT FALSE,        -- Smart collection with auto-rules
    smart_rules     JSONB NULL,                   -- Rules for smart collections
    sort_order      INTEGER DEFAULT 0,
    visibility      VARCHAR(20) DEFAULT 'private', -- private/shared/public
    share_slug      VARCHAR(100) UNIQUE NULL,
    item_count      INTEGER DEFAULT 0,             -- Cached count
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, slug)
);

CREATE TABLE bookmark_collection (
    bookmark_id     BIGINT REFERENCES bookmarks(id) ON DELETE CASCADE,
    collection_id   BIGINT REFERENCES collections(id) ON DELETE CASCADE,
    sort_order      INTEGER DEFAULT 0,
    added_at        TIMESTAMP DEFAULT NOW(),
    PRIMARY KEY (bookmark_id, collection_id)
);

-- =====================================================
-- TAGS
-- =====================================================

CREATE TABLE tags (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    name            VARCHAR(100) NOT NULL,
    slug            VARCHAR(100) NOT NULL,
    color           VARCHAR(7) NULL,
    usage_count     INTEGER DEFAULT 0,
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, slug)
);

CREATE TABLE taggables (
    tag_id          BIGINT REFERENCES tags(id) ON DELETE CASCADE,
    taggable_id     BIGINT NOT NULL,
    taggable_type   VARCHAR(100) NOT NULL,  -- App\Models\Bookmark, App\Models\Note, etc.
    created_at      TIMESTAMP DEFAULT NOW(),
    PRIMARY KEY (tag_id, taggable_id, taggable_type)
);

-- =====================================================
-- NOTES
-- =====================================================

CREATE TABLE notes (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    bookmark_id     BIGINT REFERENCES bookmarks(id) ON DELETE SET NULL,
    parent_id       BIGINT REFERENCES notes(id) ON DELETE SET NULL,
    title           VARCHAR(500) NULL,
    content         TEXT NULL,                   -- Rich text / Markdown content
    content_html    TEXT NULL,                   -- Rendered HTML
    content_plain   TEXT NULL,                   -- Plain text for search
    note_type       VARCHAR(50) DEFAULT 'note',  -- note/todo/journal/snippet
    is_pinned       BOOLEAN DEFAULT FALSE,
    is_archived     BOOLEAN DEFAULT FALSE,
    is_trashed      BOOLEAN DEFAULT FALSE,
    trashed_at      TIMESTAMP NULL,
    color           VARCHAR(7) NULL,
    cover_image     VARCHAR(500) NULL,
    word_count      INTEGER DEFAULT 0,
    ai_summary      TEXT NULL,
    ai_keywords     JSONB DEFAULT '[]',
    ai_embedding    vector(1536) NULL,
    metadata        JSONB DEFAULT '{}',
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),

    search_vector   tsvector GENERATED ALWAYS AS (
        setweight(to_tsvector('english', COALESCE(title, '')), 'A') ||
        setweight(to_tsvector('english', COALESCE(content_plain, '')), 'B')
    ) STORED
);

CREATE INDEX idx_notes_user ON notes(user_id);
CREATE INDEX idx_notes_bookmark ON notes(bookmark_id);
CREATE INDEX idx_notes_search ON notes USING GIN(search_vector);
CREATE INDEX idx_notes_embedding ON notes USING ivfflat(ai_embedding vector_cosine_ops);

-- =====================================================
-- HIGHLIGHTS & ANNOTATIONS
-- =====================================================

CREATE TABLE highlights (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    bookmark_id     BIGINT REFERENCES bookmarks(id) ON DELETE CASCADE,
    text            TEXT NOT NULL,               -- Highlighted text content
    note            TEXT NULL,                   -- User annotation on the highlight
    color           VARCHAR(7) DEFAULT '#FBBF24', -- yellow/green/blue/pink/purple
    page_url        TEXT NOT NULL,
    -- DOM position for re-rendering highlights
    start_xpath     TEXT NOT NULL,
    start_offset    INTEGER NOT NULL,
    end_xpath       TEXT NOT NULL,
    end_offset      INTEGER NOT NULL,
    -- Additional context
    surrounding_text TEXT NULL,                  -- Text around the highlight for context
    screenshot_path VARCHAR(500) NULL,
    ai_embedding    vector(1536) NULL,
    sort_order      INTEGER DEFAULT 0,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW(),

    search_vector   tsvector GENERATED ALWAYS AS (
        setweight(to_tsvector('english', COALESCE(text, '')), 'A') ||
        setweight(to_tsvector('english', COALESCE(note, '')), 'B')
    ) STORED
);

CREATE INDEX idx_highlights_user ON highlights(user_id);
CREATE INDEX idx_highlights_bookmark ON highlights(bookmark_id);
CREATE INDEX idx_highlights_url ON highlights(user_id, page_url);
CREATE INDEX idx_highlights_search ON highlights USING GIN(search_vector);

-- =====================================================
-- SCREENSHOTS & ATTACHMENTS
-- =====================================================

CREATE TABLE attachments (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    attachable_id   BIGINT NOT NULL,
    attachable_type VARCHAR(100) NOT NULL,       -- Polymorphic relation
    file_name       VARCHAR(255) NOT NULL,
    file_path       VARCHAR(500) NOT NULL,
    file_size       BIGINT NOT NULL,             -- Bytes
    mime_type       VARCHAR(100) NOT NULL,
    disk            VARCHAR(50) DEFAULT 's3',
    ocr_text        TEXT NULL,                   -- OCR extracted text
    metadata        JSONB DEFAULT '{}',
    created_at      TIMESTAMP DEFAULT NOW()
);

-- =====================================================
-- RESEARCH FOLDERS & TODO TASKS
-- =====================================================

CREATE TABLE research_projects (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    name            VARCHAR(255) NOT NULL,
    description     TEXT NULL,
    status          VARCHAR(50) DEFAULT 'active', -- active/paused/completed/archived
    color           VARCHAR(7) DEFAULT '#8B5CF6',
    icon            VARCHAR(50) DEFAULT 'beaker',
    deadline        DATE NULL,
    progress        DECIMAL(5,2) DEFAULT 0,
    settings        JSONB DEFAULT '{}',
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE research_project_items (
    id              BIGSERIAL PRIMARY KEY,
    research_project_id BIGINT REFERENCES research_projects(id) ON DELETE CASCADE,
    itemable_id     BIGINT NOT NULL,
    itemable_type   VARCHAR(100) NOT NULL,       -- Bookmark, Note, Highlight
    sort_order      INTEGER DEFAULT 0,
    notes           TEXT NULL,
    added_at        TIMESTAMP DEFAULT NOW()
);

CREATE TABLE tasks (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    research_project_id BIGINT REFERENCES research_projects(id) ON DELETE SET NULL,
    bookmark_id     BIGINT REFERENCES bookmarks(id) ON DELETE SET NULL,
    title           VARCHAR(500) NOT NULL,
    description     TEXT NULL,
    status          VARCHAR(50) DEFAULT 'pending', -- pending/in_progress/completed/cancelled
    priority        VARCHAR(20) DEFAULT 'medium',  -- low/medium/high/urgent
    due_date        TIMESTAMP NULL,
    completed_at    TIMESTAMP NULL,
    sort_order      INTEGER DEFAULT 0,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

-- =====================================================
-- TEAMS & COLLABORATION
-- =====================================================

CREATE TABLE teams (
    id              BIGSERIAL PRIMARY KEY,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) UNIQUE NOT NULL,
    description     TEXT NULL,
    avatar          VARCHAR(500) NULL,
    owner_id        BIGINT REFERENCES users(id) ON DELETE CASCADE,
    settings        JSONB DEFAULT '{}',
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

CREATE TABLE team_members (
    id              BIGSERIAL PRIMARY KEY,
    team_id         BIGINT REFERENCES teams(id) ON DELETE CASCADE,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    role            VARCHAR(50) DEFAULT 'member', -- owner/admin/editor/viewer
    joined_at       TIMESTAMP DEFAULT NOW(),
    UNIQUE(team_id, user_id)
);

CREATE TABLE shared_collections (
    id              BIGSERIAL PRIMARY KEY,
    collection_id   BIGINT REFERENCES collections(id) ON DELETE CASCADE,
    team_id         BIGINT REFERENCES teams(id) ON DELETE CASCADE,
    permission      VARCHAR(50) DEFAULT 'view', -- view/comment/edit
    shared_by       BIGINT REFERENCES users(id),
    shared_at       TIMESTAMP DEFAULT NOW(),
    UNIQUE(collection_id, team_id)
);

CREATE TABLE comments (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    commentable_id  BIGINT NOT NULL,
    commentable_type VARCHAR(100) NOT NULL,
    parent_id       BIGINT REFERENCES comments(id) ON DELETE CASCADE,
    body            TEXT NOT NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    updated_at      TIMESTAMP DEFAULT NOW()
);

-- =====================================================
-- KNOWLEDGE GRAPH
-- =====================================================

CREATE TABLE topics (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    name            VARCHAR(255) NOT NULL,
    slug            VARCHAR(255) NOT NULL,
    description     TEXT NULL,
    color           VARCHAR(7) NULL,
    icon            VARCHAR(50) NULL,
    ai_generated    BOOLEAN DEFAULT FALSE,
    item_count      INTEGER DEFAULT 0,
    embedding       vector(1536) NULL,
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, slug)
);

CREATE TABLE topic_connections (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    source_topic_id BIGINT REFERENCES topics(id) ON DELETE CASCADE,
    target_topic_id BIGINT REFERENCES topics(id) ON DELETE CASCADE,
    strength        DECIMAL(3,2) DEFAULT 0.5,    -- 0-1 connection strength
    ai_generated    BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(source_topic_id, target_topic_id)
);

CREATE TABLE topicables (
    topic_id        BIGINT REFERENCES topics(id) ON DELETE CASCADE,
    topicable_id    BIGINT NOT NULL,
    topicable_type  VARCHAR(100) NOT NULL,
    relevance_score DECIMAL(3,2) DEFAULT 0.5,
    ai_assigned     BOOLEAN DEFAULT TRUE,
    created_at      TIMESTAMP DEFAULT NOW(),
    PRIMARY KEY (topic_id, topicable_id, topicable_type)
);

-- =====================================================
-- ACTIVITY & ANALYTICS
-- =====================================================

CREATE TABLE activities (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    action          VARCHAR(100) NOT NULL,        -- bookmark.created, note.updated, etc.
    subject_id      BIGINT NULL,
    subject_type    VARCHAR(100) NULL,
    properties      JSONB DEFAULT '{}',
    ip_address      VARCHAR(45) NULL,
    user_agent      TEXT NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);

CREATE INDEX idx_activities_user ON activities(user_id, created_at DESC);

CREATE TABLE reading_stats (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    date            DATE NOT NULL,
    bookmarks_added INTEGER DEFAULT 0,
    bookmarks_read  INTEGER DEFAULT 0,
    notes_created   INTEGER DEFAULT 0,
    highlights_made INTEGER DEFAULT 0,
    time_spent_seconds INTEGER DEFAULT 0,
    topics_explored JSONB DEFAULT '[]',
    created_at      TIMESTAMP DEFAULT NOW(),
    UNIQUE(user_id, date)
);

-- =====================================================
-- IMPORTS & EXPORTS
-- =====================================================

CREATE TABLE imports (
    id              BIGSERIAL PRIMARY KEY,
    user_id         BIGINT REFERENCES users(id) ON DELETE CASCADE,
    source          VARCHAR(100) NOT NULL,        -- chrome/firefox/pocket/raindrop/csv
    file_path       VARCHAR(500) NULL,
    status          VARCHAR(50) DEFAULT 'pending',-- pending/processing/completed/failed
    total_items     INTEGER DEFAULT 0,
    processed_items INTEGER DEFAULT 0,
    failed_items    INTEGER DEFAULT 0,
    error_log       JSONB DEFAULT '[]',
    started_at      TIMESTAMP NULL,
    completed_at    TIMESTAMP NULL,
    created_at      TIMESTAMP DEFAULT NOW()
);
```

---

## 5. Authentication & Authorization

### Auth System Design

```
┌──────────────────────────────────────────────────┐
│                AUTH FLOW                           │
│                                                   │
│  ┌─────────────┐     ┌──────────────────────┐   │
│  │  Register    │     │  Social OAuth Login   │   │
│  │  (Email +   │     │  ┌─────┐ ┌────────┐  │   │
│  │   Password) │     │  │Google│ │ GitHub │  │   │
│  └──────┬──────┘     │  └─────┘ └────────┘  │   │
│         │            │  ┌─────┐ ┌────────┐  │   │
│         │            │  │Twitter│ │ Apple │  │   │
│         │            │  └─────┘ └────────┘  │   │
│         │            └──────────┬───────────┘   │
│         ▼                       ▼                │
│  ┌──────────────────────────────────────────┐   │
│  │         Email Verification                │   │
│  │         (Queued mail with signed URL)     │   │
│  └────────────────────┬─────────────────────┘   │
│                       ▼                          │
│  ┌──────────────────────────────────────────┐   │
│  │     2FA Setup (Optional but encouraged)   │   │
│  │     TOTP / Recovery Codes                 │   │
│  └────────────────────┬─────────────────────┘   │
│                       ▼                          │
│  ┌──────────────────────────────────────────┐   │
│  │          Dashboard Access                 │   │
│  │  ┌───────┐  ┌─────────┐  ┌──────────┐  │   │
│  │  │Session│  │ Sanctum │  │ Extension│  │   │
│  │  │ Auth  │  │  Token  │  │  Token   │  │   │
│  │  │ (Web) │  │  (API)  │  │ (Chrome) │  │   │
│  │  └───────┘  └─────────┘  └──────────┘  │   │
│  └──────────────────────────────────────────┘   │
└──────────────────────────────────────────────────┘
```

### Auth Features Checklist

- [x] Email + Password Registration with strong validation
- [x] Email Verification (queued, branded template)
- [x] Login with rate limiting (5 attempts / minute)
- [x] Social OAuth (Google, GitHub, Twitter/X, Apple)
- [x] Two-Factor Authentication (TOTP via Google Authenticator)
- [x] Recovery Codes (8 single-use codes)
- [x] Password Reset with signed, expiring links
- [x] Remember Me (long-lived session)
- [x] Account Deletion with data export
- [x] Session Management (view/revoke active sessions)
- [x] API Token Management (create/revoke with scopes)
- [x] Chrome Extension Token (dedicated, auto-refresh)

### Auth UI Pages

| Page | Route | Description |
|------|-------|-------------|
| **Register** | `/register` | Clean card layout, name/email/password, social buttons, terms checkbox |
| **Login** | `/login` | Email/password + "Remember me" + social login buttons + forgot password |
| **Forgot Password** | `/forgot-password` | Email input, sends reset link |
| **Reset Password** | `/reset-password/{token}` | New password form |
| **Verify Email** | `/verify-email` | Verification notice + resend button |
| **2FA Setup** | `/settings/two-factor` | QR code scanner + recovery codes |
| **2FA Challenge** | `/two-factor-challenge` | 6-digit code or recovery code input |
| **Profile Settings** | `/settings/profile` | Avatar, name, email, timezone, language |
| **Security Settings** | `/settings/security` | Password change, 2FA, active sessions, API tokens |

### Auth UI Design Specs

```
Register / Login Page:
├── Split layout (desktop): Left = brand panel with tagline + illustration
│                           Right = auth form card
├── Mobile: Full-width card with brand header
├── Social login buttons at top (prominent)
├── Divider: "or continue with email"
├── Form fields: floating labels, inline validation
├── Password strength meter (real-time)
├── Error messages: inline + toast notifications
├── Loading state: button spinner + disabled state
└── Success: redirect to dashboard with welcome toast
```

---

## 6. Laravel Backend - API Design

### API Versioning & Structure

```
/api/v1/
├── auth/
│   ├── POST   /register
│   ├── POST   /login
│   ├── POST   /logout
│   ├── POST   /forgot-password
│   ├── POST   /reset-password
│   ├── POST   /verify-email/{id}/{hash}
│   ├── POST   /two-factor/enable
│   ├── POST   /two-factor/confirm
│   ├── DELETE /two-factor/disable
│   ├── POST   /two-factor/challenge
│   ├── GET    /two-factor/recovery-codes
│   ├── POST   /two-factor/recovery-codes
│   └── GET    /user
│
├── bookmarks/
│   ├── GET    /                           # List (paginated, filterable, searchable)
│   ├── POST   /                           # Create
│   ├── GET    /{id}                       # Show
│   ├── PUT    /{id}                       # Update
│   ├── DELETE /{id}                       # Delete
│   ├── POST   /{id}/archive               # Archive/Unarchive
│   ├── POST   /{id}/favorite              # Toggle favorite
│   ├── POST   /{id}/read                  # Mark read + track progress
│   ├── GET    /{id}/highlights             # Get highlights for bookmark
│   ├── GET    /{id}/notes                  # Get notes for bookmark
│   ├── POST   /{id}/screenshot             # Trigger screenshot capture
│   ├── POST   /import                     # Bulk import (Chrome HTML, Pocket, CSV)
│   ├── GET    /export                     # Export bookmarks
│   ├── POST   /quick-save                 # Extension quick save (URL only, scrape async)
│   └── GET    /suggestions                # AI-powered related bookmark suggestions
│
├── collections/
│   ├── GET    /                           # List with item counts
│   ├── POST   /                           # Create
│   ├── GET    /{id}                       # Show with bookmarks
│   ├── PUT    /{id}                       # Update
│   ├── DELETE /{id}                       # Delete
│   ├── POST   /{id}/bookmarks             # Add bookmarks to collection
│   ├── DELETE /{id}/bookmarks/{bookmarkId} # Remove bookmark from collection
│   ├── PUT    /{id}/reorder               # Reorder bookmarks
│   ├── POST   /{id}/share                 # Generate share link
│   └── DELETE /{id}/share                 # Revoke share link
│
├── notes/
│   ├── GET    /                           # List (paginated, filterable)
│   ├── POST   /                           # Create
│   ├── GET    /{id}                       # Show
│   ├── PUT    /{id}                       # Update
│   ├── DELETE /{id}                       # Soft delete → trash
│   ├── POST   /{id}/restore               # Restore from trash
│   ├── DELETE /{id}/force                 # Permanent delete
│   ├── POST   /{id}/pin                   # Toggle pin
│   ├── POST   /{id}/duplicate             # Duplicate note
│   └── GET    /{id}/versions              # Note version history
│
├── highlights/
│   ├── GET    /                           # List all highlights
│   ├── POST   /                           # Create (from extension)
│   ├── GET    /{id}                       # Show
│   ├── PUT    /{id}                       # Update (color, note)
│   ├── DELETE /{id}                       # Delete
│   ├── GET    /by-url                     # Get highlights for a URL (extension)
│   └── GET    /recent                     # Recent highlights feed
│
├── tags/
│   ├── GET    /                           # List with usage counts
│   ├── POST   /                           # Create
│   ├── PUT    /{id}                       # Rename/recolor
│   ├── DELETE /{id}                       # Delete
│   ├── GET    /{id}/items                 # All items with this tag
│   └── POST   /merge                     # Merge tags
│
├── search/
│   ├── GET    /                           # Universal search (bookmarks, notes, highlights)
│   ├── GET    /semantic                   # AI semantic search
│   └── GET    /suggestions                # Search suggestions/autocomplete
│
├── research/
│   ├── GET    /projects                   # List research projects
│   ├── POST   /projects                   # Create project
│   ├── GET    /projects/{id}              # Show with items
│   ├── PUT    /projects/{id}              # Update
│   ├── DELETE /projects/{id}              # Delete
│   ├── POST   /projects/{id}/items        # Add items to project
│   └── DELETE /projects/{id}/items/{itemId} # Remove item
│
├── tasks/
│   ├── GET    /                           # List tasks
│   ├── POST   /                           # Create
│   ├── PUT    /{id}                       # Update
│   ├── DELETE /{id}                       # Delete
│   └── POST   /{id}/complete              # Toggle complete
│
├── teams/
│   ├── GET    /                           # List user's teams
│   ├── POST   /                           # Create team
│   ├── GET    /{id}                       # Show team
│   ├── PUT    /{id}                       # Update team
│   ├── DELETE /{id}                       # Delete team
│   ├── POST   /{id}/members               # Invite member
│   ├── PUT    /{id}/members/{userId}      # Update role
│   ├── DELETE /{id}/members/{userId}      # Remove member
│   └── GET    /{id}/activity              # Team activity feed
│
├── ai/
│   ├── POST   /summarize                  # Summarize content
│   ├── POST   /extract-keywords           # Extract keywords
│   ├── POST   /suggest-topics             # Suggest topics for content
│   ├── POST   /suggest-related            # Find related content
│   ├── GET    /insights                   # Personal knowledge insights
│   ├── GET    /trends                     # Topic trends over time
│   └── POST   /ask                        # Ask questions about your knowledge base
│
├── knowledge-graph/
│   ├── GET    /topics                     # All topics with connections
│   ├── GET    /graph                      # Full graph data (nodes + edges)
│   ├── GET    /topics/{id}/related        # Related topics
│   └── POST   /topics/merge              # Merge similar topics
│
├── analytics/
│   ├── GET    /dashboard                  # Overview stats
│   ├── GET    /reading-stats              # Reading habits
│   ├── GET    /topic-distribution         # Content by topic
│   └── GET    /activity-heatmap           # Activity over time
│
├── extension/
│   ├── POST   /heartbeat                 # Extension health check
│   ├── POST   /quick-bookmark            # One-click save from extension
│   ├── POST   /highlight                 # Save highlight from extension
│   ├── GET    /page-data                 # Get user's data for current page
│   ├── POST   /screenshot                # Upload screenshot
│   └── GET    /settings                  # Get extension settings
│
└── settings/
    ├── GET    /profile                    # Get profile
    ├── PUT    /profile                    # Update profile
    ├── PUT    /password                   # Change password
    ├── GET    /sessions                   # Active sessions
    ├── DELETE /sessions/{id}              # Revoke session
    ├── GET    /tokens                     # API tokens
    ├── POST   /tokens                     # Create token
    ├── DELETE /tokens/{id}                # Revoke token
    ├── PUT    /preferences                # Update preferences
    ├── GET    /export                     # Export all data
    └── DELETE /account                    # Delete account
```

### Service Layer Pattern

```php
// Example: BookmarkService.php
app/
├── Http/
│   ├── Controllers/Api/V1/
│   │   ├── BookmarkController.php
│   │   ├── CollectionController.php
│   │   ├── NoteController.php
│   │   ├── HighlightController.php
│   │   ├── SearchController.php
│   │   └── ...
│   ├── Requests/
│   │   ├── StoreBookmarkRequest.php
│   │   ├── UpdateBookmarkRequest.php
│   │   └── ...
│   └── Resources/
│       ├── BookmarkResource.php
│       ├── BookmarkCollection.php
│       └── ...
├── Services/
│   ├── BookmarkService.php          // Business logic
│   ├── ScraperService.php           // URL metadata extraction
│   ├── AIService.php                // AI integration layer
│   ├── SearchService.php            // Meilisearch integration
│   ├── ScreenshotService.php        // Screenshot capture
│   └── ImportExportService.php      // Import/Export logic
├── Jobs/
│   ├── ScrapeBookmarkMetadata.php   // Async metadata fetching
│   ├── GenerateAISummary.php        // AI summary generation
│   ├── GenerateEmbedding.php        // Vector embedding generation
│   ├── ProcessImport.php            // Bulk import processing
│   ├── CaptureScreenshot.php        // Screenshot capture
│   └── SyncToMeilisearch.php        // Search index sync
├── Models/
│   ├── User.php
│   ├── Bookmark.php
│   ├── Collection.php
│   ├── Note.php
│   ├── Highlight.php
│   ├── Tag.php
│   ├── Topic.php
│   ├── ResearchProject.php
│   ├── Task.php
│   ├── Team.php
│   └── ...
├── Policies/
│   ├── BookmarkPolicy.php
│   ├── CollectionPolicy.php
│   ├── NotePolicy.php
│   └── TeamPolicy.php
└── Events/
    ├── BookmarkCreated.php
    ├── NoteUpdated.php
    ├── HighlightCreated.php
    └── ...
```

---

## 7. Chrome Extension Architecture

### Extension Structure

```
chrome-extension/
├── manifest.json                    # Manifest V3 configuration
├── package.json                     # Build dependencies
├── vite.config.ts                   # Vite build config
├── tailwind.config.js               # Tailwind configuration
│
├── src/
│   ├── background/
│   │   ├── service-worker.ts        # Background service worker
│   │   ├── api-client.ts            # REST API communication
│   │   ├── auth-manager.ts          # Token management & refresh
│   │   ├── context-menu.ts          # Right-click context menus
│   │   ├── badge-manager.ts         # Extension badge updates
│   │   └── storage-manager.ts       # chrome.storage wrapper
│   │
│   ├── content/
│   │   ├── content-script.ts        # Main content script entry
│   │   ├── highlighter.ts           # Text highlighting engine
│   │   ├── highlight-renderer.ts    # Render saved highlights on page
│   │   ├── selection-tooltip.ts     # Floating toolbar on text select
│   │   ├── sidebar.ts               # Injected sidebar component
│   │   └── screenshot.ts            # Page screenshot utility
│   │
│   ├── popup/
│   │   ├── index.html               # Popup HTML entry
│   │   ├── Popup.tsx                 # Main popup component (Preact)
│   │   ├── QuickSave.tsx            # Quick bookmark save
│   │   ├── RecentBookmarks.tsx      # Recent bookmarks list
│   │   ├── QuickNote.tsx            # Quick note composer
│   │   └── Settings.tsx             # Extension settings
│   │
│   ├── sidebar/
│   │   ├── index.html               # Sidebar HTML entry
│   │   ├── Sidebar.tsx              # Main sidebar panel
│   │   ├── HighlightList.tsx        # Page highlights list
│   │   ├── NoteEditor.tsx           # Inline note editor
│   │   └── BookmarkDetails.tsx      # Bookmark details view
│   │
│   ├── options/
│   │   ├── index.html               # Options page HTML
│   │   └── Options.tsx              # Extension options/settings
│   │
│   ├── shared/
│   │   ├── types.ts                 # TypeScript interfaces
│   │   ├── constants.ts             # Shared constants
│   │   ├── utils.ts                 # Utility functions
│   │   └── styles/
│   │       └── content.css          # Injected styles (highlights, tooltip)
│   │
│   └── assets/
│       ├── icons/                   # Extension icons (16/32/48/128)
│       └── images/                  # UI images
│
├── dist/                            # Built extension output
└── tests/                           # Extension tests
```

### manifest.json

```json
{
  "manifest_version": 3,
  "name": "BrainVault - Smart Bookmarks & Notes",
  "version": "1.0.0",
  "description": "Save bookmarks, highlight text, take notes, and build your second brain",
  "permissions": [
    "activeTab",
    "storage",
    "contextMenus",
    "sidePanel",
    "tabs",
    "notifications"
  ],
  "optional_permissions": [
    "bookmarks",
    "history"
  ],
  "host_permissions": [
    "<all_urls>"
  ],
  "background": {
    "service_worker": "src/background/service-worker.ts",
    "type": "module"
  },
  "content_scripts": [
    {
      "matches": ["<all_urls>"],
      "js": ["src/content/content-script.ts"],
      "css": ["src/shared/styles/content.css"],
      "run_at": "document_idle"
    }
  ],
  "action": {
    "default_popup": "src/popup/index.html",
    "default_icon": {
      "16": "src/assets/icons/icon-16.png",
      "32": "src/assets/icons/icon-32.png",
      "48": "src/assets/icons/icon-48.png",
      "128": "src/assets/icons/icon-128.png"
    }
  },
  "side_panel": {
    "default_path": "src/sidebar/index.html"
  },
  "options_page": "src/options/index.html",
  "icons": {
    "16": "src/assets/icons/icon-16.png",
    "32": "src/assets/icons/icon-32.png",
    "48": "src/assets/icons/icon-48.png",
    "128": "src/assets/icons/icon-128.png"
  }
}
```

### Extension Features Matrix

| Feature | Popup | Sidebar | Content Script | Background |
|---------|-------|---------|----------------|------------|
| Quick bookmark save | X | | | X |
| Add note to bookmark | X | X | | |
| Highlight text | | | X | X |
| View highlights | | X | X | |
| Screenshot capture | X | | | X |
| Search bookmarks | X | X | | |
| Quick note | X | X | | |
| Right-click save | | | | X |
| Keyboard shortcuts | | | X | X |
| Offline queue | | | | X |

### Extension ↔ Backend Sync Flow

```
Extension Action (e.g., highlight text)
    ↓
Content Script captures highlight data
    ↓
Message → Service Worker
    ↓
Service Worker:
├── Save to IndexedDB (immediate local cache)
├── POST /api/v1/extension/highlight
│   ├── Success → Update local cache with server ID
│   └── Failure → Queue for retry (exponential backoff)
└── Update badge count
    ↓
Dashboard receives real-time update via WebSocket
```

### Keyboard Shortcuts

| Shortcut | Action |
|----------|--------|
| `Alt + B` | Quick bookmark current page |
| `Alt + H` | Toggle highlight mode |
| `Alt + N` | Open quick note popup |
| `Alt + S` | Open sidebar panel |
| `Alt + Shift + S` | Take screenshot |
| `Alt + F` | Search bookmarks |
| `Escape` | Close popup/sidebar |

---

## 8. Frontend UI/UX Design

### Design System

```
BRAINVAULT DESIGN SYSTEM
═══════════════════════════

COLORS:
├── Primary:     #6366F1 (Indigo 500)  — Main brand color
├── Secondary:   #8B5CF6 (Violet 500)  — Accents
├── Success:     #10B981 (Emerald 500)
├── Warning:     #F59E0B (Amber 500)
├── Danger:      #EF4444 (Red 500)
├── Info:        #3B82F6 (Blue 500)
│
├── Background:
│   ├── Light:   #FAFAFE → #F1F5F9
│   └── Dark:    #0F172A → #1E293B
│
├── Surface:
│   ├── Light:   #FFFFFF (cards, modals)
│   └── Dark:    #1E293B
│
├── Text:
│   ├── Light:   #0F172A (primary) / #475569 (secondary) / #94A3B8 (muted)
│   └── Dark:    #F8FAFC (primary) / #CBD5E1 (secondary) / #64748B (muted)
│
└── Highlight Colors:
    ├── Yellow:  #FBBF24
    ├── Green:   #34D399
    ├── Blue:    #60A5FA
    ├── Pink:    #F472B6
    └── Purple:  #A78BFA

TYPOGRAPHY:
├── Headings:    Inter (700/600)
├── Body:        Inter (400/500)
├── Monospace:   JetBrains Mono
└── Scale:       text-xs(12) → text-sm(14) → text-base(16) → text-lg(18) →
                 text-xl(20) → text-2xl(24) → text-3xl(30) → text-4xl(36)

SPACING:
└── 4px grid system (Tailwind default)

BORDER RADIUS:
├── Small:       rounded (4px)  — buttons, inputs
├── Medium:      rounded-lg (8px) — cards
├── Large:       rounded-xl (12px) — modals, panels
└── Full:        rounded-full — avatars, badges

SHADOWS:
├── sm:    0 1px 2px rgba(0,0,0,0.05)     — subtle elevation
├── md:    0 4px 6px rgba(0,0,0,0.1)      — cards
├── lg:    0 10px 15px rgba(0,0,0,0.1)    — dropdowns
└── xl:    0 20px 25px rgba(0,0,0,0.1)    — modals

ANIMATIONS:
├── Duration:    150ms (micro) / 300ms (standard) / 500ms (emphasis)
├── Easing:      ease-in-out (default), spring for interactions
└── Types:       Fade, slide, scale, skeleton loading
```

### Dashboard Layout

```
┌──────────────────────────────────────────────────────────────────────────┐
│  TOPBAR (sticky)                                                         │
│  ┌────┐  BrainVault    [═══════ Search ═══════]  🔔  🌙  [Avatar ▼]   │
│  │Logo│                                                                  │
│  └────┘                                                                  │
├──────────┬───────────────────────────────────────────────────────────────┤
│          │                                                               │
│ SIDEBAR  │  MAIN CONTENT AREA                                           │
│ (240px)  │                                                               │
│          │  ┌─────────────────────────────────────────────────────────┐  │
│ ┌──────┐ │  │  PAGE HEADER                                           │  │
│ │🏠 Dash│ │  │  Bookmarks (248)          [+ Add] [Filter▼] [Sort▼]  │  │
│ │📑 Book│ │  │                                                       │  │
│ │📝 Note│ │  ├─────────────────────────────────────────────────────────┤  │
│ │🔦 High│ │  │                                                       │  │
│ │📁 Coll│ │  │  CONTENT GRID / LIST VIEW                            │  │
│ │🔬 Rese│ │  │                                                       │  │
│ │✅ Task│ │  │  ┌──────────┐ ┌──────────┐ ┌──────────┐            │  │
│ │📊 Anal│ │  │  │ Bookmark │ │ Bookmark │ │ Bookmark │            │  │
│ │       │ │  │  │ Card     │ │ Card     │ │ Card     │            │  │
│ │───────│ │  │  │          │ │          │ │          │            │  │
│ │🏷 Tags │ │  │  │ Title    │ │ Title    │ │ Title    │            │  │
│ │       │ │  │  │ Preview  │ │ Preview  │ │ Preview  │            │  │
│ │COLLECT│ │  │  │ Tags     │ │ Tags     │ │ Tags     │            │  │
│ │├ Work │ │  │  │ AI Badge │ │ AI Badge │ │ AI Badge │            │  │
│ │├ Learn│ │  │  └──────────┘ └──────────┘ └──────────┘            │  │
│ │├ Read │ │  │                                                       │  │
│ │└ Dev  │ │  │  ┌──────────┐ ┌──────────┐ ┌──────────┐            │  │
│ │       │ │  │  │ Bookmark │ │ Bookmark │ │ Bookmark │            │  │
│ │───────│ │  │  │ Card     │ │ Card     │ │ Card     │            │  │
│ │👥Teams│ │  │  └──────────┘ └──────────┘ └──────────┘            │  │
│ │⚙ Sett│ │  │                                                       │  │
│ └──────┘ │  │  [Load More / Infinite Scroll]                        │  │
│          │  └─────────────────────────────────────────────────────────┘  │
│          │                                                               │
└──────────┴───────────────────────────────────────────────────────────────┘
```

### Key UI Pages

| # | Page | Description |
|---|------|-------------|
| 1 | **Dashboard** | Activity feed, stats widgets, recent bookmarks, AI insights cards, reading streak |
| 2 | **Bookmarks** | Grid/list view, filter by collection/tag/type/date, bulk actions, quick preview |
| 3 | **Bookmark Detail** | Full page view: metadata, AI summary, highlights, notes, related content |
| 4 | **Notes** | Masonry grid of note cards, rich text editor (Tiptap), note types (note/todo/journal) |
| 5 | **Note Editor** | Full-screen distraction-free editor, markdown support, auto-save, word count |
| 6 | **Highlights** | Timeline view of all highlights, grouped by page, color filter |
| 7 | **Collections** | Drag-drop collection management, nested folders, cover images, sharing |
| 8 | **Research Projects** | Kanban-style project boards, drag items, progress tracking |
| 9 | **Tasks** | Todo list with priorities, due dates, linked bookmarks |
| 10 | **Knowledge Graph** | Interactive D3.js visualization, clickable topic nodes, zoom/pan |
| 11 | **Search Results** | Unified search across all content types, faceted filters |
| 12 | **Analytics** | Charts: reading stats, topic distribution, activity heatmap, streaks |
| 13 | **Team Dashboard** | Team activity, shared collections, member management |
| 14 | **Import/Export** | Import from Chrome/Firefox/Pocket/Raindrop, export options |
| 15 | **Settings** | Profile, security, preferences, tokens, billing, integrations |
| 16 | **Onboarding** | Step-by-step setup wizard: install extension, import bookmarks, set interests |

### Bookmark Card Component Design

```
┌──────────────────────────────┐
│  ┌────────────────────────┐  │
│  │                        │  │    OG Image / Screenshot Preview
│  │    Preview Image       │  │    (aspect ratio 16:9)
│  │                        │  │
│  └────────────────────────┘  │
│                              │
│  📰 article · 5 min read    │    Content type badge + reading time
│                              │
│  Article Title Goes Here     │    Title (2 line clamp)
│  That Can Be Two Lines...    │
│                              │
│  Brief description text      │    Description (2 line clamp)
│  that shows a preview...     │
│                              │
│  ┌─────┐ ┌────┐ ┌──────┐   │
│  │React│ │CSS │ │Design│   │    Tags (max 3 visible + "+2")
│  └─────┘ └────┘ └──────┘   │
│                              │
│  ─────────────────────────── │
│  🌐 medium.com   ♥ ☐ ⋯     │    Favicon + domain + actions
│  2 days ago                  │    (favorite, archive, more)
│                              │
│  ┌──────────────────────┐   │
│  │ 🤖 AI: This article  │   │    AI Summary badge (collapsible)
│  │ covers advanced...    │   │
│  └──────────────────────┘   │
└──────────────────────────────┘
```

---

## 9. Landing Page Specification

### Landing Page Structure

```
LANDING PAGE SECTIONS (Top → Bottom)
═══════════════════════════════════════

1. HERO SECTION
   ├── Navigation bar (transparent → solid on scroll)
   │   ├── Logo + "BrainVault"
   │   ├── Features | Pricing | Blog | Docs
   │   └── [Login] [Get Started Free →]
   │
   ├── Headline: "Your Second Brain for the Web"
   ├── Subheadline: "Save, highlight, annotate, and organize everything
   │                 you read online. Powered by AI."
   ├── CTA Buttons: [Start Free →] [Watch Demo ▶]
   ├── Social proof: "Trusted by 10,000+ researchers & knowledge workers"
   ├── Hero illustration: Dashboard preview with floating extension popup
   └── Floating elements: bookmark cards, highlight snippets (parallax)

2. TRUSTED BY / LOGOS BAR
   └── Company/university logos (grayscale → color on hover)

3. FEATURES GRID (3 columns × 2 rows)
   ├── 🔖 One-Click Bookmarks — "Save any page instantly with our Chrome extension"
   ├── 🤖 AI Summaries — "Get instant summaries and keyword extraction"
   ├── 🔦 Web Highlights — "Highlight text on any website, synced to your dashboard"
   ├── 🧠 Knowledge Graph — "Visualize connections between your saved knowledge"
   ├── 🔍 Smart Search — "Find anything across notes, highlights, and bookmarks"
   └── 👥 Team Sharing — "Collaborate with your team on shared knowledge bases"

4. FEATURE SHOWCASE (alternating left-right sections with screenshots)
   ├── Section A: "Capture Everything" — Extension demo animation
   ├── Section B: "AI-Powered Organization" — Auto-categorization demo
   ├── Section C: "Highlight & Annotate" — Web highlighting demo
   └── Section D: "Visual Knowledge Graph" — Interactive graph preview

5. HOW IT WORKS (3-step process)
   ├── Step 1: "Install the Extension" — Install Chrome extension
   ├── Step 2: "Save & Highlight" — Bookmark, highlight, annotate
   └── Step 3: "Discover Connections" — AI organizes your knowledge

6. EXTENSION SHOWCASE
   ├── Chrome extension popup preview (animated)
   ├── Sidebar panel preview
   ├── Highlighting demo (animated GIF/video)
   └── [Add to Chrome — It's Free]

7. TESTIMONIALS
   ├── 3 testimonial cards with avatars
   ├── Star ratings
   └── Role/company of reviewer

8. PRICING TABLE
   ├── Free Plan:
   │   ├── 100 bookmarks, 50 notes, 20 highlights/day
   │   ├── Basic search, 3 collections
   │   └── Chrome extension
   │
   ├── Pro Plan ($8/mo):
   │   ├── Unlimited bookmarks, notes, highlights
   │   ├── AI summaries & smart suggestions
   │   ├── Knowledge graph, advanced search
   │   ├── Research projects, priority support
   │   └── Import/export, API access
   │
   └── Team Plan ($15/user/mo):
       ├── Everything in Pro
       ├── Team workspaces, shared collections
       ├── Admin dashboard, role management
       ├── SSO integration, audit logs
       └── Dedicated support

9. FAQ SECTION
   └── Accordion-style common questions

10. FINAL CTA
    ├── "Start Building Your Second Brain Today"
    ├── [Get Started Free →]
    └── "No credit card required. Free forever plan available."

11. FOOTER
    ├── Logo + tagline
    ├── Links: Product | Company | Resources | Legal
    ├── Social media icons
    └── © 2024 BrainVault. All rights reserved.
```

### Landing Page Technical Specs

| Aspect | Specification |
|--------|--------------|
| **Framework** | Blade template with Alpine.js for interactions |
| **Animations** | AOS (Animate on Scroll) + CSS animations + Lottie for hero |
| **Performance** | Target 95+ Lighthouse score, lazy-load images, critical CSS inline |
| **Responsive** | Mobile-first: 320px → 768px → 1024px → 1280px → 1536px |
| **SEO** | Meta tags, Open Graph, JSON-LD structured data, sitemap.xml |
| **Analytics** | Google Analytics 4 + Plausible (privacy-friendly) |
| **A/B Testing** | Split test CTA copy and pricing layouts |

---

## 10. AI Integration & Smart Features

### AI Pipeline Architecture

```
User saves a bookmark
    │
    ├──→ Queue: ScrapeBookmarkMetadata
    │     ├── Fetch page HTML (Guzzle + headless Chrome for SPAs)
    │     ├── Extract title, description, favicon, OG data
    │     ├── Extract main content (Readability algorithm)
    │     └── Detect content type (article/video/pdf/tweet)
    │
    ├──→ Queue: GenerateAISummary (after scrape completes)
    │     ├── Send extracted content to OpenAI GPT-4o
    │     ├── Generate summary (150 words)
    │     ├── Extract keywords (5-10)
    │     ├── Suggest category/topic
    │     └── Estimate reading time
    │
    ├──→ Queue: GenerateEmbedding
    │     ├── Generate text embedding (text-embedding-3-small)
    │     ├── Store in pgvector column
    │     └── Find & link similar existing bookmarks
    │
    ├──→ Queue: AssignTopics
    │     ├── Match against existing user topics
    │     ├── Create new topics if needed
    │     └── Update knowledge graph connections
    │
    └──→ Queue: SyncToMeilisearch
          └── Index bookmark in Meilisearch for full-text search
```

### AI Features Breakdown

| Feature | AI Model | Input | Output |
|---------|----------|-------|--------|
| **Auto Summary** | GPT-4o | Page content | 150-word summary |
| **Keyword Extraction** | GPT-4o | Page content | 5-10 keywords with relevance scores |
| **Topic Assignment** | Embeddings + clustering | Content embedding | Matched/new topics |
| **Related Content** | pgvector similarity | Content embedding | Top 5 similar items |
| **Smart Search** | Embeddings | Search query | Semantic search results |
| **Research Suggestions** | GPT-4o | User's topic trends | Next research suggestions |
| **Note Enhancement** | GPT-4o | User's note | Grammar fix, expansion, formatting |
| **Ask Your Knowledge** | GPT-4o + RAG | User question + relevant docs | Cited answer |
| **Trend Analysis** | Time-series analysis | Bookmark dates + topics | Topic trends over time |
| **OCR** | Tesseract | Screenshot image | Extracted text |

### "Ask Your Knowledge Base" (RAG Pipeline)

```
User asks: "What have I learned about React Server Components?"
    │
    ├── 1. Generate query embedding
    │
    ├── 2. Vector similarity search (pgvector)
    │      → Find top 20 most relevant bookmarks, notes, highlights
    │
    ├── 3. Re-rank with cross-encoder (optional)
    │      → Narrow to top 5 most relevant
    │
    ├── 4. Build prompt with context
    │      → System: "Answer based on the user's saved knowledge..."
    │      → Context: [5 relevant passages with source URLs]
    │      → Question: "What have I learned about React Server Components?"
    │
    ├── 5. Generate answer (GPT-4o)
    │      → Cited, referenced answer
    │
    └── 6. Return answer with source links
           → "Based on your bookmarks and notes, here's what you've saved
              about React Server Components: [answer with [1][2][3] citations]"
```

---

## 11. Knowledge Graph & Search

### Knowledge Graph Visualization

```
Interactive D3.js Force-Directed Graph
═══════════════════════════════════════

                    ┌──────────┐
               ┌────│  React   │────┐
               │    └──────────┘    │
               │         │          │
          ┌────▼───┐    │    ┌─────▼─────┐
          │Next.js │    │    │TypeScript │
          └────┬───┘    │    └─────┬─────┘
               │   ┌────▼───┐     │
               └───│Frontend│─────┘
                   └────┬───┘
                        │
                   ┌────▼───┐
                   │  Web   │
                   │  Dev   │
                   └────┬───┘
                        │
              ┌─────────┼──────────┐
              │         │          │
         ┌────▼──┐ ┌───▼────┐ ┌──▼─────┐
         │  CSS  │ │  Node  │ │ DevOps │
         └───────┘ └────────┘ └────────┘

Node Sizes  = proportional to bookmark count
Edge Width  = connection strength (0-1)
Colors      = topic category
Hover       = show bookmark count + description
Click       = filter dashboard by topic
Drag        = rearrange graph
Zoom        = focus on subgraph
```

### Search Architecture

```
┌────────────────────────────────────────────────┐
│                SEARCH LAYER                     │
│                                                 │
│  User Query: "react hooks performance"          │
│                                                 │
│  ┌──────────────────────────────────────────┐  │
│  │         PARALLEL SEARCH ENGINES          │  │
│  │                                          │  │
│  │  ┌──────────────┐  ┌──────────────────┐ │  │
│  │  │ Meilisearch  │  │  pgvector        │ │  │
│  │  │ (Full-text)  │  │  (Semantic)      │ │  │
│  │  │              │  │                  │ │  │
│  │  │ Typo-tolerant│  │  Embedding-based │ │  │
│  │  │ Faceted      │  │  Similarity      │ │  │
│  │  │ Highlighted  │  │  Cross-lingual   │ │  │
│  │  └──────┬───────┘  └────────┬─────────┘ │  │
│  │         │                   │            │  │
│  │         ▼                   ▼            │  │
│  │  ┌──────────────────────────────────┐   │  │
│  │  │      RESULT FUSION (RRF)         │   │  │
│  │  │  Reciprocal Rank Fusion          │   │  │
│  │  │  + Recency boost                 │   │  │
│  │  │  + User preference weights       │   │  │
│  │  └──────────────┬───────────────────┘   │  │
│  │                 │                        │  │
│  └─────────────────┼────────────────────────┘  │
│                    ▼                            │
│  ┌──────────────────────────────────────────┐  │
│  │  UNIFIED RESULTS                         │  │
│  │  ├── Bookmarks (with highlighted matches)│  │
│  │  ├── Notes (with context snippets)       │  │
│  │  ├── Highlights (with source page)       │  │
│  │  └── AI Summaries (with relevance score) │  │
│  └──────────────────────────────────────────┘  │
└────────────────────────────────────────────────┘
```

---

## 12. Team & Collaboration Features

### Collaboration Architecture

```
TEAM FEATURES
═════════════

Roles & Permissions:
├── Owner   — Full control, billing, delete team
├── Admin   — Manage members, manage all collections
├── Editor  — Add/edit content in shared collections
└── Viewer  — Read-only access to shared collections

Sharing Model:
├── Collection-level sharing (share entire folders)
├── Individual bookmark sharing (share links)
├── Public collection pages (SEO-friendly)
└── Team workspace (shared dashboard)

Real-time Features (via Laravel Reverb):
├── Live cursor presence (who's viewing what)
├── Real-time comment threads
├── Collaborative highlighting (see team highlights)
├── Activity feed (instant updates)
└── Notification system (in-app + email digest)
```

### Collaboration Notification System

```
NOTIFICATION TYPES:
├── team.member.joined          — "Alex joined your team"
├── collection.shared           — "Sarah shared 'React Resources' with you"
├── bookmark.commented          — "Mike commented on your bookmark"
├── highlight.shared            — "Lisa highlighted text in 'API Design Guide'"
├── team.collection.updated     — "New bookmarks added to 'Design System'"
├── weekly.digest               — Weekly email summary of team activity
└── ai.insight                  — "You have 15 unread bookmarks about TypeScript"

CHANNELS:
├── In-app (real-time via WebSocket)
├── Browser notification (via extension)
├── Email (immediate + daily/weekly digest)
└── Mobile push (future - PWA)
```

---

## 13. Phased Development Roadmap

### Phase 1: Foundation & MVP (Weeks 1-4)

```
WEEK 1: Project Setup & Core Backend
═══════════════════════════════════════
□ Laravel 11 project scaffolding
□ Docker Compose setup (PostgreSQL, Redis, Meilisearch, MinIO)
□ Database migrations (users, bookmarks, collections, tags)
□ Sanctum authentication (register, login, logout, email verify)
□ Social OAuth (Google, GitHub) via Laravel Socialite
□ User model + profile management
□ Base Blade layout with Tailwind CSS
□ Design system setup (colors, typography, components)

WEEK 2: Bookmark Core + UI
═══════════════════════════
□ Bookmark CRUD (create, read, update, delete)
□ URL metadata scraper service (title, description, favicon, OG image)
□ Collection CRUD with nested folders
□ Tag system (polymorphic taggable)
□ Bookmark card component (Blade/Livewire)
□ Grid & list view toggle
□ Pagination + infinite scroll
□ Filter & sort controls (by date, type, collection, tag)
□ Bulk actions (archive, delete, move, tag)
□ Responsive dashboard layout

WEEK 3: Notes + Highlights Core
════════════════════════════════
□ Note CRUD with rich text editor (Tiptap)
□ Note types (note, todo, journal, snippet)
□ Markdown support with live preview
□ Note-bookmark linking
□ Highlight model + API endpoints
□ Highlight list view on dashboard
□ Trash & restore functionality
□ Note version history (basic)

WEEK 4: Auth UI + Landing Page
═══════════════════════════════
□ Beautiful auth pages (register, login, forgot password, reset)
  ├── Split layout with brand panel
  ├── Social login buttons (prominent)
  ├── Inline validation + password strength meter
  └── Responsive mobile layout
□ Email verification flow with branded template
□ Profile settings page
□ Security settings (password change, sessions)
□ Landing page (all sections from spec above)
□ SEO optimization (meta tags, sitemap, structured data)
□ Basic onboarding wizard (3-step)
```

### Phase 2: Chrome Extension + Search (Weeks 5-8)

```
WEEK 5: Extension - Foundation
══════════════════════════════
□ Chrome extension project setup (Manifest V3 + Vite + Preact)
□ Extension auth flow (token-based, login via web app)
□ Popup UI: quick bookmark save
□ Background service worker: API client
□ Context menu: "Save to BrainVault"
□ Extension settings/options page

WEEK 6: Extension - Highlighting & Sidebar
═══════════════════════════════════════════
□ Text highlighting engine (DOM selection → XPath serialization)
□ Highlight color picker (floating tooltip on selection)
□ Render saved highlights on page revisit
□ Sidebar panel: view highlights + notes for current page
□ Quick note composer in sidebar
□ Screenshot capture (full page + selected area)

WEEK 7: Search & Import
════════════════════════
□ Meilisearch integration (Scout driver)
□ Universal search (bookmarks, notes, highlights)
□ Search UI with faceted filters
□ Search suggestions / autocomplete
□ Import from Chrome bookmarks (HTML parser)
□ Import from Firefox, Pocket, Raindrop
□ Export (HTML, JSON, CSV)
□ API token management UI

WEEK 8: Extension Polish + Sync
════════════════════════════════
□ Offline support (IndexedDB cache + sync queue)
□ Real-time sync (extension ↔ dashboard)
□ Keyboard shortcuts
□ Extension badge (unread count)
□ Browser notification integration
□ Extension store preparation (assets, description, screenshots)
□ End-to-end testing (extension + backend)
```

### Phase 3: AI & Smart Features (Weeks 9-12)

```
WEEK 9: AI Foundation
═════════════════════
□ OpenAI API integration service
□ AI summary generation (queue job)
□ Keyword extraction
□ Category suggestion
□ AI processing status UI (loading states, badges)

WEEK 10: Embeddings & Semantic Search
═════════════════════════════════════
□ pgvector extension setup
□ Embedding generation pipeline
□ Semantic search endpoint
□ "Related bookmarks" feature
□ Hybrid search (full-text + semantic fusion)

WEEK 11: Knowledge Graph
════════════════════════
□ Topic model + auto-assignment
□ Topic connection calculation
□ D3.js force-directed graph visualization
□ Interactive graph UI (hover, click, zoom, drag)
□ Topic management (merge, rename, delete)

WEEK 12: Smart Features
═══════════════════════
□ "Ask Your Knowledge Base" (RAG pipeline)
□ Research suggestions
□ Reading insights & trends
□ Analytics dashboard (charts, heatmaps, streaks)
□ AI-powered note enhancement
□ Weekly insight email digest
```

### Phase 4: Collaboration & Polish (Weeks 13-16)

```
WEEK 13: Teams & Sharing
═════════════════════════
□ Team model + CRUD
□ Member invitation system
□ Role-based permissions
□ Collection sharing (team-level)
□ Public collection pages

WEEK 14: Real-time Collaboration
═════════════════════════════════
□ Laravel Reverb (WebSocket) setup
□ Real-time comment threads
□ Live activity feed
□ Notification system (in-app + email)
□ Team dashboard

WEEK 15: Research & Productivity
═════════════════════════════════
□ Research project boards (Kanban)
□ Task management (linked to bookmarks)
□ Reading progress tracking
□ Learning streak system
□ Goal setting & tracking

WEEK 16: Final Polish & Launch
══════════════════════════════
□ Performance optimization (caching, lazy loading, query optimization)
□ Accessibility audit (WCAG 2.1 AA)
□ Security audit (OWASP top 10)
□ Mobile responsive final pass
□ Error handling & edge cases
□ Documentation (API docs, user guide)
□ Chrome Web Store submission
□ Production deployment
□ Launch 🚀
```

### Phase 5: Future Enhancements (Post-Launch)

```
FUTURE FEATURES (Prioritized):
├── 🔲 Firefox extension
├── 🔲 Safari extension
├── 🔲 Mobile PWA (installable)
├── 🔲 PDF annotation & highlight
├── 🔲 Video bookmark with timestamp notes
├── 🔲 Social media post capture (Twitter, LinkedIn)
├── 🔲 RSS feed integration
├── 🔲 Zapier/Make.com integration
├── 🔲 API for third-party integrations
├── 🔲 Self-hosted option (Docker)
├── 🔲 AI "Digital Clone" assistant
├── 🔲 Spaced repetition (flashcards from highlights)
├── 🔲 Browser history analysis
├── 🔲 Readability mode with annotation
├── 🔲 Email newsletter capture
├── 🔲 Notion/Obsidian sync
├── 🔲 Mobile apps (React Native)
└── 🔲 Enterprise SSO (SAML/OIDC)
```

---

## 14. Directory Structure

### Laravel Project Structure

```
brainvault/
├── app/
│   ├── Actions/                     # Single-purpose action classes
│   │   ├── Bookmarks/
│   │   │   ├── CreateBookmark.php
│   │   │   ├── ScrapeMetadata.php
│   │   │   └── ImportBookmarks.php
│   │   ├── Notes/
│   │   ├── Highlights/
│   │   ├── AI/
│   │   │   ├── GenerateSummary.php
│   │   │   ├── ExtractKeywords.php
│   │   │   ├── GenerateEmbedding.php
│   │   │   └── AnswerQuestion.php
│   │   └── Teams/
│   │
│   ├── Console/
│   │   └── Commands/
│   │       ├── SyncSearchIndex.php
│   │       ├── ProcessAIQueue.php
│   │       ├── CleanupTrash.php
│   │       └── GenerateWeeklyDigest.php
│   │
│   ├── Events/
│   │   ├── BookmarkCreated.php
│   │   ├── BookmarkUpdated.php
│   │   ├── NoteCreated.php
│   │   ├── HighlightCreated.php
│   │   └── TeamMemberInvited.php
│   │
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/V1/               # API controllers
│   │   │   │   ├── AuthController.php
│   │   │   │   ├── BookmarkController.php
│   │   │   │   ├── CollectionController.php
│   │   │   │   ├── NoteController.php
│   │   │   │   ├── HighlightController.php
│   │   │   │   ├── TagController.php
│   │   │   │   ├── SearchController.php
│   │   │   │   ├── AIController.php
│   │   │   │   ├── KnowledgeGraphController.php
│   │   │   │   ├── ResearchController.php
│   │   │   │   ├── TaskController.php
│   │   │   │   ├── TeamController.php
│   │   │   │   ├── AnalyticsController.php
│   │   │   │   ├── ExtensionController.php
│   │   │   │   ├── ImportExportController.php
│   │   │   │   └── SettingsController.php
│   │   │   │
│   │   │   └── Web/                   # Web controllers (Livewire pages)
│   │   │       ├── DashboardController.php
│   │   │       ├── LandingPageController.php
│   │   │       └── SharedCollectionController.php
│   │   │
│   │   ├── Middleware/
│   │   │   ├── EnsureEmailIsVerified.php
│   │   │   ├── TrackActivity.php
│   │   │   └── CheckTeamPermission.php
│   │   │
│   │   ├── Requests/                  # Form request validation
│   │   │   ├── Bookmark/
│   │   │   │   ├── StoreBookmarkRequest.php
│   │   │   │   └── UpdateBookmarkRequest.php
│   │   │   ├── Note/
│   │   │   ├── Collection/
│   │   │   └── Team/
│   │   │
│   │   └── Resources/                 # API resources
│   │       ├── BookmarkResource.php
│   │       ├── NoteResource.php
│   │       ├── HighlightResource.php
│   │       ├── CollectionResource.php
│   │       ├── TagResource.php
│   │       ├── TopicResource.php
│   │       └── UserResource.php
│   │
│   ├── Jobs/
│   │   ├── ScrapeBookmarkMetadata.php
│   │   ├── GenerateAISummary.php
│   │   ├── GenerateEmbedding.php
│   │   ├── AssignTopics.php
│   │   ├── CaptureScreenshot.php
│   │   ├── ProcessImport.php
│   │   ├── SyncToMeilisearch.php
│   │   └── SendWeeklyDigest.php
│   │
│   ├── Listeners/
│   │   ├── ProcessNewBookmark.php
│   │   ├── UpdateSearchIndex.php
│   │   ├── LogActivity.php
│   │   └── SendNotification.php
│   │
│   ├── Livewire/                      # Livewire components
│   │   ├── Dashboard/
│   │   │   ├── ActivityFeed.php
│   │   │   ├── StatsWidgets.php
│   │   │   └── RecentBookmarks.php
│   │   ├── Bookmarks/
│   │   │   ├── BookmarkGrid.php
│   │   │   ├── BookmarkCard.php
│   │   │   ├── BookmarkDetail.php
│   │   │   └── BookmarkFilters.php
│   │   ├── Notes/
│   │   │   ├── NoteList.php
│   │   │   └── NoteEditor.php
│   │   ├── Collections/
│   │   │   └── CollectionTree.php
│   │   ├── Search/
│   │   │   └── UniversalSearch.php
│   │   ├── KnowledgeGraph/
│   │   │   └── GraphVisualization.php
│   │   └── Settings/
│   │       ├── ProfileForm.php
│   │       ├── SecuritySettings.php
│   │       └── TokenManager.php
│   │
│   ├── Models/
│   │   ├── User.php
│   │   ├── SocialAccount.php
│   │   ├── Bookmark.php
│   │   ├── Collection.php
│   │   ├── Note.php
│   │   ├── Highlight.php
│   │   ├── Tag.php
│   │   ├── Attachment.php
│   │   ├── Topic.php
│   │   ├── TopicConnection.php
│   │   ├── ResearchProject.php
│   │   ├── Task.php
│   │   ├── Team.php
│   │   ├── TeamMember.php
│   │   ├── SharedCollection.php
│   │   ├── Comment.php
│   │   ├── Activity.php
│   │   ├── ReadingStat.php
│   │   └── Import.php
│   │
│   ├── Notifications/
│   │   ├── WelcomeNotification.php
│   │   ├── TeamInviteNotification.php
│   │   ├── CollectionSharedNotification.php
│   │   ├── WeeklyDigestNotification.php
│   │   └── AIInsightNotification.php
│   │
│   ├── Observers/
│   │   ├── BookmarkObserver.php
│   │   ├── NoteObserver.php
│   │   └── HighlightObserver.php
│   │
│   ├── Policies/
│   │   ├── BookmarkPolicy.php
│   │   ├── CollectionPolicy.php
│   │   ├── NotePolicy.php
│   │   ├── HighlightPolicy.php
│   │   └── TeamPolicy.php
│   │
│   ├── Providers/
│   │   ├── AppServiceProvider.php
│   │   ├── AuthServiceProvider.php
│   │   └── EventServiceProvider.php
│   │
│   └── Services/
│       ├── BookmarkService.php
│       ├── ScraperService.php
│       ├── AIService.php
│       ├── EmbeddingService.php
│       ├── SearchService.php
│       ├── ScreenshotService.php
│       ├── ImportService.php
│       ├── ExportService.php
│       ├── KnowledgeGraphService.php
│       └── AnalyticsService.php
│
├── config/
│   ├── ai.php                         # AI service configuration
│   ├── scraper.php                    # Scraper configuration
│   └── brainvault.php                 # App-specific config
│
├── database/
│   ├── factories/
│   ├── migrations/
│   │   ├── 0001_create_users_table.php
│   │   ├── 0002_create_social_accounts_table.php
│   │   ├── 0003_create_bookmarks_table.php
│   │   ├── 0004_create_collections_table.php
│   │   ├── 0005_create_bookmark_collection_table.php
│   │   ├── 0006_create_tags_table.php
│   │   ├── 0007_create_taggables_table.php
│   │   ├── 0008_create_notes_table.php
│   │   ├── 0009_create_highlights_table.php
│   │   ├── 0010_create_attachments_table.php
│   │   ├── 0011_create_research_projects_table.php
│   │   ├── 0012_create_tasks_table.php
│   │   ├── 0013_create_teams_table.php
│   │   ├── 0014_create_topics_table.php
│   │   ├── 0015_create_activities_table.php
│   │   ├── 0016_create_reading_stats_table.php
│   │   ├── 0017_create_imports_table.php
│   │   └── 0018_enable_pgvector.php
│   └── seeders/
│       ├── DatabaseSeeder.php
│       ├── DemoUserSeeder.php
│       └── SampleDataSeeder.php
│
├── resources/
│   ├── css/
│   │   └── app.css                    # Tailwind imports
│   │
│   ├── js/
│   │   ├── app.js                     # Alpine.js + core JS
│   │   ├── knowledge-graph.js         # D3.js graph visualization
│   │   ├── note-editor.js             # Tiptap editor setup
│   │   └── charts.js                  # Chart.js dashboards
│   │
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php          # Main dashboard layout
│       │   ├── auth.blade.php         # Auth pages layout
│       │   └── landing.blade.php      # Landing page layout
│       │
│       ├── components/                # Blade components
│       │   ├── button.blade.php
│       │   ├── card.blade.php
│       │   ├── modal.blade.php
│       │   ├── dropdown.blade.php
│       │   ├── toast.blade.php
│       │   ├── empty-state.blade.php
│       │   ├── loading-skeleton.blade.php
│       │   └── ...
│       │
│       ├── auth/
│       │   ├── register.blade.php
│       │   ├── login.blade.php
│       │   ├── forgot-password.blade.php
│       │   ├── reset-password.blade.php
│       │   ├── verify-email.blade.php
│       │   └── two-factor-challenge.blade.php
│       │
│       ├── dashboard/
│       │   └── index.blade.php
│       │
│       ├── bookmarks/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       │
│       ├── notes/
│       │   ├── index.blade.php
│       │   └── editor.blade.php
│       │
│       ├── highlights/
│       │   └── index.blade.php
│       │
│       ├── collections/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       │
│       ├── research/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       │
│       ├── knowledge-graph/
│       │   └── index.blade.php
│       │
│       ├── analytics/
│       │   └── index.blade.php
│       │
│       ├── teams/
│       │   ├── index.blade.php
│       │   └── show.blade.php
│       │
│       ├── search/
│       │   └── results.blade.php
│       │
│       ├── settings/
│       │   ├── profile.blade.php
│       │   ├── security.blade.php
│       │   ├── preferences.blade.php
│       │   └── tokens.blade.php
│       │
│       ├── landing/
│       │   └── index.blade.php
│       │
│       ├── shared/
│       │   └── collection.blade.php   # Public shared collection page
│       │
│       └── emails/
│           ├── verify.blade.php
│           ├── welcome.blade.php
│           ├── team-invite.blade.php
│           └── weekly-digest.blade.php
│
├── routes/
│   ├── web.php                        # Web routes (dashboard, auth, landing)
│   ├── api.php                        # API v1 routes
│   ├── channels.php                   # WebSocket channels
│   └── console.php                    # Artisan commands
│
├── tests/
│   ├── Feature/
│   │   ├── Auth/
│   │   ├── Bookmark/
│   │   ├── Note/
│   │   ├── Collection/
│   │   ├── Search/
│   │   ├── AI/
│   │   └── Team/
│   └── Unit/
│       ├── Services/
│       └── Models/
│
├── docker/
│   ├── Dockerfile
│   ├── nginx/
│   │   └── default.conf
│   └── php/
│       └── php.ini
│
├── docker-compose.yml                 # Dev environment
├── docker-compose.prod.yml            # Production environment
│
├── .env.example
├── .github/
│   └── workflows/
│       ├── ci.yml                     # Tests + lint
│       └── deploy.yml                 # Auto-deploy
│
├── agent.md                           # This roadmap file
├── README.md                          # Project documentation
├── CONTRIBUTING.md                    # Contribution guidelines
└── LICENSE
```

### Chrome Extension Directory

```
chrome-extension/
├── manifest.json
├── package.json
├── vite.config.ts
├── tsconfig.json
├── tailwind.config.js
│
├── src/
│   ├── background/
│   │   ├── service-worker.ts
│   │   ├── api-client.ts
│   │   ├── auth-manager.ts
│   │   ├── context-menu.ts
│   │   ├── badge-manager.ts
│   │   └── storage-manager.ts
│   │
│   ├── content/
│   │   ├── content-script.ts
│   │   ├── highlighter.ts
│   │   ├── highlight-renderer.ts
│   │   ├── selection-tooltip.ts
│   │   ├── sidebar.ts
│   │   └── screenshot.ts
│   │
│   ├── popup/
│   │   ├── index.html
│   │   ├── Popup.tsx
│   │   ├── QuickSave.tsx
│   │   ├── RecentBookmarks.tsx
│   │   ├── QuickNote.tsx
│   │   └── Settings.tsx
│   │
│   ├── sidebar/
│   │   ├── index.html
│   │   ├── Sidebar.tsx
│   │   ├── HighlightList.tsx
│   │   ├── NoteEditor.tsx
│   │   └── BookmarkDetails.tsx
│   │
│   ├── options/
│   │   ├── index.html
│   │   └── Options.tsx
│   │
│   ├── shared/
│   │   ├── types.ts
│   │   ├── constants.ts
│   │   ├── utils.ts
│   │   └── styles/
│   │       └── content.css
│   │
│   └── assets/
│       ├── icons/
│       │   ├── icon-16.png
│       │   ├── icon-32.png
│       │   ├── icon-48.png
│       │   └── icon-128.png
│       └── images/
│
├── dist/
└── tests/
```

---

## 15. DevOps & Deployment

### Docker Compose (Development)

```yaml
# docker-compose.yml
services:
  app:
    build:
      context: .
      dockerfile: docker/Dockerfile
    ports:
      - "8000:8000"
    volumes:
      - .:/var/www/html
    depends_on:
      - postgres
      - redis
      - meilisearch
      - minio
    environment:
      - APP_ENV=local
      - DB_HOST=postgres
      - REDIS_HOST=redis
      - MEILISEARCH_HOST=http://meilisearch:7700
      - AWS_ENDPOINT=http://minio:9000

  postgres:
    image: pgvector/pgvector:pg16
    ports:
      - "5432:5432"
    environment:
      POSTGRES_DB: brainvault
      POSTGRES_USER: brainvault
      POSTGRES_PASSWORD: secret
    volumes:
      - postgres_data:/var/lib/postgresql/data

  redis:
    image: redis:7-alpine
    ports:
      - "6379:6379"

  meilisearch:
    image: getmeili/meilisearch:v1.7
    ports:
      - "7700:7700"
    environment:
      MEILI_MASTER_KEY: masterkey
    volumes:
      - meilisearch_data:/meili_data

  minio:
    image: minio/minio
    ports:
      - "9000:9000"
      - "9001:9001"
    environment:
      MINIO_ROOT_USER: brainvault
      MINIO_ROOT_PASSWORD: secretkey
    command: server /data --console-address ":9001"
    volumes:
      - minio_data:/data

  horizon:
    build:
      context: .
      dockerfile: docker/Dockerfile
    command: php artisan horizon
    depends_on:
      - redis

  reverb:
    build:
      context: .
      dockerfile: docker/Dockerfile
    command: php artisan reverb:start
    ports:
      - "8080:8080"
    depends_on:
      - redis

  scheduler:
    build:
      context: .
      dockerfile: docker/Dockerfile
    command: >
      sh -c "while true; do php artisan schedule:run; sleep 60; done"

volumes:
  postgres_data:
  meilisearch_data:
  minio_data:
```

### CI/CD Pipeline (GitHub Actions)

```yaml
# .github/workflows/ci.yml
name: CI Pipeline

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main]

jobs:
  test:
    runs-on: ubuntu-latest
    services:
      postgres:
        image: pgvector/pgvector:pg16
        env:
          POSTGRES_DB: brainvault_test
          POSTGRES_USER: test
          POSTGRES_PASSWORD: test
        ports:
          - 5432:5432
      redis:
        image: redis:7-alpine
        ports:
          - 6379:6379

    steps:
      - uses: actions/checkout@v4
      - uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          extensions: pdo_pgsql, redis, gd
      - run: composer install --no-interaction
      - run: cp .env.testing .env
      - run: php artisan key:generate
      - run: php artisan migrate
      - run: php artisan test --parallel
      - run: ./vendor/bin/pint --test
      - run: ./vendor/bin/phpstan analyse

  extension-test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      - uses: actions/setup-node@v4
        with:
          node-version: '20'
      - working-directory: chrome-extension
        run: |
          npm ci
          npm run lint
          npm run typecheck
          npm run test
          npm run build
```

---

## 16. Security Considerations

### Security Checklist

```
AUTHENTICATION & SESSION:
├── □ bcrypt password hashing (12+ rounds)
├── □ Rate limiting: 5 login attempts / minute / IP
├── □ CSRF protection on all forms
├── □ Secure session cookies (httpOnly, sameSite, secure)
├── □ Session timeout (8 hours idle, 30 days max)
├── □ Token expiration (API: 30 days, Extension: 90 days with refresh)
├── □ 2FA (TOTP) support
└── □ Account lockout after repeated failures

DATA PROTECTION:
├── □ All data encrypted at rest (PostgreSQL TDE / disk encryption)
├── □ All traffic over HTTPS (TLS 1.3)
├── □ Sanitize all user inputs (XSS prevention)
├── □ Parameterized queries (SQL injection prevention)
├── □ File upload validation (type, size, virus scan)
├── □ S3 pre-signed URLs for private files
└── □ PII encryption for sensitive fields

API SECURITY:
├── □ API rate limiting (60 requests/minute for free, 300 for pro)
├── □ CORS configuration (whitelist origins)
├── □ Token scoping (read/write/admin)
├── □ Request size limits
├── □ Input validation on all endpoints
└── □ Audit logging for sensitive operations

EXTENSION SECURITY:
├── □ Minimum permissions (Manifest V3)
├── □ Content Security Policy headers
├── □ No inline scripts in extension pages
├── □ Token stored in chrome.storage.local (not cookies)
└── □ Communication only with whitelisted domains
```

---

## 17. Performance & Scaling

### Performance Targets

| Metric | Target |
|--------|--------|
| **Dashboard load** | < 1.5s (FCP) |
| **API response** | < 200ms (p95) |
| **Search results** | < 100ms |
| **Extension popup** | < 300ms |
| **Lighthouse score** | 95+ (performance) |
| **Concurrent users** | 10,000+ |

### Caching Strategy

```
CACHING LAYERS:
├── Browser Cache
│   ├── Static assets: 1 year (with cache-busting hashes)
│   └── API responses: 5 minutes (stale-while-revalidate)
│
├── CDN (Cloudflare)
│   ├── Static assets: edge-cached globally
│   └── Landing page: 1 hour TTL
│
├── Application Cache (Redis)
│   ├── User preferences: 1 hour
│   ├── Collection tree: 30 minutes
│   ├── Tag list: 30 minutes
│   ├── Dashboard stats: 15 minutes
│   ├── Search suggestions: 10 minutes
│   └── AI summaries: permanent (until content changes)
│
└── Database Query Cache
    ├── Frequently accessed bookmarks: 5 minutes
    └── Knowledge graph data: 30 minutes
```

### Scaling Plan

```
PHASE 1 (0-5K users):
├── Single server (4GB RAM, 2 CPU)
├── PostgreSQL on same server
├── Redis on same server
└── Estimated cost: $20-40/month

PHASE 2 (5K-50K users):
├── Separate app server (8GB RAM, 4 CPU)
├── Managed PostgreSQL (dedicated)
├── Managed Redis cluster
├── Meilisearch on separate instance
├── S3 for file storage
├── Load balancer + CDN
└── Estimated cost: $150-300/month

PHASE 3 (50K+ users):
├── Horizontal app scaling (3+ instances)
├── PostgreSQL read replicas
├── Redis Cluster (6 nodes)
├── Meilisearch cluster
├── Queue workers on separate instances
├── CDN with full-page caching
└── Estimated cost: $500-1500/month
```

---

## 18. Future Vision

### Long-term Product Vision

```
YEAR 1: Core Platform
├── Web dashboard + Chrome extension
├── AI-powered bookmarks & notes
├── Knowledge graph
├── Team collaboration
└── Chrome Web Store launch

YEAR 2: Ecosystem Expansion
├── Firefox & Safari extensions
├── Mobile apps (iOS & Android via React Native)
├── Desktop app (Electron)
├── Plugin marketplace (community integrations)
├── Notion, Obsidian, Roam Research sync
├── API marketplace for developers
└── Enterprise tier with SSO & audit logs

YEAR 3: AI-First Platform
├── AI "Digital Clone" — personal knowledge assistant
├── AI-powered workflow automation
├── Predictive research suggestions
├── Auto-generated learning paths
├── Voice-first interaction (Alexa, Siri integration)
├── AR/VR knowledge visualization
└── Cross-platform universal capture (any app, any device)
```

### Revenue Model

```
FREE TIER (forever):
├── 100 bookmarks
├── 50 notes
├── 20 highlights/day
├── Basic search
├── 3 collections
└── Chrome extension

PRO ($8/month or $72/year):
├── Unlimited everything
├── AI features (summaries, keywords, semantic search)
├── Knowledge graph
├── Research projects
├── Import/export
├── API access
├── Priority support
└── Custom themes

TEAM ($15/user/month):
├── Everything in Pro
├── Team workspaces
├── Shared collections
├── Role-based access
├── Admin dashboard
├── Activity audit logs
└── Dedicated support

ENTERPRISE (custom pricing):
├── Everything in Team
├── SSO (SAML/OIDC)
├── Custom integrations
├── SLA guarantee (99.9%)
├── Dedicated instance option
├── On-premise deployment
└── Custom AI training
```

---

## Quick Start Guide (for development)

```bash
# 1. Clone the repo
git clone https://github.com/your-org/brainvault.git
cd brainvault

# 2. Copy environment file
cp .env.example .env

# 3. Start Docker services
docker compose up -d

# 4. Install PHP dependencies
composer install

# 5. Generate app key
php artisan key:generate

# 6. Run migrations + seeders
php artisan migrate --seed

# 7. Install Node dependencies & build
npm install && npm run dev

# 8. Start Horizon (queue worker)
php artisan horizon

# 9. Start Reverb (WebSockets)
php artisan reverb:start

# 10. Access the app
# Dashboard: http://localhost:8000
# Horizon:   http://localhost:8000/horizon
# Meilisearch: http://localhost:7700
# MinIO:     http://localhost:9001

# For the Chrome extension:
cd chrome-extension
npm install
npm run dev      # Development with hot reload
npm run build    # Production build → dist/
# Load unpacked extension from chrome-extension/dist/ in Chrome
```

---

> **Document Version**: 1.0
> **Last Updated**: April 2025
> **Author**: BrainVault Development Team
> **Status**: Planning Phase — Ready for repo creation
