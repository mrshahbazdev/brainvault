<?php

namespace Database\Seeders;

use App\Models\Bookmark;
use App\Models\Collection;
use App\Models\Note;
use App\Models\ResearchProject;
use App\Models\Tag;
use App\Models\Task;
use App\Models\Team;
use App\Models\Topic;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create demo user
        $user = User::factory()->create([
            'name' => 'Shah Baz',
            'email' => 'demo@brainvault.app',
            'password' => bcrypt('password'),
            'onboarding_completed' => true,
            'plan' => 'pro',
        ]);

        // Create tags
        $tagNames = ['Laravel', 'JavaScript', 'AI', 'Design', 'DevOps', 'Database', 'Security', 'Frontend', 'Backend', 'Tutorial'];
        $tags = collect($tagNames)->map(fn ($name) => Tag::create([
            'user_id' => $user->id,
            'name' => $name,
            'slug' => \Str::slug($name),
            'color' => fake()->hexColor(),
        ]));

        // Create collections
        $collections = collect([
            ['name' => 'Web Development', 'icon' => 'code', 'color' => '#6366F1'],
            ['name' => 'AI & Machine Learning', 'icon' => 'cpu', 'color' => '#8B5CF6'],
            ['name' => 'Design Inspiration', 'icon' => 'palette', 'color' => '#EC4899'],
            ['name' => 'DevOps & Cloud', 'icon' => 'cloud', 'color' => '#06B6D4'],
            ['name' => 'Reading List', 'icon' => 'book', 'color' => '#F59E0B'],
        ])->map(fn ($data) => Collection::create([
            'user_id' => $user->id,
            'name' => $data['name'],
            'slug' => \Str::slug($data['name']),
            'icon' => $data['icon'],
            'color' => $data['color'],
        ]));

        // Create bookmarks
        $bookmarkData = [
            ['url' => 'https://laravel.com/docs', 'title' => 'Laravel Documentation', 'site_name' => 'Laravel', 'content_type' => 'documentation', 'ai_category' => 'Web Development', 'reading_time' => 15, 'word_count' => 3500],
            ['url' => 'https://vuejs.org/guide/introduction.html', 'title' => 'Vue.js Introduction', 'site_name' => 'Vue.js', 'content_type' => 'documentation', 'ai_category' => 'Frontend', 'reading_time' => 10, 'word_count' => 2400],
            ['url' => 'https://tailwindcss.com/docs/utility-first', 'title' => 'Utility-First Fundamentals', 'site_name' => 'Tailwind CSS', 'content_type' => 'documentation', 'ai_category' => 'Design', 'reading_time' => 8, 'word_count' => 1800],
            ['url' => 'https://openai.com/blog/gpt-4', 'title' => 'GPT-4 Technical Report', 'site_name' => 'OpenAI', 'content_type' => 'article', 'ai_category' => 'AI/ML', 'reading_time' => 25, 'word_count' => 6000],
            ['url' => 'https://github.com/features/copilot', 'title' => 'GitHub Copilot', 'site_name' => 'GitHub', 'content_type' => 'webpage', 'ai_category' => 'Productivity', 'reading_time' => 5, 'word_count' => 1200],
            ['url' => 'https://www.postgresql.org/docs/current/textsearch.html', 'title' => 'PostgreSQL Full Text Search', 'site_name' => 'PostgreSQL', 'content_type' => 'documentation', 'ai_category' => 'Database', 'reading_time' => 20, 'word_count' => 5000],
            ['url' => 'https://redis.io/docs/getting-started/', 'title' => 'Redis Getting Started', 'site_name' => 'Redis', 'content_type' => 'documentation', 'ai_category' => 'Database', 'reading_time' => 12, 'word_count' => 2800],
            ['url' => 'https://d3js.org/', 'title' => 'D3.js - Data-Driven Documents', 'site_name' => 'D3.js', 'content_type' => 'webpage', 'ai_category' => 'Data Science', 'reading_time' => 7, 'word_count' => 1500],
            ['url' => 'https://fly.io/docs/', 'title' => 'Fly.io Deployment Guide', 'site_name' => 'Fly.io', 'content_type' => 'documentation', 'ai_category' => 'DevOps', 'reading_time' => 15, 'word_count' => 3200],
            ['url' => 'https://livewire.laravel.com/docs/quickstart', 'title' => 'Livewire Quickstart', 'site_name' => 'Livewire', 'content_type' => 'documentation', 'ai_category' => 'Web Development', 'reading_time' => 10, 'word_count' => 2200],
            ['url' => 'https://www.figma.com/best-practices/', 'title' => 'Figma Best Practices', 'site_name' => 'Figma', 'content_type' => 'article', 'ai_category' => 'Design', 'reading_time' => 8, 'word_count' => 1900],
            ['url' => 'https://developer.chrome.com/docs/extensions/mv3/', 'title' => 'Chrome Extension Manifest V3', 'site_name' => 'Chrome Developers', 'content_type' => 'documentation', 'ai_category' => 'Web Development', 'reading_time' => 18, 'word_count' => 4200],
        ];

        $bookmarks = collect();
        foreach ($bookmarkData as $i => $data) {
            $bookmark = Bookmark::create(array_merge($data, [
                'user_id' => $user->id,
                'description' => fake()->paragraph(),
                'ai_summary' => fake()->sentences(2, true),
                'ai_keywords' => fake()->words(5),
                'is_favorite' => $i < 3,
                'is_read' => $i % 2 === 0,
                'read_at' => $i % 2 === 0 ? now()->subDays(rand(1, 30)) : null,
                'created_at' => now()->subDays(rand(0, 60)),
            ]));

            // Attach to random collection
            $bookmark->collections()->attach($collections->random()->id);

            // Attach random tags
            $bookmark->tags()->attach($tags->random(rand(1, 3))->pluck('id'));

            $bookmarks->push($bookmark);
        }

        // Create notes
        $noteData = [
            ['title' => 'Laravel Tips & Tricks', 'content' => '<h2>Useful Laravel Patterns</h2><p>Collection of tips for Laravel development including query optimization, service patterns, and testing strategies.</p><ul><li>Use eager loading to avoid N+1</li><li>Repository pattern for complex queries</li><li>Feature tests over unit tests</li></ul>'],
            ['title' => 'AI Prompt Engineering', 'content' => '<h2>Effective Prompts</h2><p>Notes on writing better prompts for GPT-4 and other LLMs.</p><ul><li>Be specific about output format</li><li>Provide examples (few-shot)</li><li>Chain of thought reasoning</li></ul>'],
            ['title' => 'Meeting Notes - Sprint Planning', 'content' => '<p>Sprint 12 planning session. Focus areas: API performance, search improvements, extension bug fixes.</p><h3>Action Items</h3><ul><li>Optimize bookmark indexing pipeline</li><li>Add rate limiting to API</li><li>Fix highlight sync issue in Chrome extension</li></ul>'],
            ['title' => 'System Design Principles', 'content' => '<h2>Key Principles</h2><ul><li>Keep it simple (KISS)</li><li>Separation of concerns</li><li>DRY but not at the cost of clarity</li><li>Design for failure</li></ul><blockquote>Premature optimization is the root of all evil - Knuth</blockquote>'],
            ['title' => 'Database Optimization Notes', 'content' => '<h2>PostgreSQL Performance</h2><p>Key techniques for optimizing PostgreSQL queries.</p><ul><li>Use EXPLAIN ANALYZE for query profiling</li><li>Composite indexes for multi-column queries</li><li>Partial indexes for filtered queries</li><li>Connection pooling with PgBouncer</li></ul>'],
            ['title' => 'Reading List - Q2 2025', 'content' => '<h2>Books to Read</h2><ul><li>Designing Data-Intensive Applications</li><li>The Pragmatic Programmer</li><li>Clean Architecture</li></ul><h2>Articles</h2><ul><li>Scaling PostgreSQL to 1M connections</li><li>Vector search at scale</li></ul>'],
        ];

        foreach ($noteData as $i => $data) {
            Note::create([
                'user_id' => $user->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'content_plain' => strip_tags($data['content']),
                'note_type' => 'note',
                'is_pinned' => $i < 2,
                'word_count' => str_word_count(strip_tags($data['content'])),
                'created_at' => now()->subDays(rand(0, 30)),
            ]);
        }

        // Create topics
        $topicNames = ['Machine Learning', 'Web Frameworks', 'Cloud Infrastructure', 'UI/UX Design', 'Data Engineering', 'Security'];
        foreach ($topicNames as $name) {
            Topic::create([
                'user_id' => $user->id,
                'name' => $name,
                'slug' => \Str::slug($name),
                'ai_generated' => true,
                'color' => fake()->hexColor(),
                'item_count' => rand(3, 15),
            ]);
        }

        // Create a team
        $team = Team::create([
            'name' => 'BrainVault Dev Team',
            'slug' => 'brainvault-dev',
            'description' => 'Core development team for BrainVault platform',
            'owner_id' => $user->id,
        ]);
        $team->members()->attach($user->id, ['role' => 'owner', 'joined_at' => now()]);

        // Create research project
        $project = ResearchProject::create([
            'user_id' => $user->id,
            'title' => 'AI-Powered Search Research',
            'slug' => 'ai-search-research',
            'description' => 'Research into semantic search, vector databases, and RAG pipelines',
            'status' => 'active',
        ]);

        // Add tasks
        $tasks = [
            ['title' => 'Compare pgvector vs Pinecone', 'status' => 'done', 'priority' => 'high'],
            ['title' => 'Benchmark embedding models', 'status' => 'done', 'priority' => 'high'],
            ['title' => 'Implement hybrid search', 'status' => 'in_progress', 'priority' => 'high'],
            ['title' => 'RAG pipeline evaluation', 'status' => 'in_progress', 'priority' => 'medium'],
            ['title' => 'User testing for search relevance', 'status' => 'todo', 'priority' => 'medium'],
            ['title' => 'Write documentation', 'status' => 'todo', 'priority' => 'low'],
        ];

        foreach ($tasks as $task) {
            Task::create([
                'user_id' => $user->id,
                'research_project_id' => $project->id,
                'title' => $task['title'],
                'status' => $task['status'],
                'priority' => $task['priority'],
                'completed_at' => $task['status'] === 'done' ? now()->subDays(rand(1, 10)) : null,
            ]);
        }
    }
}
