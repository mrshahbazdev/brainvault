<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookmarkFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'url' => fake()->url(),
            'title' => fake()->sentence(4),
            'description' => fake()->paragraph(),
            'site_name' => fake()->domainWord(),
            'content_type' => fake()->randomElement(['article', 'documentation', 'webpage', 'video']),
            'is_favorite' => false,
            'is_archived' => false,
            'is_read' => false,
        ];
    }
}
