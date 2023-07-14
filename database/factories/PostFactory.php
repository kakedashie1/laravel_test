<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => Post::OPEN,
            'title' => $this->faker->realText(20),
            'body' => $this->faker->realText(200),
        ];
    }

    public function random()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => $this->faker->randomElement([1,1,1,1,0]),
            ];
        });
    }

    public function closed()
    {
        return $this->state(function (array $attributes) {
            return [
                'status' => Post::CLOSED,
            ];
        });
    }
}
