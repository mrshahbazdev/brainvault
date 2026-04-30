<?php

namespace Tests\Feature\Api;

use App\Models\Note;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NoteApiTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    public function test_can_list_notes(): void
    {
        Note::factory()->count(3)->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson('/api/notes');

        $response->assertOk()
            ->assertJsonStructure(['data']);
    }

    public function test_can_create_note(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
            ->postJson('/api/notes', [
                'title' => 'Test Note',
                'content' => '<p>Note content</p>',
                'note_type' => 'note',
            ]);

        $response->assertCreated()
            ->assertJsonFragment(['title' => 'Test Note']);
    }

    public function test_can_show_note(): void
    {
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->getJson("/api/notes/{$note->id}");

        $response->assertOk();
    }

    public function test_can_update_note(): void
    {
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->putJson("/api/notes/{$note->id}", [
                'title' => 'Updated Note',
            ]);

        $response->assertOk();
        $this->assertDatabaseHas('notes', ['id' => $note->id, 'title' => 'Updated Note']);
    }

    public function test_can_delete_note(): void
    {
        $note = Note::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
            ->deleteJson("/api/notes/{$note->id}");

        $response->assertNoContent();
    }
}
