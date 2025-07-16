<?php

namespace Database\Factories;

use App\Models\Attendee;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Review>
 */
class ReviewFactory extends Factory
{
    protected static $uniquePairs;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Not used because it is repetition forever if the while value is never false (infinite loop)
        // $attendee = Attendee::inRandomOrder()->first();

        // while (Review::where('user_id', $attendee->user_id)
        //     ->where('event_id', $attendee->event_id)->exists()
        // ) {
        //     $attendee = Attendee::inRandomOrder()->first();
        // };

        // return [
        //     'user_id' => $attendee->user_id,
        //     'event_id' => $attendee->event_id,
        //     'rating' => fake()->numberBetween(1, 5),
        //     'comment' => fake()->boolean(80) ? fake()->sentence : null
        // ];

        if (is_null(self::$uniquePairs)) {
            $allPairs = Attendee::select('user_id', 'event_id')->get();
            self::$uniquePairs = $allPairs->shuffle();
        }

        $pair = self::$uniquePairs->pop();
        if (is_null($pair)) {
            throw new Exception("No more unique combinations are available for Review.");
        }

        return [
            'user_id' => $pair['user_id'],
            'event_id' => $pair['event_id'],
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->boolean(80) ? fake()->sentence : null
        ];
    }
}
