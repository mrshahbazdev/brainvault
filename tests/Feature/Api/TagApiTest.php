<?php

namespace Tests\Feature\Api;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_tags(): void
    {
        Tag::factory()->count(5)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/tags');

        $response->assertOk();
    }

    public function test_can_create_tag(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/tags', [
                'name' => 'Laravel',
                'color' => '#ef4444',
            ]);

        $response->assertCreated();
        $this->assertDatabaseHas('tags', ['name' => 'Laravel', 'user_id' => $this->user->id]);
    }

    public function test_can_delete_tag(): void
    {
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/tags/{$tag->id}");

        $response->assertNoContent();
    }
}
