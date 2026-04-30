<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class NoteFactory extends Factory
{
    public function definition(): array
    {
        $content = '<p>' . fake()->paragraph() . '</p>';

        return [
            'user_id' => User::factory(),
            'title' => fake()->sentence(3),
            'content' => $content,
            'content_plain' => strip_tags($content),
            'note_type' => 'note',
            'is_pinned' => false,
            'is_archived' => false,
            'is_trashed' => false,
        ];
    }
}
