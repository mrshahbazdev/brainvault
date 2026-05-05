<?php

namespace Tests\Feature\Api;

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_bookmarks(): void
    {
        Bookmark::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/bookmarks');

        $response->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_bookmark(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/bookmarks', [
                'url' => 'https://example.com/test',
                'title' => 'Test Bookmark',
            ]);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'Test Bookmark']);

        $this->assertDatabaseHas('bookmarks', [
            'url' => 'https://example.com/test',
            'user_id' => $this->user->id,
        ]);
    }

    public function test_can_show_bookmark(): void
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/bookmarks/{$bookmark->id}");

        $response->assertOk()
            ->assertJsonFragment(['id' => $bookmark->id]);
    }

    public function test_can_update_bookmark(): void
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/bookmarks/{$bookmark->id}", [
                'title' => 'Updated Title',
                'is_favorite' => true,
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('bookmarks', [
            'id' => $bookmark->id,
            'title' => 'Updated Title',
            'is_favorite' => true,
        ]);
    }

    public function test_can_delete_bookmark(): void
    {
        $bookmark = Bookmark::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/bookmarks/{$bookmark->id}");

        $response->assertNoContent();
        $this->assertDatabaseMissing('bookmarks', ['id' => $bookmark->id]);
    }

    public function test_unauthenticated_access_returns_401(): void
    {
        $response = $this->getJson('/api/bookmarks');
        $response->assertUnauthorized();
    }

    public function test_cannot_access_other_users_bookmark(): void
    {
        $otherUser = User::factory()->create();
        $bookmark = Bookmark::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/bookmarks/{$bookmark->id}");

        $response->assertForbidden();
    }

    public function test_can_filter_bookmarks_by_favorite(): void
    {
        Bookmark::factory()->create(['user_id' => $this->user->id, 'is_favorite' => true]);
        Bookmark::factory()->create(['user_id' => $this->user->id, 'is_favorite' => false]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/bookmarks?is_favorite=1');

        $response->assertOk();
    }

    public function test_can_search_bookmarks(): void
    {
        Bookmark::factory()->create(['user_id' => $this->user->id, 'title' => 'Laravel Documentation']);
        Bookmark::factory()->create(['user_id' => $this->user->id, 'title' => 'Vue.js Guide']);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/bookmarks?search=Laravel');

        $response->assertOk();
    }
}
