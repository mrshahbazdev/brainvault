<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_search_requires_query(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/search');

        $response->assertUnprocessable();
    }

    public function test_search_returns_results(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/search?query=test');

        $response->assertOk()
            ->assertJsonStructure(['bookmarks', 'notes']);
    }

    public function test_ask_requires_question(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/ask');

        $response->assertUnprocessable();
    }

    public function test_search_requires_authentication(): void
    {
        $response = $this->getJson('/api/search?query=test');
        $response->assertUnauthorized();
    }
}
