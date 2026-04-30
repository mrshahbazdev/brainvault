<?php

namespace Tests\Feature\Api;

use App\Models\Collection;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CollectionApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_collections(): void
    {
        Collection::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/collections');

        $response->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_collection(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/collections', [
                'name' => 'My Collection',
                'color' => '#6366f1',
            ]);

        $response->assertCreated()
            ->assertJsonFragment(['name' => 'My Collection']);
    }

    public function test_can_update_collection(): void
    {
        $collection = Collection::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/collections/{$collection->id}", [
                'name' => 'Updated Name',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('collections', ['id' => $collection->id, 'name' => 'Updated Name']);
    }

    public function test_can_delete_collection(): void
    {
        $collection = Collection::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/collections/{$collection->id}");

        $response->assertNoContent();
    }
}
