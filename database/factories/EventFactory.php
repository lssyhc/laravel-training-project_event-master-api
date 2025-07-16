<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $startTime = fake()->dateTimeBetween('now', '+1 month');
        $endTime = fake()->dateTimeBetween($startTime, (clone $startTime)->modify('+3 days'));

        return [
            'user_id' => User::where('role', 'organizer')->inRandomOrder()->first()->id,
            'name' => fake()->unique()->sentence(3),
            'description' => fake()->text,
            'start_time' => $startTime,
            'end_time' => $endTime
        ];
    }
}
