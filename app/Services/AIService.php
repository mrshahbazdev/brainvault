<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AIService
{
    protected string $apiKey;
    protected string $model;
    protected string $embeddingModel;

    public function __construct()
    {
        $this->apiKey = config('services.openai.key', '');
        $this->model = config('services.openai.model', 'gpt-4o-mini');
        $this->embeddingModel = config('services.openai.embedding_model', 'text-embedding-3-small');
    }

    public function isConfigured(): bool
    {
        return !empty($this->apiKey);
    }

    public function generateSummary(string $title, string $content, ?string $url = null): ?string
    {
        $prompt = "Summarize this web page in 2-3 concise sentences. Focus on the key point/value.\n\n";
        $prompt .= "Title: {$title}\n";
        if ($url) {
            $prompt .= "URL: {$url}\n";
        }
        $prompt .= "Content: " . mb_substr($content, 0, 4000);

        return $this->chat($prompt);
    }

    public function extractKeywords(string $title, string $content): array
    {
        $prompt = "Extract 3-8 relevant keywords/tags from this content. Return ONLY a JSON array of lowercase strings, nothing else.\n\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Content: " . mb_substr($content, 0, 3000);

        $response = $this->chat($prompt);
        if (!$response) {
            return [];
        }

        $cleaned = trim($response, "```json \n");
        $keywords = json_decode($cleaned, true);

        return is_array($keywords) ? array_slice($keywords, 0, 10) : [];
    }

    public function suggestCategory(string $title, string $content): ?string
    {
        $categories = [
            'Technology', 'Programming', 'Design', 'Business',
            'Science', 'Health', 'Education', 'News',
            'Entertainment', 'Finance', 'Marketing', 'Productivity',
            'AI/ML', 'DevOps', 'Security', 'Data Science',
            'Web Development', 'Mobile', 'Open Source', 'Career',
            'Research', 'Tutorial', 'Documentation', 'Other',
        ];

        $categoryList = implode(', ', $categories);
        $prompt = "Classify this content into ONE category from: {$categoryList}\n\n";
        $prompt .= "Return ONLY the category name, nothing else.\n\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Content: " . mb_substr($content, 0, 2000);

        $response = $this->chat($prompt);

        return $response ? trim($response) : null;
    }

    public function enhanceNote(string $content): ?string
    {
        $prompt = "Improve and organize this note. Fix grammar, add structure with headers if needed, and make it clearer. Keep the same meaning.\n\n";
        $prompt .= $content;

        return $this->chat($prompt);
    }

    public function generateEmbedding(string $text): ?array
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(30)
                ->post('https://api.openai.com/v1/embeddings', [
                    'model' => $this->embeddingModel,
                    'input' => mb_substr($text, 0, 8000),
                ]);

            if ($response->successful()) {
                return $response->json('data.0.embedding');
            }

            Log::warning('OpenAI embedding failed', ['status' => $response->status()]);
            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI embedding error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function askKnowledgeBase(string $question, array $context): ?string
    {
        $contextText = collect($context)->map(function ($item) {
            return "---\nTitle: {$item['title']}\nContent: {$item['content']}\n";
        })->implode("\n");

        $prompt = "You are a helpful research assistant. Answer the user's question based ONLY on the provided context from their saved bookmarks and notes. If the context doesn't contain enough information, say so honestly.\n\n";
        $prompt .= "Context:\n{$contextText}\n\n";
        $prompt .= "Question: {$question}";

        return $this->chat($prompt, 'gpt-4o');
    }

    public function suggestRelatedTopics(string $title, string $content): array
    {
        $prompt = "Based on this content, suggest 2-4 related research topics the reader might want to explore. Return a JSON array of topic name strings.\n\n";
        $prompt .= "Title: {$title}\n";
        $prompt .= "Content: " . mb_substr($content, 0, 2000);

        $response = $this->chat($prompt);
        if (!$response) {
            return [];
        }

        $cleaned = trim($response, "```json \n");
        $topics = json_decode($cleaned, true);

        return is_array($topics) ? array_slice($topics, 0, 5) : [];
    }

    protected function chat(string $prompt, ?string $model = null): ?string
    {
        if (!$this->isConfigured()) {
            return null;
        }

        try {
            $response = Http::withToken($this->apiKey)
                ->timeout(60)
                ->post('https://api.openai.com/v1/chat/completions', [
                    'model' => $model ?? $this->model,
                    'messages' => [
                        ['role' => 'user', 'content' => $prompt],
                    ],
                    'temperature' => 0.3,
                    'max_tokens' => 500,
                ]);

            if ($response->successful()) {
                return $response->json('choices.0.message.content');
            }

            Log::warning('OpenAI chat failed', [
                'status' => $response->status(),
                'body' => $response->body(),
            ]);
            return null;
        } catch (\Exception $e) {
            Log::error('OpenAI chat error', ['error' => $e->getMessage()]);
            return null;
        }
    }

    public function enhanceNote(string $title, string $content, string $instruction = 'improve'): ?string
    {
        $prompts = [
            'improve' => 'Improve the following note by fixing grammar, enhancing clarity, and adding structure. Keep the same meaning and tone.',
            'expand' => 'Expand the following note with more details, examples, and explanations. Keep the original content and add to it.',
            'summarize' => 'Create a concise summary of the following note, highlighting key points in bullet points.',
            'format' => 'Reformat the following note with proper headings, bullet points, and structure. Return as HTML.',
        ];

        $systemPrompt = $prompts[$instruction] ?? $prompts['improve'];

        return $this->chat($systemPrompt, "Title: {$title}\n\nContent: {$content}");
    }
}
